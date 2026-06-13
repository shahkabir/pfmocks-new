<?php

namespace Database\Seeders;

use App\Models\Pte\PteSection;
use Illuminate\Database\Seeder;

class PteSectionSeeder extends Seeder
{
    public function run(): void
    {
        $sections = [
            ['name' => 'Speaking & Writing', 'tag' => 'SPWR', 'display_order' => 1, 'time_allowed_minutes' => 77, 'description' => 'Speaking and writing tasks: Read Aloud, Repeat Sentence, Describe Image, Re-tell Lecture, Answer Short Question, Summarize Written Text, Write Essay.'],
            ['name' => 'Reading',            'tag' => 'RD',   'display_order' => 2, 'time_allowed_minutes' => 41, 'description' => 'Reading tasks: Fill in Blanks, MCQ, Re-order Paragraphs.'],
            ['name' => 'Listening',          'tag' => 'LS',   'display_order' => 3, 'time_allowed_minutes' => 57, 'description' => 'Listening tasks: Summarize Spoken Text, MCQ, Fill in Blanks, Highlight Correct Summary, Select Missing Word, Highlight Incorrect Words, Write from Dictation.'],
        ];

        foreach ($sections as $s) {
            PteSection::updateOrCreate(['tag' => $s['tag']], $s);
        }
    }
}
