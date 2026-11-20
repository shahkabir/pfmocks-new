<?php

/*
|--------------------------------------------------------------------------
| Duolingo English Test (DET) — static catalogue & grading rules
|--------------------------------------------------------------------------
| Source: duolingo.md (project root).
|
| This file is the single source of truth for:
|   - the four skills / sections,
|   - the 21 question types (timings, response shape, scoring method),
|   - the internal grading model (objective marks, rubric weights,
|     integrated subscores, 10–160 reporting scale).
|
| The future seeder should read `sections` and `question_types` from here
| to populate `duolingo_sections` and `duolingo_question_types`.
|
| NOTE: these are PerfectMocks internal mock-exam rules, NOT Duolingo's
| proprietary IRT scoring. Raw scores and estimated proficiency are kept
| separate, as recommended in duolingo.md §6 and §8.
*/

return [

    /* ── Sections (= the four individual skills) ─────────────────────── */
    'sections' => [
        ['tag' => 'R', 'name' => 'Reading',   'display_order' => 1],
        ['tag' => 'L', 'name' => 'Listening', 'display_order' => 2],
        ['tag' => 'W', 'name' => 'Writing',   'display_order' => 3],
        ['tag' => 'S', 'name' => 'Speaking',  'display_order' => 4],
    ],

    /*
    | Response types (duolingo.md §7 internal `question_type` mapping).
    | Each one has a matching blade partial:
    |   resources/views/exams/duolingo/questions/_{response_type}.blade.php
    */
    'response_types' => [
        'fill_in_blanks',       // C-Test, Vocabulary in Context — per-blank text
        'word_classification',  // Read and Select, Listen and Select — real word Yes/No
        'mcq_single',           // Interactive Reading / Listening subtasks — option id
        'highlighting',         // Interactive Reading: Highlight the Answer — text range
        'dictation',            // Listen and Type — full sentence text
        'writing',              // Write About the Photo, Summarize the Conversation — text
        'interactive_writing',  // Two connected text responses
        'essay',                // Writing Sample — extended text
        'speaking',             // Single audio recording
        'interactive_speaking', // Multiple audio recordings (one per turn)
    ],

    'stimulus_types' => ['text', 'audio', 'image', 'word', 'none'],

    /* Scoring methods (duolingo.md §6 A/B/C). */
    'scoring_methods' => [
        'objective' => 'Correct = 1, incorrect = 0, partial = 0.5 where the rubric allows.',
        'rubric'    => 'Weighted rubric (see grading.rubric).',
    ],

    /*
    | Task groups — connected multi-step activities whose subtasks are
    | scored independently and then aggregated (duolingo.md §6 C).
    */
    'task_groups' => [
        'interactive_reading'   => 'Interactive Reading',
        'interactive_listening' => 'Interactive Listening',
    ],

    /* ── Question types (duolingo.md §1–§4) ───────────────────────────
    | Times in seconds. null = governed by the task-group / task timer.
    | answer_time_sec for word_classification is PER WORD.
    | answer_time_sec for interactive_speaking is PER TURN.
    */
    'question_types' => [
        // Reading
        ['tag' => 'RCT',  'section' => 'R', 'name' => 'Read and Complete (C-Test)',               'response_type' => 'fill_in_blanks',      'stimulus_type' => 'text',  'scoring_method' => 'objective', 'task_group' => null,                   'prep' => 0, 'answer' => 180,  'replays' => null, 'turns' => null],
        ['tag' => 'RSL',  'section' => 'R', 'name' => 'Read and Select',                          'response_type' => 'word_classification', 'stimulus_type' => 'word',  'scoring_method' => 'objective', 'task_group' => null,                   'prep' => 0, 'answer' => 5,    'replays' => null, 'turns' => null],
        ['tag' => 'IRCS', 'section' => 'R', 'name' => 'Interactive Reading — Complete the Sentences', 'response_type' => 'mcq_single',      'stimulus_type' => 'text',  'scoring_method' => 'objective', 'task_group' => 'interactive_reading',  'prep' => 0, 'answer' => null, 'replays' => null, 'turns' => null],
        ['tag' => 'IRCP', 'section' => 'R', 'name' => 'Interactive Reading — Complete the Passage',   'response_type' => 'mcq_single',      'stimulus_type' => 'text',  'scoring_method' => 'objective', 'task_group' => 'interactive_reading',  'prep' => 0, 'answer' => null, 'replays' => null, 'turns' => null],
        ['tag' => 'IRHA', 'section' => 'R', 'name' => 'Interactive Reading — Highlight the Answer',   'response_type' => 'highlighting',    'stimulus_type' => 'text',  'scoring_method' => 'objective', 'task_group' => 'interactive_reading',  'prep' => 0, 'answer' => null, 'replays' => null, 'turns' => null],
        ['tag' => 'IRII', 'section' => 'R', 'name' => 'Interactive Reading — Identify the Idea',      'response_type' => 'mcq_single',      'stimulus_type' => 'text',  'scoring_method' => 'objective', 'task_group' => 'interactive_reading',  'prep' => 0, 'answer' => null, 'replays' => null, 'turns' => null],
        ['tag' => 'IRTP', 'section' => 'R', 'name' => 'Interactive Reading — Title the Passage',      'response_type' => 'mcq_single',      'stimulus_type' => 'text',  'scoring_method' => 'objective', 'task_group' => 'interactive_reading',  'prep' => 0, 'answer' => null, 'replays' => null, 'turns' => null],
        ['tag' => 'VIC',  'section' => 'R', 'name' => 'Vocabulary in Context',                    'response_type' => 'fill_in_blanks',      'stimulus_type' => 'text',  'scoring_method' => 'objective', 'task_group' => null,                   'prep' => 0, 'answer' => null, 'replays' => null, 'turns' => null],

        // Listening
        ['tag' => 'LSL',  'section' => 'L', 'name' => 'Listen and Select',                        'response_type' => 'word_classification', 'stimulus_type' => 'audio', 'scoring_method' => 'objective', 'task_group' => null,                    'prep' => 0, 'answer' => 5,    'replays' => null, 'turns' => null],
        ['tag' => 'LTY',  'section' => 'L', 'name' => 'Listen and Type (Dictation)',              'response_type' => 'dictation',           'stimulus_type' => 'audio', 'scoring_method' => 'objective', 'task_group' => null,                    'prep' => 0, 'answer' => 60,   'replays' => 2,    'turns' => null],
        ['tag' => 'ILLR', 'section' => 'L', 'name' => 'Interactive Listening — Listen and Respond',     'response_type' => 'mcq_single',    'stimulus_type' => 'audio', 'scoring_method' => 'objective', 'task_group' => 'interactive_listening', 'prep' => 0, 'answer' => null, 'replays' => null, 'turns' => null],
        ['tag' => 'ILLC', 'section' => 'L', 'name' => 'Interactive Listening — Listen and Complete',    'response_type' => 'mcq_single',    'stimulus_type' => 'audio', 'scoring_method' => 'objective', 'task_group' => 'interactive_listening', 'prep' => 0, 'answer' => null, 'replays' => null, 'turns' => null],
        ['tag' => 'ILSC', 'section' => 'L', 'name' => 'Interactive Listening — Summarize the Conversation', 'response_type' => 'writing',   'stimulus_type' => 'none',  'scoring_method' => 'rubric',    'task_group' => 'interactive_listening', 'prep' => 0, 'answer' => null, 'replays' => null, 'turns' => null],

        // Writing
        ['tag' => 'WAP',  'section' => 'W', 'name' => 'Write About the Photo',                    'response_type' => 'writing',             'stimulus_type' => 'image', 'scoring_method' => 'rubric', 'task_group' => null, 'prep' => 0, 'answer' => 60,  'replays' => null, 'turns' => null],
        // Interactive Writing: initial response 300s + follow-up 180s (follow-up prompt stored as turn #2)
        ['tag' => 'IWR',  'section' => 'W', 'name' => 'Interactive Writing',                      'response_type' => 'interactive_writing', 'stimulus_type' => 'text',  'scoring_method' => 'rubric', 'task_group' => null, 'prep' => 0, 'answer' => 480, 'replays' => null, 'turns' => 2],
        ['tag' => 'WSM',  'section' => 'W', 'name' => 'Writing Sample',                           'response_type' => 'essay',               'stimulus_type' => 'text',  'scoring_method' => 'rubric', 'task_group' => null, 'prep' => 0, 'answer' => 300, 'replays' => null, 'turns' => null],

        // Speaking
        ['tag' => 'SAP',  'section' => 'S', 'name' => 'Speak About the Photo',                    'response_type' => 'speaking',             'stimulus_type' => 'image', 'scoring_method' => 'rubric', 'task_group' => null, 'prep' => 0, 'answer' => 90,   'replays' => null, 'turns' => null],
        ['tag' => 'RTS',  'section' => 'S', 'name' => 'Read, Then Speak',                         'response_type' => 'speaking',             'stimulus_type' => 'text',  'scoring_method' => 'rubric', 'task_group' => null, 'prep' => 0, 'answer' => 90,   'replays' => null, 'turns' => null],
        ['tag' => 'LTS',  'section' => 'S', 'name' => 'Listen, Then Speak',                       'response_type' => 'speaking',             'stimulus_type' => 'audio', 'scoring_method' => 'rubric', 'task_group' => null, 'prep' => 0, 'answer' => null, 'replays' => null, 'turns' => null],
        ['tag' => 'ISP',  'section' => 'S', 'name' => 'Interactive Speaking',                     'response_type' => 'interactive_speaking', 'stimulus_type' => 'audio', 'scoring_method' => 'rubric', 'task_group' => null, 'prep' => 0, 'answer' => 35,   'replays' => null, 'turns' => 8],
        ['tag' => 'SSM',  'section' => 'S', 'name' => 'Speaking Sample',                          'response_type' => 'speaking',             'stimulus_type' => 'text',  'scoring_method' => 'rubric', 'task_group' => null, 'prep' => 0, 'answer' => 180,  'replays' => null, 'turns' => null],
    ],

    /* ── Grading (duolingo.md §5–§6) ─────────────────────────────────── */
    'grading' => [

        // §6 A — objective marks
        'objective' => [
            'correct'   => 1.0,
            'partial'   => 0.5,
            'incorrect' => 0.0,
        ],

        // §6 B — rubric weights (%). Kept per skill so they can diverge later.
        'rubric' => [
            'writing' => [
                'relevance'   => 25, // Relevance and task completion
                'grammar'     => 25,
                'vocabulary'  => 20,
                'coherence'   => 20, // Coherence and organization
                'fluency'     => 10, // Fluency and pronunciation
            ],
            'speaking' => [
                'relevance'   => 25,
                'grammar'     => 25,
                'vocabulary'  => 20,
                'coherence'   => 20,
                'fluency'     => 10,
            ],
        ],
        // Each rubric criterion is marked on 0..rubric_criterion_max.
        'rubric_criterion_max' => 5,

        // §5 — reporting scale
        'scale' => [
            'min'       => 10,
            'max'       => 160,
            'increment' => 5,
        ],

        // §5 — integrated subscores = average of two individual subscores
        'integrated' => [
            'literacy'      => ['reading',   'writing'],
            'comprehension' => ['reading',   'listening'],
            'conversation'  => ['listening', 'speaking'],
            'production'    => ['speaking',  'writing'],
        ],

        // Section tag => individual subscore key
        'skills' => [
            'R' => 'reading',
            'W' => 'writing',
            'L' => 'listening',
            'S' => 'speaking',
        ],
    ],
];
