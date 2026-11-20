<?php

namespace Database\Seeders\Duolingo;

use App\Models\Duolingo\DuolingoModule;
use App\Models\Duolingo\DuolingoModuleWiseQuestion;
use App\Models\Duolingo\DuolingoQuestion;
use App\Models\Duolingo\DuolingoQuestionGroup;
use App\Models\Duolingo\DuolingoQuestionType;
use App\Models\Duolingo\DuolingoSection;
use App\Models\Exam\Exam;
use App\Models\Module\Module;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

/**
 * Creates the free Duolingo mock exam with 3 sample questions per question type.
 *
 *   php artisan migrate --path=database/migrations/duolingo
 *   php artisan db:seed --class="Database\Seeders\Duolingo\DuolingoSampleSeeder"
 *
 * Idempotent: rows are matched on natural keys and updated in place. Missing
 * audio clips are synthesized with Windows speech synthesis; on other systems,
 * or when a clip already exists, nothing is generated.
 */
class DuolingoSampleSeeder extends Seeder
{
    private const AUDIO_DIR = 'data/audio/duolingo';
    private const IMAGE_DIR = 'data/image/duolingo';

    /** Timers the spec leaves as "task-specific" or "within the overall limit" (seconds). */
    private const VOCABULARY_SEC            = 30;
    private const LISTEN_THEN_SPEAK_SEC     = 90;
    private const SUMMARY_SEC               = 75;
    private const INTERACTIVE_READING_SEC   = 420;
    private const INTERACTIVE_LISTENING_SEC = 240;

    /** Interactive Writing stages (duolingo.md section 3). */
    private const WRITING_STAGE_1_SEC = 300;
    private const WRITING_STAGE_2_SEC = 180;

    /** @var array<string, DuolingoQuestionType> */
    private array $types = [];

    /** @var array<string, array<int, array{voice: string, text: string}>> file name => segments */
    private array $clips = [];

    /** @var array<string, array<int, int>> section tag => ordered question ids */
    private array $plan = ['R' => [], 'L' => [], 'W' => [], 'S' => []];

    public function run(): void
    {
        DB::transaction(function () {
            $sections = $this->seedCatalogue();

            $this->seedReading();
            $this->seedListening();
            $this->seedWriting();
            $this->seedSpeaking();

            $this->seedExamAndModule();

            foreach ($this->freeDuolingoModules() as $module) {
                $this->assemble($module, $sections);
                $this->command?->info("Duolingo: module #{$module->id} \"{$module->name}\" is free and assembled.");
            }
        });

        $this->generateAudio();

        $this->command?->info('Duolingo: ' . DuolingoQuestion::count() . ' questions across '
            . DuolingoQuestionType::count() . ' question types.');
    }

    /* ── Catalogue ──────────────────────────────────────────────────── */

    /** @return array<string, DuolingoSection> */
    private function seedCatalogue(): array
    {
        $sections = [];
        foreach (config('duolingo.sections') as $s) {
            $sections[$s['tag']] = DuolingoSection::updateOrCreate(
                ['tag' => $s['tag']],
                ['name' => $s['name'], 'display_order' => $s['display_order']],
            );
        }

        foreach (config('duolingo.question_types') as $i => $t) {
            $this->types[$t['tag']] = DuolingoQuestionType::updateOrCreate(
                ['tag' => $t['tag']],
                [
                    'duolingo_section_id'          => $sections[$t['section']]->id,
                    'name'                         => $t['name'],
                    'response_type'                => $t['response_type'],
                    'stimulus_type'                => $t['stimulus_type'],
                    'scoring_method'               => $t['scoring_method'],
                    'task_group'                   => $t['task_group'],
                    'preparation_time_sec_default' => $t['prep'],
                    'answer_time_sec_default'      => $t['answer'],
                    'max_replays_default'          => $t['replays'],
                    'turns_default'                => $t['turns'],
                    'instructions'                 => DuolingoSampleData::INSTRUCTIONS[$t['tag']],
                    'display_order'                => $i + 1,
                    'is_active'                    => true,
                ],
            );
        }

        return $sections;
    }

