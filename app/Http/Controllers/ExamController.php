<?php

namespace App\Http\Controllers;

use App\Constants\ModuleConstants;
use App\Models\Answer\Answer;
use App\Models\Answer\Results;
use App\Models\Exam\Exam;
use App\Models\Exam\ExamAttempt;
use App\Models\Exam\UserExam;
use App\Models\Module\Module;
use App\Models\Question\Question;
use App\Models\Question\QuestionOptions;
use Illuminate\Http\Request;

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
            // ->where('exam.is_active', true)
            ->orderByDesc('purchased_at')
            ->get();
        //dd($exams);

        // dd($user);
        //Show all exams for admin too for testing (later we can have a separate admin dashboard)
        // if($user->role === 'admin'){
        //     $exams = Exam::with('modules.exam')
        //         ->where('is_active', true)
        //         ->orderByDesc('created_at')
        //         ->get();
        // }

        // dd($exams);

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
        // dd($module->module_type);

        // Get the questons for the module
        // Works for Writing module
        $questions = Question::with('options')
            ->join('modules', 'questions.module_id', '=', 'modules.id')
            ->where('modules.module_type', $moduleType)
            ->where('modules.id', $moduleId)
            ->orderBy('sort_order', 'asc')
            ->get()
            ->toArray();

        // Works for Listening and Reading module with question groups and blocks
        
        // $questions = Question::with(['options','group.blocks'])
        // ->leftJoin('modules', 'questions.module_id', '=', 'modules.id')
        // ->leftJoin('question_groups', 'questions.id', '=', 'question_groups.question_id')
        // ->where('modules.module_type', $moduleType)
        // ->where('modules.id', $moduleId)
        // ->active()
        // ->orderBy('questions.sort_order')
        // ->select([
        //     'questions.*',
        //     'question_groups.question_options_group_ids',
        //     'question_groups.part_number',
        //     'question_groups.part_audio_url',
        //     'question_groups.part_image_url'
        //     ])
        // ->get()
        // ->toArray();

        // dd($questions, $moduleType);

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
        }else if(($moduleType === 'general_mcq')){
            return view('exams.general.mcq', compact('questions', 'user', 'showFeedback', 'module'));
        }else{
            abort(404, 'Module type not found.');
        }

        // Later: create exam_attempt here
        //return view('student.exam-start', compact('moduleId'));
    }

    /**
     * Show a completed exam in review mode — pre-populates the user's
     * previous answers and auto-enters the review view.
     *
     * Reuses the same reading/listening/writing/speaking blades
     * that `start()` renders, but passes extra flags.
     */
    public function showResult($userExamId)
    {
        $user = auth()->user();

        $userExam = UserExam::where('id', $userExamId)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $module     = Module::findOrFail($userExam->module_id);
        $moduleType = $module->module_type;

        // Latest completed attempt for this user + module
        $examAttempt = ExamAttempt::where('user_id', $user->id)
            ->where('module_id', $module->id)
            ->where('status', 'completed')
            ->latest('ended_at')
            ->firstOrFail();

        $result = Results::where('exam_attempt_id', $examAttempt->id)->first();

        // Load user's previous answers for this attempt.
        // Build two lookups for the view:
        //   $userSelectedOptionIds : flat array of option_id the user selected (MCQ)
        //   $userFillAnswers       : map of option_id => typed text (fill_in_blanks)
        $previousAnswers = Answer::where('exam_attempt_id', $examAttempt->id)->get();

        $userSelectedOptionIds = $previousAnswers
            ->pluck('question_option_id')
            ->filter()
            ->map(fn($id) => (int) $id)
            ->values()
            ->all();

        $userFillAnswers = [];
        foreach ($previousAnswers as $a) {
            if (!empty($a->answer) && $a->question_option_id) {
                $userFillAnswers[(int) $a->question_option_id] = $a->answer;
            }
        }

        // Same question load as start()
        $questions = Question::with(['options', 'group.blocks'])
            ->leftJoin('modules', 'questions.module_id', '=', 'modules.id')
            ->leftJoin('question_groups', 'questions.id', '=', 'question_groups.question_id')
            ->where('modules.module_type', $moduleType)
            ->where('modules.id', $module->id)
            ->active()
            ->orderBy('questions.sort_order')
            ->select([
                'questions.*',
                'question_groups.question_options_group_ids',
                'question_groups.part_number',
                'question_groups.part_audio_url',
                'question_groups.part_image_url',
            ])
            ->get()
            ->toArray();

        $showFeedback = false;
        $reviewMode   = true;

        $summary = [
            'exam_name'            => $result?->exam_name ?? $userExam->module->exam?->name ?? '',
            'module_name'          => $result?->module_name ?? (ModuleConstants::MODULES[$moduleType] ?? $moduleType),
            'total_questions'      => (int) ($result?->total_score ?? 0),
            'correct_answers'      => (int) ($result?->achieved_score ?? 0),
            'score_percentage'     => (float) ($result?->score_percentage ?? 0),
            'band_score'           => (float) ($result?->band_score ?? 0),
            'time_elapsed_seconds' => (int) ($result?->time_taken_seconds ?? 0),
        ];

        // Load evaluator feedback if any (writing/speaking)
        $evaluation = \App\Models\Evaluation\Evaluation::with('evaluator:id,name')
            ->where('exam_attempt_id', $examAttempt->id)
            ->first();

        if ($evaluation && $evaluation->isCompleted()) {
            $summary['evaluator_feedback']  = $evaluation->feedback_text;
            $summary['evaluator_audio_url'] = $evaluation->feedback_audio_path
                ? asset($evaluation->feedback_audio_path) : null;
            $summary['evaluator_name']      = $evaluation->evaluator?->name;
            $summary['band_scores']         = $evaluation->band_scores ?? [];
            $summary['overall_band']        = $evaluation->overallBand();
        }

        $viewData = compact(
            'questions', 'user', 'showFeedback', 'module',
            'reviewMode', 'userSelectedOptionIds', 'userFillAnswers', 'summary',
            'evaluation'
        );

        if ($moduleType === 'reading') {
            return view('exams.ielts.reading', $viewData);
        } else if ($moduleType === 'listening') {
            return view('exams.ielts.listening', $viewData);
        } else if ($moduleType === 'writing') {
            return view('exams.ielts.writing', $viewData);
        } else if ($moduleType === 'speaking') {
            return view('exams.ielts.speaking', $viewData);
        } else if ($moduleType === 'general_mcq') {
            return view('exams.general.mcq', $viewData);
        }

        abort(404, 'Module type not found.');
    }


    // This function will convert the nested answer structure into a linear array of option IDs 
    // and also capture fill in the blanks answers
    // Returns an array with 'option_ids' and 'fill_in_blank_answers'
    public function makeArrayLinear($array)
    {
        $allOptionIds = [];
        $fill_in_blank_answers = [];

        foreach ($array as $key => $value) {

            if(is_numeric($value)){ //single mcq
                $allOptionIds[] = (int) $value;
            }
            else if (is_array($value)) //mcq multiselect
            {
                foreach ($value as $v) {
                    if (is_numeric($v)) {
                        $allOptionIds[] = (int) $v;
                    }
                }
                // $allOptionIds = array_merge($allOptionIds, $value);
            }
            // else if(is_string($value) && trim($value) !== ''){ // For fill in the blanks case
            //     $allOptionIds = array_merge($allOptionIds, [$key]);
            // } 
            else { //fill_in_blanks
                $allOptionIds = array_merge($allOptionIds, [$key]);
                $fill_in_blank_answers[$key] = $value; // Store the user's input for fill in the blanks
            }
        }

        return [
            'option_ids' => $allOptionIds,
            'fill_in_blank_answers' => $fill_in_blank_answers
        ];
    }

    /*
    * Saves both IELTS Reading and Listening answers since they have similar 
    * structure and handling (mcq single, mcq multiple, fill in the blanks, etc.
    *
    */

    public function submitIELTSReadingAndListening(Request $request)
    {
        // dd($request->all());

        $user = auth()->user();
        $answers = $request->input('answers', []);
        $moduleId = $request->input('module_id');
        $moduleType = $request->input('module_type');
        $examName = $request->input('exam_name');
        $totalQuestions = $request->input('total_questions');
        
        // dd($answers);

        //First get the question_type for each question from the request
        // We need this to handle different question types (mcq_single, mcq_multiple, fill_in_blanks) 
        // differently while saving answers
        $allOptionIds = [];
        foreach ($answers as $questionId => $data) {

            if (isset($data['question_option_id'])) {
                
                $optionIds = $this->makeArrayLinear($data['question_option_id'])['option_ids'];
                $allOptionIds = array_merge($allOptionIds, $optionIds);
            }
        }
        

        $questionTypes = QuestionOptions::whereIn('id', $allOptionIds)
                    ->get(['id', 'question_type'])
                    ->keyBy('id')
                    ->pluck('question_type', 'id')
                    ->toArray();

        // dd($request->all(), $allOptionIds, $questionTypes);

        //Update Exam Attempt
        $examAttempt = ExamAttempt::updateOrCreate(
            [
                'user_id' => $user->id,
                'module_id' => $moduleId,
                'started_at' => now(),
                'status' => 'completed',
            ],
            [
                'ended_at' => now(),
            ]
        );

        // answers contains the question_id as key and an array of question_option_id(s) 
        foreach ($answers as $questionId => $data) {
                
                //dd($data['question_option_id']);

                // dd($data);

                // Normalize to array (important)
                // $optionIds = 
                //     is_array($data['question_option_id'])
                //     ? array_merge($optionIds ?? [], $data['question_option_id'])
                //     : [$data[$questionId]]; // $data['question_option_id']

                $optionIds = $this->makeArrayLinear($data['question_option_id'])['option_ids'];
                $fillInBlankAnswers = $this->makeArrayLinear($data['question_option_id'])['fill_in_blank_answers'];
                
                // if(in_array(21, $optionIds))
                // {
                //     dd($data, $optionIds, $fillInBlankAnswers);
                // }

                // if(in_array(22, $optionIds)){
                //     dd($questionId, $data['question_option_id'], $optionIds);
                // }
                

                // optionIds contains the IDs of the options selected by the user for this question 
                // (can be multiple for multiselect, single for single select, 
                // and can also contain question IDs for fill in the blanks)
                foreach ($optionIds as $optionId) {
                    
                    //var_dump($optionId);
                    
                    //dd($optionIds);

                    //Check question type using optionId
                    if(array_key_exists($optionId, $questionTypes)){
                        $questionType = $questionTypes[$optionId];
                    } else {
                        // Handle the case where the option ID is not found in the question types
                        // You might want to log this or set a default value
                        $questionType = null; // or 'unknown'
                    }

                    //echo "Processing Question ID: $questionId, Option ID: $optionId\n" . "Question Type: $questionType\n";

                    //Check if the answer is correct
                    $isCorrect = $this->CheckIfAnswerIsCorrect($optionId, $fillInBlankAnswers);
                    // $isCorrect = true;

                    if ($questionType === 'mcq_multiple') { //For multiselect case

                            //multi select has multiple option IDs for the same question ID, so we will update or create answer for each option ID with the same question ID
                            //$isCorrect = $this->CheckIfAnswerIsCorrect($optionId, $fillInBlankAnswers);
                            //dd($isCorrect, $optionId);
                            Answer::updateOrCreate([
                                'user_id' => $user->id,
                                'exam_attempt_id' => $examAttempt->id,
                                'question_id' => $questionId,
                                'question_option_id' => $optionId, //$nestedId,
                            ],
                            [
                                'is_correct' => $isCorrect,
                            ]
                            );
                        
                    } 
                    else if($questionType === 'mcq_single'){ // normal case
                        Answer::updateOrCreate(
                            [
                                'user_id' => $user->id,
                                'exam_attempt_id' => $examAttempt->id,
                                'question_id' => $questionId,
                                'question_option_id' => is_numeric($optionId) ? $optionId : null,
                            ],
                            [
                                'is_correct' => $isCorrect,
                            ]
                        );
                    }
                    else if($questionType === 'fill_in_blanks'){ // For fill in the blanks case // && trim($optionId) !== ''
                        
                        Answer::updateOrCreate(
                            [
                                'user_id' => $user->id,
                                'exam_attempt_id' => $examAttempt->id,
                                'question_id' => $questionId,
                                'question_option_id' => $optionId, // No option ID for fill in the blanks
                            ],
                            [
                                'answer' => $fillInBlankAnswers[$optionId], // Store the user's input as the answer
                                'is_correct' => $isCorrect,
                            ]
                        );
                    }
                    else if($questionType === 'mcq_select'){ // For select dropdown case

                        Answer::updateOrCreate(
                            [
                                'user_id' => $user->id,
                                'exam_attempt_id' => $examAttempt->id,
                                'question_id' => $questionId,
                                'question_option_id' => is_numeric($optionId) ? $optionId : null,
                            ],
                            [
                                'is_correct' => $isCorrect,
                            ]
                        );
                    }
                    else{
                        // Handle unknown question types if necessary
                    }
                }
        }


        //Save the results for the exam attempt here (calculate score, save to results table, etc.)
        $status = 'completed'; // or 'pending' if you want to evaluate later
        $correctAnswer = Answer::where('exam_attempt_id', $examAttempt->id)
                        ->where('is_correct', true)
                        ->count();

        //$totalScore = Question::where('module_id', 1)->count(); // Total questions in the module
        
        
        $scorePercentage = $totalQuestions > 0 ? ($correctAnswer / $totalQuestions) * 100 : 0;
        $bandScore = $this->calculateIELTSBand($correctAnswer);
        $timeTakenSeconds = $examAttempt->ended_at->diffInSeconds($examAttempt->started_at);

        // Save the result to the results table
        $result = Results::updateOrCreate(
            [
                'user_id' => $user->id,
                'exam_attempt_id' => $examAttempt->id,
            ],
            [
                'status' => $status,
                'exam_name' => $examName, 
                'module_name' => ModuleConstants::MODULES[$moduleType] ?? null,
                'achieved_score' => $correctAnswer,
                'total_score' => $totalQuestions,
                'score_percentage' => $scorePercentage,
                'band_score' => $bandScore,
                'time_taken_seconds' => $timeTakenSeconds,
            ]
        );

        // Update user exam status to completed
        $this->updateUserExamStatus($user->id, $moduleId, 'completed');


        return response()->json([
            'message'            => 'Your answers have been submitted successfully!',
            'summary'            => [
                'exam_name'            => $examName,
                'module_name'          => ModuleConstants::MODULES[$moduleType] ?? $moduleType,
                'total_questions'      => $totalQuestions,
                'correct_answers'      => $correctAnswer,
                'score_percentage'     => round($scorePercentage, 2),
                'band_score'           => $bandScore,
                'time_elapsed_seconds' => $timeTakenSeconds,
            ],
            'achieved_score'     => $correctAnswer,
            'total_score'        => $totalQuestions,
            'score_percentage'   => $scorePercentage,
            'band_score'         => $bandScore,
        ]);
    }

    public function submitIeltsSpeakingAudio(Request $request)
    {
        $user       = auth()->user();
        $questionId = $request->input('question_id');
        $moduleId   = $request->input('module_id');
        $audioFile  = $request->file('audio');

        if (!$audioFile) {
            return response()->json(['message' => 'No audio file uploaded.'], 400);
        }
        if (!$moduleId) {
            return response()->json(['message' => 'module_id is missing.'], 422);
        }

        $filename = uniqid() . '_' . $audioFile->getClientOriginalName();
        $path     = $audioFile->storeAs('speaking-audios', $filename, 'public');
        $audioUrl = str_replace('public/', 'storage/', $path);

        // One in-progress ExamAttempt per (user, module).
        // Don't put `started_at`/`status` in the lookup keys — they'd diverge each call.
        $examAttempt = ExamAttempt::firstOrCreate(
            [
                'user_id'   => $user->id,
                'module_id' => $moduleId,
            ],
            [
                'started_at' => now(),
                'status'     => 'in_progress',
            ]
        );

        Answer::updateOrCreate(
            [
                'user_id'         => $user->id,
                'question_id'     => $questionId,
                'exam_attempt_id' => $examAttempt->id,
            ],
            [
                'answer'             => $audioUrl,
                'question_option_id' => null,
                'is_correct'         => null,
            ]
        );

        return response()->json([
            'message'   => 'Audio uploaded successfully!',
            'audio_url' => asset($audioUrl),
        ]);
    }

    /**
     * Final "Submit" press from the speaking blade — closes out the ExamAttempt,
     * creates the Results row, and flips UserExam to completed so it lands in
     * the admin's evaluation DataTable.
     */
    public function submitIeltsSpeakingFinalize(Request $request)
    {
        $user           = auth()->user();
        $moduleId       = $request->input('module_id');
        $moduleType     = $request->input('module_type', 'speaking');
        $examName       = $request->input('exam_name');
        $totalQuestions = (int) $request->input('total_questions', 0);

        if (!$moduleId) {
            return response()->json(['message' => 'module_id is missing.'], 422);
        }

        $examAttempt = ExamAttempt::where('user_id', $user->id)
            ->where('module_id', $moduleId)
            ->orderByDesc('id')
            ->first();

        if (!$examAttempt) {
            $examAttempt = ExamAttempt::create([
                'user_id'    => $user->id,
                'module_id'  => $moduleId,
                'started_at' => now(),
                'status'     => 'completed',
                'ended_at'   => now(),
            ]);
        } else {
            $examAttempt->update([
                'status'   => 'completed',
                'ended_at' => now(),
            ]);
        }

        if ($totalQuestions === 0) {
            $totalQuestions = Answer::where('exam_attempt_id', $examAttempt->id)->count();
        }

        $timeTakenSeconds = $examAttempt->ended_at->diffInSeconds($examAttempt->started_at);

        Results::updateOrCreate(
            [
                'user_id'         => $user->id,
                'exam_attempt_id' => $examAttempt->id,
            ],
            [
                'status'             => 'pending', // → manual_review on assign → evaluated on submit
                'exam_name'          => $examName,
                'module_name'        => ModuleConstants::MODULES[$moduleType] ?? 'Speaking',
                'achieved_score'     => 0,
                'total_score'        => $totalQuestions,
                'score_percentage'   => 0,
                'band_score'         => null,
                'time_taken_seconds' => $timeTakenSeconds,
            ]
        );

        $this->updateUserExamStatus($user->id, $moduleId, 'completed');

        return response()->json([
            'message' => 'Speaking exam submitted. Awaiting evaluator assignment.',
            'summary' => [
                'exam_name'            => $examName,
                'module_name'          => ModuleConstants::MODULES[$moduleType] ?? 'Speaking',
                'total_questions'      => $totalQuestions,
                'time_elapsed_seconds' => $timeTakenSeconds,
            ],
        ]);
    }

    public function submitIELTSWriting(Request $request)
    {
        $user           = auth()->user();
        $answers        = $request->input('answers', []);
        $moduleId       = $request->input('module_id');
        $moduleType     = $request->input('module_type', 'writing');
        $examName       = $request->input('exam_name');
        $totalQuestions = (int) $request->input('total_questions', count($answers));

        if (!$moduleId) {
            return response()->json(['message' => 'module_id is missing.'], 422);
        }

        // Find or create the ExamAttempt for this user/module — only one active per pair
        $examAttempt = ExamAttempt::where('user_id', $user->id)
            ->where('module_id', $moduleId)
            ->orderByDesc('id')
            ->first();

        if (!$examAttempt) {
            $examAttempt = ExamAttempt::create([
                'user_id'    => $user->id,
                'module_id'  => $moduleId,
                'started_at' => now(),
                'status'     => 'completed',
                'ended_at'   => now(),
            ]);
        } else {
            $examAttempt->update([
                'status'   => 'completed',
                'ended_at' => now(),
            ]);
        }

        // Save each writing answer (one per question)
        foreach ($answers as $questionId => $data) {
            Answer::updateOrCreate(
                [
                    'user_id'         => $user->id,
                    'exam_attempt_id' => $examAttempt->id,
                    'question_id'     => $questionId,
                ],
                [
                    'answer'             => $data['answer'] ?? null,
                    'question_option_id' => null,
                    'is_correct'         => null, // Writing answers are graded by evaluator
                ]
            );
        }

        // Save Results row in pending state — evaluator will fill band scores later
        $timeTakenSeconds = $examAttempt->ended_at->diffInSeconds($examAttempt->started_at);
        Results::updateOrCreate(
            [
                'user_id'         => $user->id,
                'exam_attempt_id' => $examAttempt->id,
            ],
            [
                'status'             => 'pending',     // → 'manual_review' on assign → 'evaluated' on submit
                'exam_name'          => $examName,
                'module_name'        => ModuleConstants::MODULES[$moduleType] ?? 'Writing',
                'achieved_score'     => 0,
                'total_score'        => $totalQuestions,
                'score_percentage'   => 0,
                'band_score'         => null,
                'time_taken_seconds' => $timeTakenSeconds,
            ]
        );

        // Mark UserExam as completed so it shows on the student dashboard with "Show Result"
        $this->updateUserExamStatus($user->id, $moduleId, 'completed');

        return response()->json([
            'message' => 'Writing answers submitted successfully!',
            //'answers' => $answers,
        ]);
    }

    /*
    * Saves General MCQ exam answers (mcq_single, mcq_multiple).
    * Auto-grades answers and saves results without a band score.
    */
    public function submitGeneralMCQ(Request $request)
    {
        $user           = auth()->user();
        $answers        = $request->input('answers', []);
        $moduleId       = $request->input('module_id');
        $moduleType     = $request->input('module_type');
        $examName       = $request->input('exam_name');
        $totalQuestions = (int) $request->input('total_questions', 0);

        $allOptionIds = [];
        foreach ($answers as $questionId => $data) {
            if (isset($data['question_option_id'])) {
                $optionIds = $this->makeArrayLinear($data['question_option_id'])['option_ids'];
                $allOptionIds = array_merge($allOptionIds, $optionIds);
            }
        }

        $questionTypes = QuestionOptions::whereIn('id', $allOptionIds)
            ->get(['id', 'question_type'])
            ->keyBy('id')
            ->pluck('question_type', 'id')
            ->toArray();

        $examAttempt = ExamAttempt::updateOrCreate(
            [
                'user_id'    => $user->id,
                'module_id'  => $moduleId,
                'started_at' => now(),
                'status'     => 'completed',
            ],
            [
                'ended_at' => now(),
            ]
        );

        foreach ($answers as $questionId => $data) {
            $optionIds          = $this->makeArrayLinear($data['question_option_id'])['option_ids'];

            foreach ($optionIds as $optionId) {
                $questionType = $questionTypes[$optionId] ?? null;
                $isCorrect    = QuestionOptions::where('id', $optionId)->value('is_correct');

                if ($questionType === 'mcq_multiple') {
                    Answer::updateOrCreate(
                        [
                            'user_id'         => $user->id,
                            'exam_attempt_id' => $examAttempt->id,
                            'question_id'     => $questionId,
                            'question_option_id' => $optionId,
                        ],
                        [       
                            'is_correct'         => $isCorrect,
                        ]
                    );
                } else if ($questionType === 'mcq_single') {
                    Answer::updateOrCreate(
                        [
                            'user_id'            => $user->id,
                            'exam_attempt_id'    => $examAttempt->id,
                            'question_id'        => $questionId,
                            'question_option_id' => is_numeric($optionId) ? $optionId : null,
                        ],
                        [
                            'is_correct' => $isCorrect,
                        ]
                    );
                }
            }
        }

        $achievedScore    = Answer::where('exam_attempt_id', $examAttempt->id)->where('is_correct', true)->count();
        $totalScore       = $totalQuestions > 0 ? $totalQuestions : Question::where('module_id', $moduleId)->count();
        $scorePercentage  = $totalScore > 0 ? ($achievedScore / $totalScore) * 100 : 0;
        $timeTakenSeconds = $examAttempt->ended_at->diffInSeconds($examAttempt->started_at);

        Results::updateOrCreate(
            [
                'user_id'         => $user->id,
                'exam_attempt_id' => $examAttempt->id,
            ],
            [
                'status'             => 'completed',
                'exam_name'          => $examName,
                'module_name'        => ModuleConstants::MODULES[$moduleType] ?? 'General MCQ',
                'achieved_score'     => $achievedScore,
                'total_score'        => $totalScore,
                'score_percentage'   => $scorePercentage,
                'band_score'         => null,
                'time_taken_seconds' => $timeTakenSeconds,
            ]
        );

        $this->updateUserExamStatus($user->id, $moduleId, 'completed');

        return response()->json([
            'message'          => 'Your answers have been submitted successfully!',
            'summary'          => [
                'exam_name'            => $examName,
                'module_name'          => ModuleConstants::MODULES[$moduleType] ?? 'General MCQ',
                'total_questions'      => $totalScore,
                'correct_answers'      => $achievedScore,
                'score_percentage'     => round($scorePercentage, 2),
                'band_score'           => null,
                'time_elapsed_seconds' => $timeTakenSeconds,
            ],
            'achieved_score'   => $achievedScore,
            'total_score'      => $totalScore,
            'score_percentage' => $scorePercentage,
        ]);
    }

    public function calculateIELTSBand($correctAnswers) 
    {
        if ($correctAnswers >= 39) return 9.0;
        if ($correctAnswers >= 37) return 8.5;
        if ($correctAnswers >= 35) return 8.0;
        if ($correctAnswers >= 33) return 7.5;
        if ($correctAnswers >= 30) return 7.0;
        if ($correctAnswers >= 27) return 6.5;
        if ($correctAnswers >= 23) return 6.0;
        if ($correctAnswers >= 19) return 5.5;
        if ($correctAnswers >= 15) return 5.0;
        if ($correctAnswers >= 13) return 4.5;
        if ($correctAnswers >= 10) return 4.0;
        if ($correctAnswers >= 8)  return 3.5;
        if ($correctAnswers >= 6)  return 3.0;
        if ($correctAnswers >= 4)  return 2.5;
        if ($correctAnswers >= 2)  return 2.0;
        if ($correctAnswers >= 1)  return 1.5;

        return 0.0;
    }

    /*
    * Returns true if the selected option ID is correct for MCQs 
    * or if the fill in the blank answer matches the correct answer.
    */
    public function CheckIfAnswerIsCorrect($optionId, $fillInBlankAnswers)
    {
        // dd($optionId, $fillInBlankAnswers);

        // Check if the option ID corresponds to a fill in the blanks question
        if (array_key_exists($optionId, $fillInBlankAnswers)) {
            // For fill in the blanks, you would compare the user's answer with the correct answer
            // This is a simplified example, you might want to implement more complex logic (e.g., case-insensitive comparison, partial credit, etc.)
            $userAnswer = trim($fillInBlankAnswers[$optionId]);
            $correctAnswer = QuestionOptions::where('id', $optionId)->value('correct_answer_fib');
            // if($optionId == 21){
            //     // dd($userAnswer, $correctAnswer);
            //     dd($userAnswer === trim($correctAnswer));
            // }

            return $userAnswer === trim($correctAnswer);
        } else {
            // For MCQ questions, check if the selected option is marked as correct
            return QuestionOptions::where('id', $optionId)->value('is_correct');
        }
    }

    public function updateUserExamStatus($userId, $moduleId, $status)
    {
        return UserExam::where('user_id', $userId)
                ->where('module_id', $moduleId)
                ->update(['status' => $status]);
    }

    public function showExams($examName)
    {
        // This function can be used to show the exam instructions page before starting the exam
        // You can customize this based on your requirements

        //$user = auth()->user();

        // $exams = UserExam::with(['module.exam'])
        //     ->where('user_id', $user->id)
        //     ->orderByDesc('purchased_at')
        //     ->get();
        // //dd($exams);

        // select ex.*, m.* from exams as ex left join modules as m on ex.id=m.exam_id
        // where ex.tag='gre'
        $exams = Exam::with('modules')
            ->where('tag', $examName)
            ->where('is_active', true)
            ->orderByDesc('created_at')
            ->get();
            // ->toArray();

        // dd($exams);

        return view('student.dashboard-all-exams', compact('exams'));
    }
}
