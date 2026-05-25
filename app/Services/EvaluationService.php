<?php

namespace App\Services;

use App\Mail\EvaluationAssignedMail;
use App\Mail\EvaluationCompletedMail;
use App\Models\Answer\Results;
use App\Models\Evaluation\Evaluation;
use App\Models\Exam\ExamAttempt;
use App\Models\User;
use App\Services\Mail\Mailer;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EvaluationService
{
    public function __construct(private Mailer $mailer) {}

    /* ========================================================================
     | Assignment (admin)
     |========================================================================*/

    /**
     * Admin assigns a finished writing/speaking attempt to an evaluator.
     * Sends notification emails to both evaluator and student via the common Mailer.
     */
    public function assign(int $examAttemptId, int $evaluatorUserId, int $adminUserId): Evaluation
    {
        return DB::transaction(function () use ($examAttemptId, $evaluatorUserId, $adminUserId) {
            $attempt = ExamAttempt::with(['user:id,name,email', 'module:id,name,module_type', 'module.exam:id,name'])
                ->findOrFail($examAttemptId);

            if (!in_array($attempt->module->module_type, Evaluation::EVALUATABLE_MODULE_TYPES, true)) {
                throw new \RuntimeException("Module type '{$attempt->module->module_type}' is not evaluatable.");
            }

            $evaluator = User::findOrFail($evaluatorUserId);
            if ($evaluator->role !== 'evaluator') {
                throw new \RuntimeException("User #{$evaluatorUserId} is not an evaluator.");
            }

            // One evaluation per attempt — re-assignment overwrites the assignee
            $evaluation = Evaluation::updateOrCreate(
                ['exam_attempt_id' => $attempt->id],
                [
                    'assigned_to'  => $evaluator->id,
                    'assigned_by'  => $adminUserId,
                    'module_type'  => $attempt->module->module_type,
                    'status'       => Evaluation::STATUS_ASSIGNED,
                    'assigned_at'  => now(),
                ]
            );

            // Update Results.status so the student knows it's under review
            Results::where('exam_attempt_id', $attempt->id)
                ->update(['status' => 'manual_review']);

            $student   = $attempt->user;
            $module    = $attempt->module;
            $examName  = $module->exam?->name ?? '';
            // Friendly label — "SOP Review" / "Personalized SOP Writing" / "Writing" / "Speaking"
            $moduleNm  = \App\Constants\ModuleConstants::MODULES[$module->module_type]
                      ?? ucfirst($module->module_type);

            // Email evaluator
            $this->mailer->send($evaluator->email, new EvaluationAssignedMail(
                evaluation:    $evaluation,
                recipientType: 'evaluator',
                studentName:   $student->name,
                evaluatorName: $evaluator->name,
                examName:      $examName,
                moduleName:    $moduleNm,
            ));

            // Email student
            $this->mailer->send($student->email, new EvaluationAssignedMail(
                evaluation:    $evaluation,
                recipientType: 'student',
                studentName:   $student->name,
                evaluatorName: $evaluator->name,
                examName:      $examName,
                moduleName:    $moduleNm,
            ));

            return $evaluation->fresh();
        });
    }

    /* ========================================================================
     | Feedback submission (evaluator)
     |========================================================================*/

    /**
     * Evaluator submits typed feedback + optional voice recording + IELTS criteria scores.
     * For SOP modules, also accepts a final SOP file (the deliverable).
     */
    public function submitFeedback(
        int    $evaluationId,
        array  $data,
        ?UploadedFile $audio    = null,
        ?UploadedFile $finalSop = null
    ): Evaluation {
        return DB::transaction(function () use ($evaluationId, $data, $audio, $finalSop) {
            /** @var Evaluation $evaluation */
            $evaluation = Evaluation::with(['attempt.user', 'attempt.module.exam'])
                ->findOrFail($evaluationId);

            $audioPath = $evaluation->feedback_audio_path;

            if ($audio) {
                // Remove the previous audio if any
                if ($audioPath && file_exists(public_path($audioPath))) {
                    @unlink(public_path($audioPath));
                }
                $filename = uniqid('feedback_') . '_' . Str::random(6) . '.webm';
                $audio->move(public_path('data/audio/feedback'), $filename);
                $audioPath = 'data/audio/feedback/' . $filename;
            }

            // SOP final deliverable: store the new SOP file on the linked SopSubmission
            if ($finalSop) {
                $student = $evaluation->attempt->user;
                $module  = $evaluation->attempt->module;
                $submission = \App\Models\Sop\SopSubmission::where('user_id', $student->id)
                    ->where('module_id', $module->id)
                    ->latest('id')
                    ->first();
                if ($submission) {
                    if ($submission->final_sop_path && file_exists(public_path($submission->final_sop_path))) {
                        @unlink(public_path($submission->final_sop_path));
                    }
                    $sopDir  = "data/sop/{$student->id}";
                    $sopName = uniqid('final_sop_') . '_' . Str::random(6) . '.' . $finalSop->getClientOriginalExtension();
                    $finalSop->move(public_path($sopDir), $sopName);
                    $submission->update(['final_sop_path' => "{$sopDir}/{$sopName}"]);
                }
            }

            // Build band scores map (numeric only)
            $bandScores = [];
            foreach ([
                'task_achievement', 'coherence_cohesion', 'lexical_resource',
                'grammatical_range', 'fluency_coherence', 'pronunciation', 'overall',
            ] as $criterion) {
                if (isset($data[$criterion]) && is_numeric($data[$criterion])) {
                    $bandScores[$criterion] = (float) $data[$criterion];
                }
            }

            $evaluation->update([
                'status'              => Evaluation::STATUS_COMPLETED,
                'started_at'          => $evaluation->started_at ?? now(),
                'completed_at'        => now(),
                'band_scores'         => $bandScores,
                'feedback_text'       => $data['feedback_text'] ?? null,
                'feedback_audio_path' => $audioPath,
            ]);

            // Persist the overall band on Results too, for quick access on the student dashboard modal
            $overall = $evaluation->fresh()->overallBand();
            Results::where('exam_attempt_id', $evaluation->exam_attempt_id)->update([
                'status'             => 'evaluated',
                'band_score'         => $overall,
                'evaluator_feedback' => $data['feedback_text'] ?? null,
                'evaluated_by'       => $evaluation->assigned_to,
            ]);

            // Notify student
            $student   = $evaluation->attempt->user;
            $module    = $evaluation->attempt->module;
            $examName  = $module?->exam?->name ?? '';
            $moduleNm  = $module
                ? (\App\Constants\ModuleConstants::MODULES[$module->module_type] ?? ucfirst($module->module_type))
                : 'Exam';

            // Find UserExam id for the result link
            $userExam = \App\Models\Exam\UserExam::where('user_id', $student->id)
                ->where('module_id', $module->id)
                ->latest('purchased_at')
                ->first();

            $resultUrl = $userExam
                ? url(route('exam.result', $userExam->id, false))
                : url('/dashboard');

            $this->mailer->send($student->email, new EvaluationCompletedMail(
                evaluation:  $evaluation->fresh(),
                studentName: $student->name,
                examName:    $examName,
                moduleName:  $moduleNm,
                resultUrl:   $resultUrl,
            ));

            return $evaluation->fresh();
        });
    }

    /** Mark "in progress" the first time the evaluator opens the page. */
    public function markInProgress(Evaluation $evaluation): void
    {
        if ($evaluation->status === Evaluation::STATUS_ASSIGNED) {
            $evaluation->update([
                'status'     => Evaluation::STATUS_IN_PROGRESS,
                'started_at' => now(),
            ]);
        }
    }
}