    /* ── Reading ────────────────────────────────────────────────────── */

    private function seedReading(): void
    {
        foreach (DuolingoSampleData::readAndComplete() as $i => $text) {
            $this->blankQuestion('R', 'RCT', $i + 1, $text);
        }

        foreach (DuolingoSampleData::readAndSelect() as $i => $w) {
            $this->question('R', 'RSL', $i + 1, ['question_text' => $w['word'], 'correct_ans' => $w['answer']]);
        }

        foreach (DuolingoSampleData::interactiveReading() as $i => $p) {
            $n     = $i + 1;
            $group = $this->group(DuolingoQuestionGroup::INTERACTIVE_READING, $p['title'], [
                'passage_text'   => $p['passage'],
                'time_limit_sec' => self::INTERACTIVE_READING_SEC,
            ]);

            $this->mcq('R', 'IRCS', $n, $p['sentence'], $group, 1, [
                'question_text' => $p['sentence_q'],
                'config'        => ['passage_override' => $p['partial']],
            ]);
            $this->mcq('R', 'IRCP', $n, $p['gap'], $group, 2, [
                'question_text' => 'Select the sentence that best fills the gap.',
                'config'        => ['passage_override' => $p['gapped']],
            ]);

            $highlight = $this->question('R', 'IRHA', $n, [
                'question_text'              => $p['highlight_q'],
                'correct_ans'                => $p['highlight'],
                'duolingo_question_group_id' => $group->id,
                'group_order'                => 3,
            ]);
            $start = strpos($p['passage'], $p['highlight']);
            $highlight->highlights()->delete();
            $highlight->highlights()->create([
                'start_offset' => $start,
                'end_offset'   => $start + strlen($p['highlight']),
                'answer_text'  => $p['highlight'],
            ]);

            $this->mcq('R', 'IRII', $n, $p['idea'], $group, 4, ['question_text' => $p['idea_q']]);
            $this->mcq('R', 'IRTP', $n, $p['titles'], $group, 5, ['question_text' => 'Which title best fits the passage?']);
        }

        foreach (DuolingoSampleData::vocabularyInContext() as $i => $text) {
            $this->blankQuestion('R', 'VIC', $i + 1, $text, ['answer_time_sec' => self::VOCABULARY_SEC]);
        }
    }

    /* ── Listening ──────────────────────────────────────────────────── */

    private function seedListening(): void
    {
        foreach (DuolingoSampleData::listenAndSelect() as $i => $w) {
            $n = $i + 1;
            $this->question('L', 'LSL', $n, [
                'question_text' => $w['word'],
                'correct_ans'   => $w['answer'],
                'audio_url'     => $this->clip("lsl{$n}", [['F', $w['word']]]),
                'max_replays'   => 1,
            ]);
        }

        foreach (DuolingoSampleData::listenAndType() as $i => [$sentence, $variants]) {
            $n = $i + 1;
            $this->question('L', 'LTY', $n, [
                'correct_ans'       => $sentence,
                'accepted_variants' => $variants ?: null,
                'audio_transcript'  => $sentence,
                'audio_url'         => $this->clip("lty{$n}", [['F', $sentence]]),
            ]);
        }

        foreach (DuolingoSampleData::interactiveListening() as $i => $c) {
            $n     = $i + 1;
            $group = $this->group(DuolingoQuestionGroup::INTERACTIVE_LISTENING, $c['title'], [
                'scenario'       => $c['scenario'],
                'time_limit_sec' => self::INTERACTIVE_LISTENING_SEC,
            ]);

            $this->mcq('L', 'ILLR', $n, $c['respond'], $group, 1, [
                'question_text'    => 'Select the best response.',
                'audio_url'        => $this->clip("illr{$n}", $c['respond_audio']),
                'audio_transcript' => $this->transcript($c['respond_audio']),
                'max_replays'      => 1,
            ]);
            $this->mcq('L', 'ILLC', $n, $c['complete'], $group, 2, [
                'question_text'    => $c['complete_q'],
                'audio_url'        => $this->clip("illc{$n}", $c['complete_audio']),
                'audio_transcript' => $this->transcript($c['complete_audio']),
                'max_replays'      => 1,
            ]);
            $this->question('L', 'ILSC', $n, [
                'question_text'              => 'Summarize the conversation in your own words.',
                'answer_time_sec'            => self::SUMMARY_SEC,
                'duolingo_question_group_id' => $group->id,
                'group_order'                => 3,
            ]);
        }
    }

