<?php

namespace App\Http\Controllers;

use App\Models\Answer\Answer;
use App\Models\Exam\ExamAttempt;
use Illuminate\Http\Request;
use App\Models\Exam\UserExam;
use App\Models\Question\Question;

class ExamController extends Controller
{
     /**
     * Show student dashboard exams (free + paid)
     */
    public function dashboard()
    {
        $user = auth()->user();

        $exams = UserExam::with(['module.exam'])
            ->where('user_id', $user->id)
            ->orderByDesc('purchased_at')
            ->get();

        return view('student.dashboard', compact('exams'));
    }

    /**
     * Start an exam module for all IELTS, PTE, TOEFL, GRE
     */
    public function start($moduleId)
    {
        $user = auth()->user();

        $hasAccess = UserExam::where('user_id', $user->id)
            ->where('module_id', $moduleId)
            ->exists();

        //dd($hasAccess);

        if (!$hasAccess) {
            abort(403, 'You do not have access to this exam.');
        }

        //Get the questons for the module
        $questions = Question::with('options')
            ->where('module_id', $moduleId)
            ->orderBy('sort_order', 'asc')
            ->get()
            ->toArray();

        //dd($questions);

        $showFeedback = false;

        return view('exams.ielts.writing', compact('questions', 'user', 'showFeedback'));

        // Later: create exam_attempt here
        //return view('student.exam-start', compact('moduleId'));
    }

    public function submitIELTSWriting(Request $request)
    {
        //dd($request->all());

        $user = auth()->user();
        $answers = $request->input('answers', []);

        //Update Exam Attempt
        $examAttempt = ExamAttempt::updateOrCreate(
            [
                'user_id' => $user->id,
                'module_id' => 1, // IELTS Writing module ID
                'started_at' => now(),
                'status' => 'completed',
            ],
            [
                'ended_at' => now(),
            ]
        );

        foreach ($answers as $questionId => $data) {
                Answer::updateOrCreate(
                        [
                            'user_id' => $user->id,
                            'exam_attempt_id' => $examAttempt->id,
                            'question_id' => $questionId,
                            'is_correct' => null, // Writing answers are not auto-graded
                        ],
                        [
                            'answer' => $data['answer'],
                        ]
                    );
        }

        // Here you can process the answers, save them to the database, etc.
        // For demonstration, we'll just return a success message.

        return response()->json([
            'message' => 'Writing answers submitted successfully!',
            //'answers' => $answers,
        ]);
    }
}
