<?php

namespace Database\Seeders\Duolingo;

use App\Models\Duolingo\DuolingoModule;
use App\Models\Duolingo\DuolingoModuleWiseQuestion;
use App\Models\Duolingo\DuolingoQuestion;
use App\Models\Duolingo\DuolingoQuestionType;
use App\Models\Exam\Exam;
use App\Models\Module\Module;
use Database\Seeders\Duolingo\Concerns\BuildsDuolingoTests;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Creates the free Duolingo mock exam (Test 1) with 3 sample questions per question type.
 *
 *   php artisan migrate --path=database/migrations/duolingo
 *   php artisan db:seed --class="Database\Seeders\Duolingo\DuolingoSampleSeeder"
 *
 * Every Duolingo exam is made free. Test 1's questions also go into any Duolingo
 * module that has no questions yet; modules with their own content are left alone.
 *
 * Idempotent: rows are matched on natural keys and updated in place. Missing
 * audio clips are synthesized with Windows speech synthesis; on other systems,
 * or when a clip already exists, nothing is generated.
 */
class DuolingoSampleSeeder extends Seeder
{
    use BuildsDuolingoTests;

    public function run(): void
    {
        DB::transaction(function () {
            $this->seedCatalogue();

            $this->buildTest('', [
                'rct' => DuolingoSampleData::readAndComplete(),
                'rsl' => DuolingoSampleData::readAndSelect(),
                'ir'  => DuolingoSampleData::interactiveReading(),
                'vic' => DuolingoSampleData::vocabularyInContext(),
                'lsl' => DuolingoSampleData::listenAndSelect(),
                'lty' => DuolingoSampleData::listenAndType(),
                'il'  => DuolingoSampleData::interactiveListening(),
                'wap' => DuolingoSampleData::writePhotos(),
                'iwr' => DuolingoSampleData::interactiveWriting(),
                'wsm' => DuolingoSampleData::writingSamples(),
                'sap' => DuolingoSampleData::speakPhotos(),
                'rts' => DuolingoSampleData::readThenSpeak(),
                'lts' => DuolingoSampleData::listenThenSpeak(),
                'isp' => DuolingoSampleData::interactiveSpeaking(),
                'ssm' => DuolingoSampleData::speakingSamples(),
            ]);

            $testOne = $this->freeExamModule(
                'Duolingo English Test - Free Mock',
                'Duolingo Free Mock Test 1',
                'A free practice test covering every Duolingo English Test question type. '
                    . 'Scores are PerfectMocks estimates, not official Duolingo scores.',
                'All 21 question types with 3 sample questions each.',
            );

            foreach ($this->freeDuolingoModules() as $module) {
                if ($module->id === $testOne->id || !$this->hasQuestions($module)) {
                    $this->assemble($module);
                    $this->command?->info("Duolingo: module #{$module->id} \"{$module->name}\" assembled with Test 1 ({$this->planSummary()}).");
                }
            }
        });

        $this->generateAudio();

        $this->command?->info('Duolingo: ' . DuolingoQuestion::count() . ' questions across '
            . DuolingoQuestionType::count() . ' question types.');
    }

    /**
     * Every module of every Duolingo exam becomes free, opens in the Duolingo player
     * and is listed for all students. Free modules need no per-student purchase row:
     * the exam listing shows Start to every student.
     *
     * @return \Illuminate\Support\Collection<int, Module>
     */
    private function freeDuolingoModules()
    {
        $exams = Exam::with('modules')->where('tag', 'duolingo')->get();

        foreach ($exams as $exam) {
            $exam->update(['is_active' => true]);

            foreach ($exam->modules as $module) {
                $module->update([
                    'module_type'  => DuolingoModule::MODULE_TYPE,
                    'type'         => 'free',
                    'price_in_bdt' => 0,
                    'price_in_usd' => 0,
                ]);
            }
        }

        return $exams->flatMap->modules;
    }

    private function hasQuestions(Module $module): bool
    {
        return DuolingoModuleWiseQuestion::whereHas('duolingoModule', fn ($q) => $q->where('module_id', $module->id))->exists();
    }
}