    /* ── Writing ────────────────────────────────────────────────────── */

    private function seedWriting(): void
    {
        foreach (DuolingoSampleData::writePhotos() as $i => $photo) {
            $this->question('W', 'WAP', $i + 1, [
                'image_url'      => self::IMAGE_DIR . '/' . $photo['image'],
                'image_alt_text' => $photo['alt'],
            ]);
        }

        foreach (DuolingoSampleData::interactiveWriting() as $i => [$initial, $followUp]) {
            $q = $this->question('W', 'IWR', $i + 1, ['question_text' => $initial]);
            $this->turns($q, [
                ['turn_order' => 1, 'prompt_text' => $initial,  'response_time_sec' => self::WRITING_STAGE_1_SEC],
                ['turn_order' => 2, 'prompt_text' => $followUp, 'response_time_sec' => self::WRITING_STAGE_2_SEC],
            ]);
        }

        foreach (DuolingoSampleData::writingSamples() as $i => $prompt) {
            $this->question('W', 'WSM', $i + 1, ['question_text' => $prompt]);
        }
    }

    /* ── Speaking ───────────────────────────────────────────────────── */

    private function seedSpeaking(): void
    {
        foreach (DuolingoSampleData::speakPhotos() as $i => $photo) {
            $this->question('S', 'SAP', $i + 1, [
                'image_url'      => self::IMAGE_DIR . '/' . $photo['image'],
                'image_alt_text' => $photo['alt'],
            ]);
        }

        foreach (DuolingoSampleData::readThenSpeak() as $i => $prompt) {
            $this->question('S', 'RTS', $i + 1, ['question_text' => $prompt]);
        }

        foreach (DuolingoSampleData::listenThenSpeak() as $i => $segments) {
            $n = $i + 1;
            $this->question('S', 'LTS', $n, [
                'answer_time_sec'  => self::LISTEN_THEN_SPEAK_SEC,
                'audio_url'        => $this->clip("lts{$n}", $segments),
                'audio_transcript' => $this->transcript($segments),
                'max_replays'      => 1,
            ]);
        }

        foreach (DuolingoSampleData::interactiveSpeaking() as $i => $s) {
            $n = $i + 1;
            $q = $this->question('S', 'ISP', $n, ['question_text' => $s['intro']]);

            $turns = [];
            foreach ($s['turns'] as $t => $text) {
                $order   = $t + 1;
                $turns[] = [
                    'turn_order'        => $order,
                    'speaker'           => 'Interviewer',
                    'prompt_text'       => $text,
                    'audio_url'         => $this->clip("isp{$n}_t{$order}", [[$s['voice'], $text]]),
                    'response_time_sec' => (int) $this->types['ISP']->answer_time_sec_default,
                ];
            }
            $this->turns($q, $turns);
        }

        foreach (DuolingoSampleData::speakingSamples() as $i => $prompt) {
            $this->question('S', 'SSM', $i + 1, ['question_text' => $prompt]);
        }
    }

    /* ── Exam, module and assembly ──────────────────────────────────── */

