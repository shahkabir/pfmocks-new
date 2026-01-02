<?php

namespace App\Http\Controllers;

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
            ->get()
            ->toArray();

        //dd($questions);

        return view('exams.ielts.writing', compact('questions'));

        // Later: create exam_attempt here
        //return view('student.exam-start', compact('moduleId'));
    }
}
