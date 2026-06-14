<?php

namespace Database\Seeders;

use App\Models\Pte\PteQuestionGranular;
use App\Models\Pte\PteQuestionSubType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeds 3 sample questions for every PTE sub-type (60 questions total).
 * For sub-types that need satellites (options / blanks / segments / highlight words),
 * the satellite rows are inserted in the same transaction.
 */
class PteQuestionGranularSeeder extends Seeder
{
    public function run(): void
    {
        $subTypes = PteQuestionSubType::pluck('id', 'tag'); // [RA => id, RS => id, ...]

        DB::transaction(function () use ($subTypes) {
            foreach ($this->dataset() as $tag => $questions) {
                if (!isset($subTypes[$tag])) continue;

                foreach ($questions as $index => $q) {
                    $granularId = $tag . str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT);

                    if (PteQuestionGranular::where('question_granular_id', $granularId)->exists()) {
                        continue;
                    }

                    $question = PteQuestionGranular::create([
                        'pte_sub_type_id'         => $subTypes[$tag],
                        'question_granular_id'    => $granularId,
                        'question_text'           => $q['question_text']     ?? null,
                        'audio_transcript'        => $q['audio_transcript']  ?? null,
                        'audio_url'               => $q['audio_url']         ?? null,
                        'image_url'               => $q['image_url']         ?? null,
                        'image_alt_text'          => $q['image_alt_text']    ?? null,
                        'preparation_time_sec'    => $q['preparation_time_sec']    ?? null,
                        'answer_time_sec'         => $q['answer_time_sec']         ?? null,
                        'marks'                   => $q['marks']                   ?? 1,
                        'correct_ans'             => $q['correct_ans']             ?? null,
                        'correct_ans_explanation' => $q['correct_ans_explanation'] ?? null,
                        'min_word_count'          => $q['min_word_count']          ?? null,
                        'max_word_count'          => $q['max_word_count']          ?? null,
                        'difficulty'              => $q['difficulty']              ?? 'medium',
                        'topic_tags'              => $q['topic_tags']              ?? null,
                        'source_reference'        => 'Seeded sample',
                        'is_active'               => true,
                    ]);

                    foreach ($q['options'] ?? [] as $i => $opt) {
                        $question->options()->create([
                            'option_text'   => $opt['text'],
                            'is_correct'    => (bool) ($opt['correct'] ?? false),
                            'display_order' => $i + 1,
                        ]);
                    }
                    foreach ($q['blanks'] ?? [] as $i => $b) {
                        $question->blanks()->create([
                            'blank_order'       => $i + 1,
                            'correct_answer'    => $b['answer'],
                            'accepted_variants' => $b['variants']  ?? null,
                            'dropdown_options'  => $b['dropdown']  ?? null,
                        ]);
                    }
                    foreach ($q['segments'] ?? [] as $i => $seg) {
                        $question->segments()->create([
                            'segment_text'  => $seg['text'],
                            'correct_order' => $seg['order'] ?? ($i + 1),
                        ]);
                    }
                    foreach ($q['highlight_words'] ?? [] as $i => $w) {
                        $question->highlightWords()->create([
                            'word_text'    => $w['word'],
                            'word_order'   => $i + 1,
                            'is_incorrect' => (bool) ($w['incorrect'] ?? false),
                        ]);
                    }
                }
            }
        });
    }

    /** @return array<string, array<int, array<string, mixed>>> */
    private function dataset(): array
    {
        return [
            // ────────────────── SPEAKING & WRITING ──────────────────

            // Read Aloud: short passages, candidate reads aloud
            'RA' => [
                ['question_text' => 'The Industrial Revolution began in Britain in the late 18th century and marked a shift from agrarian economies to industrialised, urban societies.',                 'difficulty' => 'easy',   'marks' => 5],
                ['question_text' => 'Climate change poses one of the greatest threats to global biodiversity, affecting ecosystems and the species that depend on them for survival.',                    'difficulty' => 'medium', 'marks' => 5],
                ['question_text' => 'Quantum computing exploits the principles of superposition and entanglement to perform calculations that are infeasible for classical computers.',                  'difficulty' => 'hard',   'marks' => 5],
            ],

            // Repeat Sentence: candidate hears a sentence, repeats it
            'RS' => [
                ['audio_transcript' => 'The library is open until midnight during exam week.',                             'audio_url' => 'data/audio/pte/rs0001.mp3', 'marks' => 3],
                ['audio_transcript' => 'Please submit your assignment before the end of next Friday.',                     'audio_url' => 'data/audio/pte/rs0002.mp3', 'marks' => 3],
                ['audio_transcript' => 'Renewable energy sources are becoming increasingly cost-competitive worldwide.',   'audio_url' => 'data/audio/pte/rs0003.mp3', 'marks' => 3],
            ],

            // Describe Image
            'DI' => [
                ['question_text' => 'Describe the bar chart showing population growth across five major cities from 2000 to 2020.', 'image_url' => 'data/image/pte/di0001.png', 'image_alt_text' => 'Bar chart of population growth', 'marks' => 5],
                ['question_text' => 'Describe the line graph illustrating monthly rainfall in three regions over one year.',        'image_url' => 'data/image/pte/di0002.png', 'image_alt_text' => 'Line graph of rainfall',         'marks' => 5],
                ['question_text' => 'Describe the pie chart depicting global electricity generation by source in 2023.',            'image_url' => 'data/image/pte/di0003.png', 'image_alt_text' => 'Pie chart energy mix',            'marks' => 5],
            ],

            // Re-tell Lecture
            'RL' => [
                ['question_text' => 'Lecture on photosynthesis and the role of chlorophyll in capturing solar energy.', 'audio_url' => 'data/audio/pte/rl0001.mp3', 'image_url' => 'data/image/pte/rl0001.png', 'marks' => 5],
                ['question_text' => 'Lecture on early human migration patterns out of Africa.',                          'audio_url' => 'data/audio/pte/rl0002.mp3', 'image_url' => 'data/image/pte/rl0002.png', 'marks' => 5],
                ['question_text' => 'Lecture on the basic principles of behavioural economics.',                         'audio_url' => 'data/audio/pte/rl0003.mp3', 'image_url' => 'data/image/pte/rl0003.png', 'marks' => 5],
            ],

            // Answer Short Question
            'ASQ' => [
                ['question_text' => 'What do we call a person who studies the stars and planets?',                'audio_url' => 'data/audio/pte/asq0001.mp3', 'correct_ans' => 'astronomer',  'marks' => 1],
                ['question_text' => 'What is the largest mammal living on Earth today?',                          'audio_url' => 'data/audio/pte/asq0002.mp3', 'correct_ans' => 'blue whale',  'marks' => 1],
                ['question_text' => 'In which season of the year do leaves typically change colour and fall?',    'audio_url' => 'data/audio/pte/asq0003.mp3', 'correct_ans' => 'autumn',      'marks' => 1],
            ],

            // Summarize Written Text
            'SWT' => [
                ['question_text' => 'Read the following passage about the history of the internet and summarize it in a single sentence (5–75 words).',         'min_word_count' => 5, 'max_word_count' => 75, 'marks' => 7],
                ['question_text' => 'Read the passage about urban farming and summarize it in one sentence (5–75 words).',                                      'min_word_count' => 5, 'max_word_count' => 75, 'marks' => 7],
                ['question_text' => 'Read the passage on the effects of sleep deprivation and summarize it in one sentence (5–75 words).',                     'min_word_count' => 5, 'max_word_count' => 75, 'marks' => 7],
            ],

            // Write Essay
            'WE' => [
                ['question_text' => 'Some people believe that university education should be free for everyone. Discuss both views and give your opinion. Write 200–300 words.', 'min_word_count' => 200, 'max_word_count' => 300, 'marks' => 15],
                ['question_text' => 'Social media has changed the way we communicate. Discuss the advantages and disadvantages. Write 200–300 words.',                          'min_word_count' => 200, 'max_word_count' => 300, 'marks' => 15],
                ['question_text' => 'Working from home is becoming common. Discuss whether it benefits employees and employers. Write 200–300 words.',                          'min_word_count' => 200, 'max_word_count' => 300, 'marks' => 15],
            ],

            // ────────────────── READING ──────────────────

            // Reading & Writing Fill in the Blanks (typing answers)
            'RWFIB' => [
                [
                    'question_text' => 'Despite the heavy rain, the team [BLANK] their game and managed to win the championship [BLANK].',
                    'marks'         => 2,
                    'blanks'        => [
                        ['answer' => 'continued', 'variants' => ['continued', 'finished']],
                        ['answer' => 'trophy',    'variants' => ['trophy', 'title']],
                    ],
                ],
                [
                    'question_text' => 'The new policy was [BLANK] to reduce traffic congestion and [BLANK] air quality.',
                    'marks'         => 2,
                    'blanks'        => [
                        ['answer' => 'designed', 'variants' => ['designed', 'intended']],
                        ['answer' => 'improve',  'variants' => ['improve']],
                    ],
                ],
                [
                    'question_text' => 'Scientists have [BLANK] a new species of insect in the rainforest of [BLANK] America.',
                    'marks'         => 2,
                    'blanks'        => [
                        ['answer' => 'discovered', 'variants' => ['discovered', 'found']],
                        ['answer' => 'South',      'variants' => ['South']],
                    ],
                ],
            ],

            // MCQ Multiple Answers (Reading)
            'MCMAR' => [
                [
                    'question_text' => 'According to the passage, which TWO factors contributed to the decline of the population? Select all that apply.',
                    'marks'         => 2,
                    'options'       => [
                        ['text' => 'Disease outbreak',     'correct' => true],
                        ['text' => 'Famine',               'correct' => true],
                        ['text' => 'Volcanic eruption',    'correct' => false],
                        ['text' => 'Trade expansion',      'correct' => false],
                    ],
                ],
                [
                    'question_text' => 'Which TWO of the following statements are true based on the passage?',
                    'marks'         => 2,
                    'options'       => [
                        ['text' => 'Renewable energy is becoming cheaper.', 'correct' => true],
                        ['text' => 'Solar accounts for the largest share.', 'correct' => false],
                        ['text' => 'Wind capacity has grown rapidly.',      'correct' => true],
                        ['text' => 'Coal usage has fully ended.',           'correct' => false],
                    ],
                ],
                [
                    'question_text' => 'Which TWO benefits of reading does the author mention?',
                    'marks'         => 2,
                    'options'       => [
                        ['text' => 'Improved vocabulary',      'correct' => true],
                        ['text' => 'Better sleep quality',     'correct' => false],
                        ['text' => 'Stronger empathy',         'correct' => true],
                        ['text' => 'Higher physical strength', 'correct' => false],
                    ],
                ],
            ],

            // Re-order Paragraphs
            'ROP' => [
                [
                    'question_text' => 'Arrange the following sentences in a logical order.',
                    'marks'         => 3,
                    'segments'      => [
                        ['text' => 'In the early 20th century, scientists began exploring the structure of the atom.',                 'order' => 1],
                        ['text' => 'These investigations led to the discovery of the nucleus.',                                         'order' => 2],
                        ['text' => 'Later, quantum theory radically changed our understanding of atomic behaviour.',                    'order' => 3],
                        ['text' => 'Today, atomic physics underpins technologies from medical imaging to nuclear energy.',              'order' => 4],
                    ],
                ],
                [
                    'question_text' => 'Re-order the sentences to form a coherent paragraph.',
                    'marks'         => 3,
                    'segments'      => [
                        ['text' => 'Coffee was first discovered in Ethiopia centuries ago.',                                            'order' => 1],
                        ['text' => 'From there, it spread to the Arabian Peninsula.',                                                   'order' => 2],
                        ['text' => 'European traders later brought it to the rest of the world.',                                       'order' => 3],
                        ['text' => 'It is now one of the most consumed beverages globally.',                                            'order' => 4],
                    ],
                ],
                [
                    'question_text' => 'Place the sentences in the correct sequence.',
                    'marks'         => 3,
                    'segments'      => [
                        ['text' => 'Smartphones started as devices primarily for making calls.',                                        'order' => 1],
                        ['text' => 'They quickly evolved to support text messaging and email.',                                          'order' => 2],
                        ['text' => 'The introduction of app stores transformed them into general-purpose computers.',                   'order' => 3],
                        ['text' => 'Today, smartphones are central to daily life across the globe.',                                     'order' => 4],
                    ],
                ],
            ],

            // Reading Fill in the Blanks (dropdowns)
            'RFIB' => [
                [
                    'question_text' => 'The river [BLANK] through the valley, providing water for the surrounding farms [BLANK] centuries.',
                    'marks'         => 2,
                    'blanks'        => [
                        ['answer' => 'flows',  'dropdown' => ['flows', 'walks', 'jumps', 'sits']],
                        ['answer' => 'for',    'dropdown' => ['for', 'since', 'during', 'by']],
                    ],
                ],
                [
                    'question_text' => 'Although the experiment was [BLANK], the results were [BLANK] to the team.',
                    'marks'         => 2,
                    'blanks'        => [
                        ['answer' => 'simple',     'dropdown' => ['simple', 'broken', 'expensive', 'late']],
                        ['answer' => 'surprising', 'dropdown' => ['surprising', 'boring', 'tasty', 'noisy']],
                    ],
                ],
                [
                    'question_text' => 'The committee will [BLANK] the proposal next week and [BLANK] a decision soon after.',
                    'marks'         => 2,
                    'blanks'        => [
                        ['answer' => 'review',  'dropdown' => ['review', 'eat', 'sing', 'paint']],
                        ['answer' => 'reach',   'dropdown' => ['reach', 'lose', 'spill', 'forget']],
                    ],
                ],
            ],

            // MCQ Single Answer (Reading)
            'MCSAR' => [
                [
                    'question_text' => 'What is the main idea of the passage?',
                    'marks'         => 1,
                    'options'       => [
                        ['text' => 'Technology has reshaped modern education.', 'correct' => true],
                        ['text' => 'Education was better in the past.',         'correct' => false],
                        ['text' => 'Teachers should be paid more.',             'correct' => false],
                        ['text' => 'Students dislike online classes.',          'correct' => false],
                    ],
                ],
                [
                    'question_text' => 'Which best describes the author\'s tone?',
                    'marks'         => 1,
                    'options'       => [
                        ['text' => 'Optimistic and forward-looking', 'correct' => true],
                        ['text' => 'Pessimistic',                    'correct' => false],
                        ['text' => 'Neutral',                        'correct' => false],
                        ['text' => 'Sarcastic',                      'correct' => false],
                    ],
                ],
                [
                    'question_text' => 'What conclusion does the passage support?',
                    'marks'         => 1,
                    'options'       => [
                        ['text' => 'Renewable energy will dominate by 2050.', 'correct' => true],
                        ['text' => 'Fossil fuels are still the cheapest.',    'correct' => false],
                        ['text' => 'Nuclear energy is unsafe.',               'correct' => false],
                        ['text' => 'Hydropower is no longer used.',           'correct' => false],
                    ],
                ],
            ],

            // ────────────────── LISTENING ──────────────────

            // Summarize Spoken Text
            'SST' => [
                ['question_text' => 'Listen to the lecture on ocean currents and summarize it in 50–70 words.', 'audio_url' => 'data/audio/pte/sst0001.mp3', 'min_word_count' => 50, 'max_word_count' => 70, 'marks' => 10],
                ['question_text' => 'Listen to the talk on volunteer tourism and summarize it in 50–70 words.', 'audio_url' => 'data/audio/pte/sst0002.mp3', 'min_word_count' => 50, 'max_word_count' => 70, 'marks' => 10],
                ['question_text' => 'Listen to the lecture on AI ethics and summarize it in 50–70 words.',      'audio_url' => 'data/audio/pte/sst0003.mp3', 'min_word_count' => 50, 'max_word_count' => 70, 'marks' => 10],
            ],

            // MCQ Multiple Answers (Listening)
            'MCMAL' => [
                [
                    'question_text' => 'Which TWO points does the speaker mention as benefits of remote learning?',
                    'audio_url'     => 'data/audio/pte/mcmal0001.mp3', 'marks' => 2,
                    'options'       => [
                        ['text' => 'Flexible schedule',  'correct' => true],
                        ['text' => 'Reduced commuting',  'correct' => true],
                        ['text' => 'Better lunch food',  'correct' => false],
                        ['text' => 'More homework',      'correct' => false],
                    ],
                ],
                [
                    'question_text' => 'Pick TWO concerns the speaker raises about plastic waste.',
                    'audio_url'     => 'data/audio/pte/mcmal0002.mp3', 'marks' => 2,
                    'options'       => [
                        ['text' => 'Ocean pollution',     'correct' => true],
                        ['text' => 'Wildlife harm',       'correct' => true],
                        ['text' => 'Higher fuel costs',   'correct' => false],
                        ['text' => 'Faster shipping',     'correct' => false],
                    ],
                ],
                [
                    'question_text' => 'Which TWO advantages of fermented foods does the speaker mention?',
                    'audio_url'     => 'data/audio/pte/mcmal0003.mp3', 'marks' => 2,
                    'options'       => [
                        ['text' => 'Improved gut health', 'correct' => true],
                        ['text' => 'Longer shelf life',   'correct' => true],
                        ['text' => 'Faster cooking',      'correct' => false],
                        ['text' => 'Lower price',         'correct' => false],
                    ],
                ],
            ],

            // Listening Fill in the Blanks (typing words you hear)
            'LFIB' => [
                [
                    'question_text'    => 'Type the missing words you hear: "The committee [BLANK] to postpone the meeting until next [BLANK]."',
                    'audio_url'        => 'data/audio/pte/lfib0001.mp3', 'audio_transcript' => 'The committee decided to postpone the meeting until next Tuesday.',
                    'marks'            => 2,
                    'blanks'           => [
                        ['answer' => 'decided'],
                        ['answer' => 'Tuesday'],
                    ],
                ],
                [
                    'question_text'    => 'Type the missing words: "Modern cities face [BLANK] challenges in providing affordable [BLANK]."',
                    'audio_url'        => 'data/audio/pte/lfib0002.mp3', 'audio_transcript' => 'Modern cities face significant challenges in providing affordable housing.',
                    'marks'            => 2,
                    'blanks'           => [
                        ['answer' => 'significant'],
                        ['answer' => 'housing'],
                    ],
                ],
                [
                    'question_text'    => 'Type the missing words: "Researchers [BLANK] that sleep affects [BLANK] performance."',
                    'audio_url'        => 'data/audio/pte/lfib0003.mp3', 'audio_transcript' => 'Researchers discovered that sleep affects cognitive performance.',
                    'marks'            => 2,
                    'blanks'           => [
                        ['answer' => 'discovered'],
                        ['answer' => 'cognitive'],
                    ],
                ],
            ],

            // Highlight Correct Summary
            'HCS' => [
                [
                    'question_text' => 'Listen to the recording, then choose the option that best summarises it.',
                    'audio_url'     => 'data/audio/pte/hcs0001.mp3', 'marks' => 1,
                    'options'       => [
                        ['text' => 'The speaker explains how solar panels work and their growing adoption.',                  'correct' => true],
                        ['text' => 'The speaker argues that nuclear power should replace fossil fuels.',                       'correct' => false],
                        ['text' => 'The speaker describes how wind turbines are built.',                                       'correct' => false],
                        ['text' => 'The speaker compares electric cars with petrol cars.',                                     'correct' => false],
                    ],
                ],
                [
                    'question_text' => 'Choose the summary that best matches the recording.',
                    'audio_url'     => 'data/audio/pte/hcs0002.mp3', 'marks' => 1,
                    'options'       => [
                        ['text' => 'The talk discusses ancient trade routes and their cultural impact.',                       'correct' => true],
                        ['text' => 'The talk focuses on modern shipping logistics.',                                            'correct' => false],
                        ['text' => 'The talk examines the rise of online retail.',                                              'correct' => false],
                        ['text' => 'The talk introduces a new historical novel.',                                               'correct' => false],
                    ],
                ],
                [
                    'question_text' => 'Pick the option that summarises the recording most accurately.',
                    'audio_url'     => 'data/audio/pte/hcs0003.mp3', 'marks' => 1,
                    'options'       => [
                        ['text' => 'The speaker outlines the benefits and risks of artificial intelligence.',                  'correct' => true],
                        ['text' => 'The speaker presents an AI start-up\'s business plan.',                                     'correct' => false],
                        ['text' => 'The speaker introduces a new programming language.',                                        'correct' => false],
                        ['text' => 'The speaker recounts the history of computer hardware.',                                    'correct' => false],
                    ],
                ],
            ],

            // MCQ Single Answer (Listening)
            'MCSAL' => [
                [
                    'question_text' => 'What is the speaker\'s main point?',
                    'audio_url'     => 'data/audio/pte/mcsal0001.mp3', 'marks' => 1,
                    'options'       => [
                        ['text' => 'Exercise improves long-term mental health.', 'correct' => true],
                        ['text' => 'Diet matters more than exercise.',           'correct' => false],
                        ['text' => 'Sleep is unimportant for athletes.',         'correct' => false],
                        ['text' => 'Stress has no impact on the brain.',         'correct' => false],
                    ],
                ],
                [
                    'question_text' => 'What does the speaker recommend?',
                    'audio_url'     => 'data/audio/pte/mcsal0002.mp3', 'marks' => 1,
                    'options'       => [
                        ['text' => 'Reading reviews before buying online.',  'correct' => true],
                        ['text' => 'Buying without research.',               'correct' => false],
                        ['text' => 'Avoiding online shopping altogether.',   'correct' => false],
                        ['text' => 'Only shopping in physical stores.',       'correct' => false],
                    ],
                ],
                [
                    'question_text' => 'According to the speaker, what is the main cause of the problem?',
                    'audio_url'     => 'data/audio/pte/mcsal0003.mp3', 'marks' => 1,
                    'options'       => [
                        ['text' => 'Rapid urbanisation.',         'correct' => true],
                        ['text' => 'Decline in birth rates.',     'correct' => false],
                        ['text' => 'Reduced public spending.',    'correct' => false],
                        ['text' => 'Changes in farming methods.', 'correct' => false],
                    ],
                ],
            ],

            // Select Missing Word
            'SMW' => [
                [
                    'question_text' => 'Listen, then select the word that best completes the recording.',
                    'audio_url'     => 'data/audio/pte/smw0001.mp3', 'marks' => 1,
                    'options'       => [
                        ['text' => 'sustainability', 'correct' => true],
                        ['text' => 'rivalry',        'correct' => false],
                        ['text' => 'memory',         'correct' => false],
                        ['text' => 'engineering',    'correct' => false],
                    ],
                ],
                [
                    'question_text' => 'Pick the missing final word from the recording.',
                    'audio_url'     => 'data/audio/pte/smw0002.mp3', 'marks' => 1,
                    'options'       => [
                        ['text' => 'collaboration', 'correct' => true],
                        ['text' => 'invention',     'correct' => false],
                        ['text' => 'currency',      'correct' => false],
                        ['text' => 'translation',   'correct' => false],
                    ],
                ],
                [
                    'question_text' => 'Choose the word that completes the recording.',
                    'audio_url'     => 'data/audio/pte/smw0003.mp3', 'marks' => 1,
                    'options'       => [
                        ['text' => 'productivity', 'correct' => true],
                        ['text' => 'celebration',  'correct' => false],
                        ['text' => 'navigation',   'correct' => false],
                        ['text' => 'ceremony',     'correct' => false],
                    ],
                ],
            ],

            // Highlight Incorrect Words (compare audio with transcript)
            'HIW' => [
                [
                    'question_text'    => 'Click the words that differ from what the speaker says.',
                    'audio_url'        => 'data/audio/pte/hiw0001.mp3',
                    'audio_transcript' => 'The students walked quickly to the library to finish their assignments.',
                    'marks'            => 1,
                    'highlight_words'  => [
                        ['word' => 'The'],
                        ['word' => 'students'],
                        ['word' => 'walked'],
                        ['word' => 'slowly',     'incorrect' => true],
                        ['word' => 'to'],
                        ['word' => 'the'],
                        ['word' => 'library'],
                        ['word' => 'to'],
                        ['word' => 'finish'],
                        ['word' => 'their'],
                        ['word' => 'homework',   'incorrect' => true],
                    ],
                ],
                [
                    'question_text'    => 'Highlight the incorrect words in the transcript.',
                    'audio_url'        => 'data/audio/pte/hiw0002.mp3',
                    'audio_transcript' => 'Modern cities require efficient transport systems to reduce congestion and pollution.',
                    'marks'            => 1,
                    'highlight_words'  => [
                        ['word' => 'Modern'],
                        ['word' => 'cities'],
                        ['word' => 'demand',     'incorrect' => true],
                        ['word' => 'efficient'],
                        ['word' => 'transport'],
                        ['word' => 'systems'],
                        ['word' => 'to'],
                        ['word' => 'reduce'],
                        ['word' => 'traffic',    'incorrect' => true],
                        ['word' => 'and'],
                        ['word' => 'pollution'],
                    ],
                ],
                [
                    'question_text'    => 'Mark the words that do not match the audio.',
                    'audio_url'        => 'data/audio/pte/hiw0003.mp3',
                    'audio_transcript' => 'Researchers have discovered a new species of fish in the deep ocean.',
                    'marks'            => 1,
                    'highlight_words'  => [
                        ['word' => 'Researchers'],
                        ['word' => 'have'],
                        ['word' => 'found',     'incorrect' => true],
                        ['word' => 'a'],
                        ['word' => 'new'],
                        ['word' => 'species'],
                        ['word' => 'of'],
                        ['word' => 'bird',      'incorrect' => true],
                        ['word' => 'in'],
                        ['word' => 'the'],
                        ['word' => 'deep'],
                        ['word' => 'ocean'],
                    ],
                ],
            ],

            // Write from Dictation
            'WFD' => [
                ['audio_url' => 'data/audio/pte/wfd0001.mp3', 'audio_transcript' => 'The professor will publish her research findings next month.', 'correct_ans' => 'The professor will publish her research findings next month.', 'marks' => 3],
                ['audio_url' => 'data/audio/pte/wfd0002.mp3', 'audio_transcript' => 'Climate change affects agriculture in many parts of the world.',  'correct_ans' => 'Climate change affects agriculture in many parts of the world.',  'marks' => 3],
                ['audio_url' => 'data/audio/pte/wfd0003.mp3', 'audio_transcript' => 'Most students prefer to study in groups before final exams.',     'correct_ans' => 'Most students prefer to study in groups before final exams.',     'marks' => 3],
            ],
        ];
    }
}
