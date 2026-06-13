<?php

namespace Database\Seeders;

use App\Models\Pte\PteQuestionSubType;
use App\Models\Pte\PteSection;
use Illuminate\Database\Seeder;

class PteQuestionSubTypeSeeder extends Seeder
{
    public function run(): void
    {
        $sections = PteSection::pluck('id', 'tag'); // [SPWR=>id, RD=>id, LS=>id]

        $subTypes = [
            // ── Speaking & Writing (SPWR) ─────────────────────────────────
            ['section' => 'SPWR', 'name' => 'Read Aloud',                 'tag' => 'RA',    'response_type' => 'audio_record',   'stimulus_type' => 'text',        'preparation_time_sec_default' => 30, 'answer_time_sec_default' => 40,  'marks_default' => 5,  'typical_count_in_exam' => 6, 'display_order' => 1],
            ['section' => 'SPWR', 'name' => 'Repeat Sentence',            'tag' => 'RS',    'response_type' => 'audio_record',   'stimulus_type' => 'audio',       'preparation_time_sec_default' => 0,  'answer_time_sec_default' => 15,  'marks_default' => 3,  'typical_count_in_exam' => 10, 'display_order' => 2],
            ['section' => 'SPWR', 'name' => 'Describe Image',             'tag' => 'DI',    'response_type' => 'audio_record',   'stimulus_type' => 'image',       'preparation_time_sec_default' => 25, 'answer_time_sec_default' => 40,  'marks_default' => 5,  'typical_count_in_exam' => 6, 'display_order' => 3],
            ['section' => 'SPWR', 'name' => 'Re-tell Lecture',            'tag' => 'RL',    'response_type' => 'audio_record',   'stimulus_type' => 'audio_image', 'preparation_time_sec_default' => 10, 'answer_time_sec_default' => 40,  'marks_default' => 5,  'typical_count_in_exam' => 3, 'display_order' => 4],
            ['section' => 'SPWR', 'name' => 'Answer Short Question',      'tag' => 'ASQ',   'response_type' => 'audio_record',   'stimulus_type' => 'audio',       'preparation_time_sec_default' => 0,  'answer_time_sec_default' => 10,  'marks_default' => 1,  'typical_count_in_exam' => 10, 'display_order' => 5],
            ['section' => 'SPWR', 'name' => 'Summarize Written Text',     'tag' => 'SWT',   'response_type' => 'text_write',     'stimulus_type' => 'text',        'preparation_time_sec_default' => 0,  'answer_time_sec_default' => 600, 'marks_default' => 7,  'typical_count_in_exam' => 2, 'display_order' => 6],
            ['section' => 'SPWR', 'name' => 'Write Essay',                'tag' => 'WE',    'response_type' => 'text_write',     'stimulus_type' => 'text',        'preparation_time_sec_default' => 0,  'answer_time_sec_default' => 1200,'marks_default' => 15, 'typical_count_in_exam' => 1, 'display_order' => 7],

            // ── Reading (RD) ──────────────────────────────────────────────
            ['section' => 'RD',   'name' => 'Reading & Writing Fill in the Blanks', 'tag' => 'RWFIB', 'response_type' => 'fill_blank',     'stimulus_type' => 'text',  'preparation_time_sec_default' => 0, 'answer_time_sec_default' => null, 'marks_default' => 5, 'typical_count_in_exam' => 6, 'display_order' => 8],
            ['section' => 'RD',   'name' => 'Multiple Choice, Multiple Answers (R)', 'tag' => 'MCMAR', 'response_type' => 'multi_choice',   'stimulus_type' => 'text',  'preparation_time_sec_default' => 0, 'answer_time_sec_default' => null, 'marks_default' => 2, 'typical_count_in_exam' => 2, 'display_order' => 9],
            ['section' => 'RD',   'name' => 'Re-order Paragraphs',                  'tag' => 'ROP',   'response_type' => 'reorder',        'stimulus_type' => 'text',  'preparation_time_sec_default' => 0, 'answer_time_sec_default' => null, 'marks_default' => 3, 'typical_count_in_exam' => 3, 'display_order' => 10],
            ['section' => 'RD',   'name' => 'Reading Fill in the Blanks',           'tag' => 'RFIB',  'response_type' => 'fill_blank',     'stimulus_type' => 'text',  'preparation_time_sec_default' => 0, 'answer_time_sec_default' => null, 'marks_default' => 5, 'typical_count_in_exam' => 5, 'display_order' => 11],
            ['section' => 'RD',   'name' => 'Multiple Choice, Single Answer (R)',   'tag' => 'MCSAR', 'response_type' => 'single_choice',  'stimulus_type' => 'text',  'preparation_time_sec_default' => 0, 'answer_time_sec_default' => null, 'marks_default' => 1, 'typical_count_in_exam' => 2, 'display_order' => 12],

            // ── Listening (LS) ────────────────────────────────────────────
            ['section' => 'LS',   'name' => 'Summarize Spoken Text',                'tag' => 'SST',   'response_type' => 'text_write',     'stimulus_type' => 'audio', 'preparation_time_sec_default' => 0, 'answer_time_sec_default' => 600, 'marks_default' => 10, 'typical_count_in_exam' => 2, 'display_order' => 13],
            ['section' => 'LS',   'name' => 'Multiple Choice, Multiple Answers (L)','tag' => 'MCMAL', 'response_type' => 'multi_choice',   'stimulus_type' => 'audio', 'preparation_time_sec_default' => 0, 'answer_time_sec_default' => null, 'marks_default' => 2,  'typical_count_in_exam' => 2, 'display_order' => 14],
            ['section' => 'LS',   'name' => 'Listening Fill in the Blanks',         'tag' => 'LFIB',  'response_type' => 'fill_blank',     'stimulus_type' => 'audio', 'preparation_time_sec_default' => 0, 'answer_time_sec_default' => null, 'marks_default' => 7,  'typical_count_in_exam' => 3, 'display_order' => 15],
            ['section' => 'LS',   'name' => 'Highlight Correct Summary',            'tag' => 'HCS',   'response_type' => 'single_choice',  'stimulus_type' => 'audio', 'preparation_time_sec_default' => 0, 'answer_time_sec_default' => null, 'marks_default' => 1,  'typical_count_in_exam' => 2, 'display_order' => 16],
            ['section' => 'LS',   'name' => 'Multiple Choice, Single Answer (L)',   'tag' => 'MCSAL', 'response_type' => 'single_choice',  'stimulus_type' => 'audio', 'preparation_time_sec_default' => 0, 'answer_time_sec_default' => null, 'marks_default' => 1,  'typical_count_in_exam' => 2, 'display_order' => 17],
            ['section' => 'LS',   'name' => 'Select Missing Word',                  'tag' => 'SMW',   'response_type' => 'select_option',  'stimulus_type' => 'audio', 'preparation_time_sec_default' => 0, 'answer_time_sec_default' => null, 'marks_default' => 1,  'typical_count_in_exam' => 2, 'display_order' => 18],
            ['section' => 'LS',   'name' => 'Highlight Incorrect Words',            'tag' => 'HIW',   'response_type' => 'highlight_words','stimulus_type' => 'audio', 'preparation_time_sec_default' => 0, 'answer_time_sec_default' => null, 'marks_default' => 1,  'typical_count_in_exam' => 2, 'display_order' => 19],
            ['section' => 'LS',   'name' => 'Write from Dictation',                 'tag' => 'WFD',   'response_type' => 'fill_blank',     'stimulus_type' => 'audio', 'preparation_time_sec_default' => 0, 'answer_time_sec_default' => null, 'marks_default' => 3,  'typical_count_in_exam' => 3, 'display_order' => 20],
        ];

        foreach ($subTypes as $row) {
            $sectionTag = $row['section'];
            unset($row['section']);
            $row['pte_section_id'] = $sections[$sectionTag] ?? null;
            if (!$row['pte_section_id']) continue;

            PteQuestionSubType::updateOrCreate(['tag' => $row['tag']], $row);
        }
    }
}
