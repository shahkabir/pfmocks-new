<?php

namespace App\Http\Controllers;

use App\Constants\ModuleConstants;
use App\Models\Exam\Exam;
use App\Models\Module\Module;
use App\Models\Sop\SopSubmission;
use App\Services\SopService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SopController extends Controller
{
    public function __construct(private SopService $sop) {}

    /** Student-facing services landing page. */
    public function index()
    {
        $sopExam = Exam::with(['modules' => function ($q) {
                $q->whereIn('module_type', ModuleConstants::SOP_TYPES);
            }])
            ->where('tag', 'sop')
            ->where('is_active', true)
            ->first();

        $modules = $sopExam?->modules ?? collect();

        // Existing submissions for this user (so we can show "View final SOP" links etc.)
        $userId = auth()->id();
        $submissions = SopSubmission::with(['module', 'payment', 'userExam'])
            ->where('user_id', $userId)
            ->orderByDesc('id')
            ->get();

        return view('student.services.sop', [
            'sopExam'     => $sopExam,
            'modules'     => $modules,
            'submissions' => $submissions,
            'payeeMsisdn' => config('payment.bkash_msisdn', '01962424219'),
        ]);
    }

    /** AJAX: tiny JSON payload used by the purchase modal. */
    public function moduleInfo(int $moduleId): JsonResponse
    {
        $module = Module::with('exam:id,name')->findOrFail($moduleId);

        if (!in_array($module->module_type, ModuleConstants::SOP_TYPES, true)) {
            abort(404);
        }

        return response()->json([
            'module_id'    => $module->id,
            'module_name'  => $module->name,
            'module_type'  => $module->module_type,
            'service_type' => $module->module_type === 'sop_review' ? 'review' : 'new',
            'price_bdt'    => (float) $module->price_in_bdt,
            'payee_msisdn' => config('payment.bkash_msisdn', '01962424219'),
        ]);
    }

    /** Form submission: SOP details + payment trxId. */
    public function submit(Request $request): JsonResponse
    {
        try {
            $submission = $this->sop->submit(
                userId:      auth()->id(),
                data:        $request->all(),
                resume:      $request->file('resume'),
                originalSop: $request->file('original_sop'),
            );
        } catch (ValidationException $e) {
            return response()->json([
                'ok'      => false,
                'message' => $e->validator->errors()->first(),
                'errors'  => $e->errors(),
            ], 422);
        }

        return response()->json([
            'ok'      => true,
            'message' => 'SOP request submitted. Admin will verify your payment shortly.',
            'submission_id' => $submission->id,
        ]);
    }

    /**
     * Authenticated download for any SOP-related file (resume / original / final).
     * The student gets their own files; admin & assigned evaluator can also download.
     */
    public function download(int $submissionId, string $type): BinaryFileResponse
    {
        $submission = SopSubmission::findOrFail($submissionId);
        $user       = auth()->user();

        $allowed = $user->id === $submission->user_id
            || $user->role === 'admin'
            || ($user->role === 'evaluator' && $this->isAssignedEvaluator($submission, $user->id));

        abort_unless($allowed, 403);

        $path = match ($type) {
            'resume'        => $submission->resume_path,
            'original-sop'  => $submission->original_sop_path,
            'final-sop'     => $submission->final_sop_path,
            default         => null,
        };

        abort_unless($path, 404);

        $abs = public_path($path);
        abort_unless(file_exists($abs), 404);

        return response()->download($abs);
    }

    private function isAssignedEvaluator(SopSubmission $submission, int $evaluatorId): bool
    {
        $attempt = \App\Models\Exam\ExamAttempt::where('user_id', $submission->user_id)
            ->where('module_id', $submission->module_id)
            ->orderByDesc('id')
            ->first();
        if (!$attempt) return false;

        return \App\Models\Evaluation\Evaluation::where('exam_attempt_id', $attempt->id)
            ->where('assigned_to', $evaluatorId)
            ->exists();
    }
}
