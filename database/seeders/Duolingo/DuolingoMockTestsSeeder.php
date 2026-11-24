<?php

namespace Database\Seeders\Duolingo;

use App\Models\Duolingo\DuolingoQuestion;
use Database\Seeders\Duolingo\Concerns\BuildsDuolingoTests;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Creates four more free Duolingo exams (Mock Tests 2–5), each with its own questions
 * and a different number of questions per section. Content: DuolingoMockTestsData.
 *
 *   php artisan migrate --path=database/migrations/duolingo
 *   php artisan db:seed --class="Database\Seeders\Duolingo\DuolingoMockTestsSeeder"
 *
 * Idempotent, like DuolingoSampleSeeder. Question codes and audio files are prefixed
 * per test (T2RCT01, t2_lsl1.wav, …) so tests never share or overwrite each other's rows.
 */
class DuolingoMockTestsSeeder extends Seeder
{
    use BuildsDuolingoTests;

    public function run(): void
    {
        DB::transaction(function () {
            $this->seedCatalogue();

            foreach (DuolingoMockTestsData::tests() as $test) {
                $n = $test['number'];
                $this->buildTest("T{$n}", $test['content']);

                $module = $this->freeExamModule(
                    "Duolingo English Test - Free Mock {$n}",
                    "Duolingo Free Mock Test {$n}",
                    'A free Duolingo English Test practice exam with reading, listening, writing and speaking. '
                        . 'Scores are PerfectMocks estimates, not official Duolingo scores.',
                    'Original practice questions across all four skills.',
                );
                $this->assemble($module);

                $this->command?->info("Duolingo: Mock Test {$n} (module #{$module->id}) is free with "
                    . array_sum(array_map('count', $this->plan)) . " questions ({$this->planSummary()}), {$module->fresh()->duration_minutes} min.");
            }
        });

        // Audio clips are named per test: t2_lsl1.wav and so on.
        $this->generateAudio();

        $this->command?->info('Duolingo: ' . DuolingoQuestion::count() . ' questions in the bank.');
    }
}