    private function seedExamAndModule(): Module
    {
        $exam = Exam::updateOrCreate(
            ['tag' => 'duolingo', 'name' => 'Duolingo English Test - Free Mock'],
            [
                'exam_information'  => 'A free practice test covering every Duolingo English Test question type. '
                    . 'Scores are PerfectMocks estimates, not official Duolingo scores.',
                'is_active'         => true,
                'is_public_visible' => false,
            ],
        );

        return Module::updateOrCreate(
            ['exam_id' => $exam->id, 'name' => 'Duolingo Free Mock Test 1'],
            [
                'module_type'        => DuolingoModule::MODULE_TYPE,
                'module_information' => 'All 21 question types with 3 sample questions each.',
                'type'               => 'free',
                'price_in_bdt'       => 0,
                'price_in_usd'       => 0,
                'duration_minutes'   => 0, // Set from the assembled timers in assemble().
            ],
        );
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

    /** @param array<string, DuolingoSection> $sections */
    private function assemble(Module $module, array $sections): void
    {
        $totalMinutes = 0;

        foreach ($this->plan as $tag => $questionIds) {
            $bridge = DuolingoModule::firstOrCreate([
                'module_id'           => $module->id,
                'duolingo_section_id' => $sections[$tag]->id,
            ]);

            foreach (array_values($questionIds) as $i => $questionId) {
                DuolingoModuleWiseQuestion::updateOrCreate(
                    ['duolingo_module_id' => $bridge->id, 'display_order' => $i + 1],
                    ['duolingo_question_id' => $questionId, 'is_active' => true],
                );
            }

            $minutes = $this->sectionMinutes($questionIds);
            $sections[$tag]->update(['time_allowed_minutes' => $minutes]);
            $totalMinutes += $minutes;
        }

        $module->update(['duration_minutes' => $totalMinutes]);
    }

    /** @param array<int, int> $questionIds */
    private function sectionMinutes(array $questionIds): int
    {
        $questions = DuolingoQuestion::with(['type', 'group', 'turns'])->whereIn('id', $questionIds)->get();

        $seconds = 0;
        $groups  = [];
        foreach ($questions as $q) {
            if ($q->type->response_type === 'interactive_writing' || $q->type->response_type === 'interactive_speaking') {
                $seconds += $q->turns->sum('response_time_sec');
            } elseif ($q->answerTimeSec()) {
                $seconds += $q->answerTimeSec();
            } elseif ($q->group) {
                $groups[$q->group->id] = (int) $q->group->time_limit_sec;
            }
        }

        return (int) ceil(($seconds + array_sum($groups)) / 60);
    }

    /* ── Builders ───────────────────────────────────────────────────── */

    private function question(string $section, string $tag, int $n, array $attributes): DuolingoQuestion
    {
        $question = DuolingoQuestion::updateOrCreate(
            ['question_code' => sprintf('%s%02d', $tag, $n)],
            array_merge([
                'duolingo_question_type_id' => $this->types[$tag]->id,
                'marks'                     => 1,
                'difficulty'                => 'medium',
                'source_reference'          => 'PerfectMocks sample',
                'is_active'                 => true,
            ], $attributes),
        );

        $this->plan[$section][] = $question->id;

        return $question;
    }

    /** A question whose text contains "[prefix|answer]" blanks. Each blank earns one mark. */
    private function blankQuestion(string $section, string $tag, int $n, string $markup, array $attributes = []): DuolingoQuestion
    {
        $blanks = [];
        $text   = preg_replace_callback('/\[([^|\]]*)\|([^\]]+)\]/', function (array $m) use (&$blanks) {
            $blanks[] = ['visible_prefix' => $m[1] === '' ? null : $m[1], 'correct_answer' => $m[2]];
            return '{' . count($blanks) . '}';
        }, $markup);

        $question = $this->question($section, $tag, $n, array_merge($attributes, [
            'question_text' => $text,
            'marks'         => count($blanks),
        ]));

        $question->blanks()->delete();
        foreach ($blanks as $i => $blank) {
            $question->blanks()->create($blank + ['blank_order' => $i + 1]);
        }

        return $question;
    }

    /** A single-choice question. The first entry of $options is the correct one. */
    private function mcq(string $section, string $tag, int $n, array $options, DuolingoQuestionGroup $group, int $groupOrder, array $attributes): DuolingoQuestion
    {
        $question = $this->question($section, $tag, $n, array_merge($attributes, [
            'duolingo_question_group_id' => $group->id,
            'group_order'                => $groupOrder,
        ]));

        // Rotate deterministically so the correct option moves between questions.
        $correctAt = ($n + $groupOrder) % count($options);
        $ordered   = array_slice($options, 1);
        array_splice($ordered, $correctAt, 0, [$options[0]]);

        $question->options()->delete();
        foreach ($ordered as $i => $text) {
            $question->options()->create([
                'option_text'   => $text,
                'is_correct'    => $text === $options[0],
                'display_order' => $i + 1,
            ]);
        }

        return $question;
    }

    private function group(string $taskGroup, string $title, array $attributes): DuolingoQuestionGroup
    {
        return DuolingoQuestionGroup::updateOrCreate(
            ['task_group' => $taskGroup, 'title' => $title],
            array_merge(['is_active' => true], $attributes),
        );
    }

    private function turns(DuolingoQuestion $question, array $turns): void
    {
        $question->turns()->delete();
        foreach ($turns as $turn) {
            $question->turns()->create($turn);
        }
    }

    /* ── Audio ──────────────────────────────────────────────────────── */

    /** Register a clip for synthesis and return its public path. */
    private function clip(string $name, array $segments): string
    {
        $this->clips[$name] = array_map(fn ($s) => ['voice' => $s[0], 'text' => $s[1]], $segments);

        return self::AUDIO_DIR . "/{$name}.wav";
    }

    private function transcript(array $segments): string
    {
        return implode("\n", array_column($segments, 1));
    }

    private function generateAudio(): void
    {
        $dir = public_path(self::AUDIO_DIR);
        File::ensureDirectoryExists($dir);

        $missing = [];
        foreach ($this->clips as $name => $segments) {
            if (!file_exists("{$dir}/{$name}.wav")) {
                $missing[] = ['path' => "{$dir}\\{$name}.wav", 'segments' => $segments];
            }
        }

        if ($missing === []) {
            $this->command?->info('Duolingo audio: all ' . count($this->clips) . ' clips already exist.');
            return;
        }

        if (PHP_OS_FAMILY !== 'Windows') {
            $this->command?->warn('Duolingo audio: ' . count($missing) . ' clip(s) are missing. Copy them from a machine that has them.');
            return;
        }

        $work     = storage_path('app/duolingo-tts');
        File::ensureDirectoryExists($work);
        $manifest = "{$work}/manifest.json";
        $script   = "{$work}/synthesize.ps1";

        file_put_contents($manifest, json_encode($missing, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        file_put_contents($script, <<<'PS1'
param([string]$Manifest)
$ErrorActionPreference = 'Stop'
Add-Type -AssemblyName System.Speech
$voices = @{ F = 'Microsoft Zira Desktop'; M = 'Microsoft David Desktop' }
$format = New-Object System.Speech.AudioFormat.SpeechAudioFormatInfo(
    16000,
    [System.Speech.AudioFormat.AudioBitsPerSample]::Sixteen,
    [System.Speech.AudioFormat.AudioChannel]::Mono)
$clips = Get-Content -Raw -Encoding UTF8 $Manifest | ConvertFrom-Json
foreach ($clip in $clips) {
    $speaker = New-Object System.Speech.Synthesis.SpeechSynthesizer
    $speaker.Rate = -1
    $speaker.SetOutputToWaveFile($clip.path, $format)
    foreach ($segment in $clip.segments) {
        $speaker.SelectVoice($voices[$segment.voice])
        $speaker.Speak($segment.text)
    }
    $speaker.Dispose()
}
PS1);

        exec('powershell -NoProfile -ExecutionPolicy Bypass -File ' . escapeshellarg($script)
            . ' -Manifest ' . escapeshellarg($manifest) . ' 2>&1', $output, $code);

        File::deleteDirectory($work);

        if ($code !== 0) {
            $this->command?->error('Duolingo audio synthesis failed: ' . implode(' ', $output));
            return;
        }

        $this->command?->info('Duolingo audio: generated ' . count($missing) . ' clip(s).');
    }
}
