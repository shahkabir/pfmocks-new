<?php

namespace App\Http\Controllers;

use App\Constants\ModuleConstants;
use Illuminate\Http\Request;
use App\Models\Answer\Answer;
use App\Models\Exam\UserExam;
use App\Models\Module\Module;
use App\Models\Exam\ExamAttempt;
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
        //dd($user);
        //dd($moduleId);

        $userExam = UserExam::where('user_id', $user->id)
            ->where('module_id', $moduleId)
            ->get();
            //->exists();

        //dd($userExam);

        // if (!$userExam) {
        //     abort(403, 'You do not have access to this exam.');
        // }

        //Get the Module Details
        $module = Module::findOrFail($moduleId);
                  // ->get();
                 //->toArray();
         
        //dd($module);

        $duration = $module->duration_minutes;
        $moduleType = $module->module_type;
        //dd($module->module_type);

        //Get the questons for the module
        // $questions = Question::with('options')
        //     ->join('modules', 'questions.module_id', '=', 'modules.id')
        //     ->where('modules.module_type', $moduleType)
        //     //->where('module_id', $moduleId)
        //     ->orderBy('sort_order', 'asc')
        //     ->get()
        //     ->toArray();

        $questions = Question::with(['options','group.blocks'])
        ->leftJoin('modules', 'questions.module_id', '=', 'modules.id')
        ->leftJoin('question_groups', 'questions.id', '=', 'question_groups.question_id')
        ->where('modules.module_type', $moduleType)
        ->orderBy('questions.sort_order')
        ->select([
            'questions.*',
            'question_groups.question_options_group_ids',
            'question_groups.part_number',
            ])
        ->get()
        ->toArray();


        //dd($questions);

        $showFeedback = false;

        //if(ModuleConstants::MODULES['writing'])
        if(($moduleType === 'writing')){
            return view('exams.ielts.writing', compact('questions', 'user', 'showFeedback', 'module'));
        }else if(($moduleType === 'listening')){
            return view('exams.ielts.listening', compact('questions', 'user', 'showFeedback', 'module'));
        }else if(($moduleType === 'reading')){
            return view('exams.ielts.reading', compact('questions', 'user', 'showFeedback', 'module'));
        }else if(($moduleType === 'speaking')){
            return view('exams.ielts.speaking', compact('questions', 'user', 'showFeedback', 'module'));
        }else{
            abort(404, 'Module type not found.');
        }

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

    public function submitIELTSReading(Request $request)
    {
        dd($request->all());

        $user = auth()->user();
        $answers = $request->input('answers', []);

        //Update Exam Attempt
        $examAttempt = ExamAttempt::updateOrCreate(
            [
                'user_id' => $user->id,
                'module_id' => 2, // IELTS Reading module ID
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
                            'question_option_id' => $data['question_option_id'] ?? null,
                        ],
                        [
                            'answer' => $data['answer'],
                            'is_correct' => $data['is_correct'] ?? false,
                        ]
                    );
        }

        return response()->json([
            'message' => 'Reading answers submitted successfully!',
            //'answers' => $answers,
        ]);
    }
}
