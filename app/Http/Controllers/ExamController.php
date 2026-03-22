<?php

namespace App\Http\Controllers;

use App\Constants\ModuleConstants;
use App\Models\Answer\Answer;
use App\Models\Answer\Results;
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
        ->active()
        ->orderBy('questions.sort_order')
        ->select([
            'questions.*',
            'question_groups.question_options_group_ids',
            'question_groups.part_number',
            'question_groups.part_audio_url',
            'question_groups.part_image_url'
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


    public function submitIELTSReading(Request $request)
    {
        //dd($request->all());

        $user = auth()->user();
        $answers = $request->input('answers', []);
        //dd($answers);

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

        //dd($allOptionIds, $questionTypes);

        //Update Exam Attempt
        $examAttempt = ExamAttempt::updateOrCreate(
            [
                'user_id' => $user->id,
                'module_id' => 1, // IELTS Reading module ID
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

                // Normalize to array (important)
                // $optionIds = 
                //     is_array($data['question_option_id'])
                //     ? array_merge($optionIds ?? [], $data['question_option_id'])
                //     : [$data[$questionId]]; // $data['question_option_id']

                $optionIds = $this->makeArrayLinear($data['question_option_id'])['option_ids'];
                $fillInBlankAnswers = $this->makeArrayLinear($data['question_option_id'])['fill_in_blank_answers'];
                
                //dd($optionIds, $fillInBlankAnswers);
  
                //dd($questionId, $data['question_option_id'], $optionIds);

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
                    $isCorrect = QuestionOptions::where('id', $optionId)->value('is_correct');
                    // $isCorrect = true;

                    if ($questionType === 'mcq_multiple') { //For multiselect case
                        
                            Answer::updateOrCreate([
                                'user_id' => $user->id,
                                'exam_attempt_id' => $examAttempt->id,
                                'question_id' => $questionId,
                            ],
                            [
                                'question_option_id' => $optionId, //$nestedId,
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
        $achievedScore = Answer::where('exam_attempt_id', $examAttempt->id)
                        ->where('is_correct', true)
                        ->count();

        $totalScore = Question::where('module_id', 1)->count(); // Total questions in the module
        $scorePercentage = $totalScore > 0 ? ($achievedScore / $totalScore) * 100 : 0;
        $bandScore = $this->calculateIELTSBand($scorePercentage);
        $timeTakenSeconds = $examAttempt->ended_at->diffInSeconds($examAttempt->started_at);

        // Save the result to the results table
        $result = Results::updateOrCreate(
            [
                'user_id' => $user->id,
                'exam_attempt_id' => $examAttempt->id,
            ],
            [
                'status' => $status,
                'exam_name' => 'IELTS Reading',
                'module_name' => 'Reading',
                'achieved_score' => $achievedScore,
                'total_score' => $totalScore,
                'score_percentage' => $scorePercentage,
                'band_score' => $bandScore,
                'time_taken_seconds' => $timeTakenSeconds,
            ]
        );



        return response()->json([
            'message' => 'Reading answers submitted successfully!',
            'achieved_score' => $achievedScore,
            'total_score' => $totalScore,
            'score_percentage' => $scorePercentage,
            'band_score' => $bandScore,
        ]);
    }

    public function submitIeltsSpeakingAudio(Request $request)
    {
        //dd($request->all());

        $user = auth()->user();
        $questionId = $request->input('question_id');
        $audioFile = $request->file('audio');

        $filename = uniqid().'_'.$audioFile->getClientOriginalName();

        if ($audioFile) {
            $path = $audioFile->storeAs('speaking-audios', $filename, 'public');
            $audioUrl = str_replace('public/', 'storage/', $path);

        //Update Exam Attempt
        $examAttempt = ExamAttempt::updateOrCreate(
            [
                'user_id' => $user->id,
                'module_id' => 1, // IELTS Reading module ID
                'started_at' => now(),
                'status' => 'completed',
            ],
            [
                'ended_at' => now(),
            ]
        );

            // Save the audio URL to the database (you can create a new model or use an existing one)
            Answer::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'question_id' => $questionId,
                    'exam_attempt_id' => $examAttempt->id,
                ],
                [
                    'answer' => $audioUrl, // Store the audio URL as the answer
                    'is_correct' => null, // Speaking answers are not auto-graded
                ]
            );

            return response()->json([
                'message' => 'Audio uploaded successfully!',
                'audio_url' => asset($audioUrl),
            ]);
        } else {
            return response()->json([
                'message' => 'No audio file uploaded.',
            ], 400);
        }
    }

    public function submitIELTSListening(Request $request)
    {
        //dd($request->all());

        $user = auth()->user();
        $answers = $request->input('answers', []);
        //dd($answers);

        // The logic for processing listening answers will be similar to reading answers
        // You can reuse the makeArrayLinear function and the way we handle different question types

        // For brevity, I'm not repeating the entire code here, but you would follow a similar structure:
        // 1. Flatten the answers array to get all option IDs and fill in the blank answers
        // 2. Get question types based on option IDs
        // 3. Update or create Answer records based on question type (mcq_single, mcq_multiple, fill_in_blanks, etc.)
        // 4. Calculate score and save results

        return response()->json([
            'message' => 'Listening answers submitted successfully!',
            //'answers' => $answers,
        ]);
    }

    public function calculateIELTSBand($scorePercentage)
    {
        // This is a simplified example. You can adjust the thresholds based on actual IELTS band score criteria.
        if ($scorePercentage >= 90) {
            return 9.0;
        } elseif ($scorePercentage >= 80) {
            return 8.0;
        } elseif ($scorePercentage >= 70) {
            return 7.0;
        } elseif ($scorePercentage >= 60) {
            return 6.0;
        } elseif ($scorePercentage >= 50) {
            return 5.0;
        } elseif ($scorePercentage >= 40) {
            return 4.0;
        } elseif ($scorePercentage >= 30) {
            return 3.0;
        } elseif ($scorePercentage >= 20) {
            return 2.0;
        } elseif ($scorePercentage >= 10) {
            return 1.0;
        } else {
            return 0.0;
        }

    }
}
