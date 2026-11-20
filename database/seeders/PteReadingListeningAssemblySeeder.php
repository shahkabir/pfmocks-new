<?php

namespace Database\Seeders;

use App\Models\Pte\PteModule;
use App\Models\Pte\PteQuestionGranular;
use App\Services\Pte\PteModuleWiseQuestionService;
use Illuminate\Database\Seeder;

/**
 * Assembles 6 Reading + 6 Listening questions into every PTE-enabled module,
 * covering the response types requested for those two sections:
 *   Reading   -> fill_blank, multi_choice, reorder, single_choice
 *   Listening -> fill_blank, multi_choice, single_choice, select_option, highlight_words
 *
 * Idempotent: relies on PteModuleWiseQuestionService::bulkCreate(), which
 * skips any (pte_module, question) pair that is already attached.
 */
class PteReadingListeningAssemblySeeder extends Seeder
{
    /** @var array<string,string[]> section tag => granular IDs to attach */
    private const PLAN = [
        'RD' => ['RWFIB0001', 'RWFIB0002', 'MCMAR0001', 'ROP0001', 'RFIB0001', 'MCSAR0001'],
        'LS' => ['LFIB0001', 'MCMAL0001', 'MCSAL0001', 'SMW0001', 'HIW0001', 'WFD0001'],
    ];

    public function run(): void
    {
        /** @var PteModuleWiseQuestionService $service */
        $service = app(PteModuleWiseQuestionService::class);

        $pteModules = PteModule::with('section:id,tag')->get()->keyBy(fn ($pm) => $pm->section?->tag);

        foreach (self::PLAN as $sectionTag => $granularIds) {
            $pteModule = $pteModules->get($sectionTag);
            if (!$pteModule) {
                $this->command?->warn("No pte_module mapped for section [{$sectionTag}] — skipping.");
                continue;
            }

            $questionIds = PteQuestionGranular::whereIn('question_granular_id', $granularIds)
                ->pluck('id')
                ->all();

            if (count($questionIds) !== count($granularIds)) {
                $this->command?->warn("Some granular IDs for [{$sectionTag}] were not found in the question bank.");
            }

            $created = $service->bulkCreate($pteModule->id, $questionIds);
            $this->command?->info("[{$sectionTag}] pte_module #{$pteModule->id}: {$created} new mapping(s) created.");
        }
    }
}
