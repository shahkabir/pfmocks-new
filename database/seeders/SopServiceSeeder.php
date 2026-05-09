<?php

namespace Database\Seeders;

use App\Models\Exam\Exam;
use App\Models\Module\Module;
use App\Models\Question\Question;
use Illuminate\Database\Seeder;

/**
 * One-time setup of the SOP service.
 * Creates an Exam ("Statement of Purpose Service"), two Modules (Review @ 1000 BDT,
 * New @ 2000 BDT), and a placeholder Question for each so the existing
 * payment + evaluation pipeline picks them up.
 *
 * Run once:  D:/php-8.2/php.exe artisan db:seed --class=SopServiceSeeder
 */
class SopServiceSeeder extends Seeder
{
    public function run(): void
    {
        $exam = Exam::firstOrCreate(
            ['tag' => 'sop'],
            [
                'name'      => 'Statement of Purpose Service',
                'is_active' => true,
            ]
        );

        $modules = [
            [
                'name'             => 'SOP Review',
                'module_type'      => 'sop_review',
                'type'             => 'paid',
                'price_in_bdt'     => 1000,
                'price_in_usd'     => 10,
                'duration_minutes' => 0,
            ],
            [
                'name'             => 'New SOP Writing',
                'module_type'      => 'sop_new',
                'type'             => 'paid',
                'price_in_bdt'     => 2000,
                'price_in_usd'     => 20,
                'duration_minutes' => 0,
            ],
        ];

        foreach ($modules as $m) {
            $module = Module::firstOrCreate(
                ['exam_id' => $exam->id, 'module_type' => $m['module_type']],
                $m
            );

            // Placeholder question — required so existing exam-attempt machinery
            // has something to attach to. Students never see this.
            Question::firstOrCreate(
                ['module_id' => $module->id, 'sort_order' => 1],
                [
                    'type'                => 'essay',
                    'question_header'     => $m['name'],
                    'passage_instruction' => 'Filled by the SOP submission form (résumé, university, country).',
                    'passage'             => null,
                    'marks'               => 0,
                ]
            );
        }

        $this->command?->info('SOP service exam + modules seeded.');
    }
}
