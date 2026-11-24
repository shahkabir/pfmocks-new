<?php

namespace Database\Seeders\Duolingo\Concerns;

use App\Models\Duolingo\DuolingoModule;
use App\Models\Duolingo\DuolingoModuleWiseQuestion;
use App\Models\Duolingo\DuolingoQuestion;
use App\Models\Duolingo\DuolingoQuestionGroup;
use App\Models\Duolingo\DuolingoQuestionType;
use App\Models\Duolingo\DuolingoSection;
use App\Models\Exam\Exam;
use App\Models\Module\Module;
use Database\Seeders\Duolingo\DuolingoSampleData;
use Illuminate\Support\Facades\File;

/**
 * Shared building blocks for Duolingo seeders: the question-type catalogue, question
 * builders, test assembly and audio synthesis.
 *
 * Test content is an array keyed by question type, in the shapes used by DuolingoSampleData:
 *   rct, vic: "[prefix|answer]" markup (answer may list variants: "[|turn/switch]")
 *   rsl, lsl: ['word' => ..., 'answer' => 'yes'|'no']
 *   ir:  passage arrays (see DuolingoSampleData::interactiveReading)
 *   lty: [sentence, [accepted variants]]
 *   il:  conversation arrays (see DuolingoSampleData::interactiveListening)
 *   wap, sap: ['image' => file, 'alt' => text]
 *   iwr: [initial prompt, follow-up prompt]
 *   wsm, rts, ssm: prompt strings
 *   lts: [[voice, text], ...]
 *   isp: ['intro' => ..., 'voice' => 'F'|'M', 'turns' => [...]]
 */
trait BuildsDuolingoTests
{
    protected const AUDIO_DIR = 'data/audio/duolingo';
    protected const IMAGE_DIR = 'data/image/duolingo';

    /** Timers the spec leaves as "task-specific" or "within the overall limit" (seconds). */
    protected const VOCABULARY_SEC            = 30;
    protected const LISTEN_THEN_SPEAK_SEC     = 90;
    protected const SUMMARY_SEC               = 75;
    protected const INTERACTIVE_READING_SEC   = 420;
    protected const INTERACTIVE_LISTENING_SEC = 240;

    /** Interactive Writing stages (duolingo.md section 3). */
    protected const WRITING_STAGE_1_SEC = 300;
    protected const WRITING_STAGE_2_SEC = 180;

    /** @var array<string, DuolingoQuestionType> */
    private array $types = [];

    /** @var array<string, DuolingoSection> */
    private array $sections = [];

    /** @var array<string, array<int, array{voice: string, text: string}>> file name => segments */
    private array $clips = [];

    /** @var array<string, array<int, int>> section tag => ordered question ids of the test being built */
    private array $plan = [];

    /** Prefix for question codes and audio files, so each test's questions stay distinct. */
    private string $prefix = '';

    /* ── Catalogue ──────────────────────────────────────────────────── */

    private function seedCatalogue(): void
    {
        foreach (config('duolingo.sections') as $s) {
            $this->sections[$s['tag']] = DuolingoSection::updateOrCreate(
                ['tag' => $s['tag']],
                ['name' => $s['name'], 'display_order' => $s['display_order']],
            );
        }

        foreach (config('duolingo.question_types') as $i => $t) {
            $this->types[$t['tag']] = DuolingoQuestionType::updateOrCreate(
                ['tag' => $t['tag']],
                [
                    'duolingo_section_id'          => $this->sections[$t['section']]->id,
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
    }

    /* ── Test content ───────────────────────────────────────────────── */

    /**
     * Creates or updates one test's questions, in section order R, L, W, S.
     * Afterwards $this->plan holds the question ids to assemble into a module.
     */
    private function buildTest(string $prefix, array $c): void
    {
        $this->prefix = $prefix;
        $this->plan   = ['R' => [], 'L' => [], 'W' => [], 'S' => []];

        $this->buildReading($c);
        $this->buildListening($c);
        $this->buildWriting($c);
        $this->buildSpeaking($c);
    }

    private function buildReading(array $c): void
    {
        foreach ($c['rct'] ?? [] as $i => $text) {
            $this->blankQuestion('R', 'RCT', $i + 1, $text);
        }

        foreach ($c['rsl'] ?? [] as $i => $w) {
            $this->question('R', 'RSL', $i + 1, ['question_text' => $w['word'], 'correct_ans' => $w['answer']]);
        }

        foreach ($c['ir'] ?? [] as $i => $p) {
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

            $start = strpos($p['passage'], $p['highlight']);
            if ($start === false) {
                throw new \RuntimeException("Highlight answer is not in the passage: {$p['title']}");
            }
            $highlight = $this->question('R', 'IRHA', $n, [
                'question_text'              => $p['highlight_q'],
                'correct_ans'                => $p['highlight'],
                'duolingo_question_group_id' => $group->id,
                'group_order'                => 3,
            ]);
            $highlight->highlights()->delete();
            $highlight->highlights()->create([
                'start_offset' => $start,
                'end_offset'   => $start + strlen($p['highlight']),
                'answer_text'  => $p['highlight'],
            ]);

            $this->mcq('R', 'IRII', $n, $p['idea'], $group, 4, ['question_text' => $p['idea_q']]);
            $this->mcq('R', 'IRTP', $n, $p['titles'], $group, 5, ['question_text' => 'Which title best fits the passage?']);
        }

        foreach ($c['vic'] ?? [] as $i => $text) {
            $this->blankQuestion('R', 'VIC', $i + 1, $text, ['answer_time_sec' => self::VOCABULARY_SEC]);
        }
    }

    private function buildListening(array $c): void
    {
        foreach ($c['lsl'] ?? [] as $i => $w) {
            $n = $i + 1;
            $this->question('L', 'LSL', $n, [
                'question_text' => $w['word'],
                'correct_ans'   => $w['answer'],
                'audio_url'     => $this->clip("lsl{$n}", [['F', $w['word']]]),
                'max_replays'   => 1,
            ]);
        }

        foreach ($c['lty'] ?? [] as $i => [$sentence, $variants]) {
            $n = $i + 1;
            $this->question('L', 'LTY', $n, [
                'correct_ans'       => $sentence,
                'accepted_variants' => $variants ?: null,
                'audio_transcript'  => $sentence,
                'audio_url'         => $this->clip("lty{$n}", [['F', $sentence]]),
            ]);
        }

        foreach ($c['il'] ?? [] as $i => $conv) {
            $n     = $i + 1;
            $group = $this->group(DuolingoQuestionGroup::INTERACTIVE_LISTENING, $conv['title'], [
                'scenario'       => $conv['scenario'],
                'time_limit_sec' => self::INTERACTIVE_LISTENING_SEC,
            ]);

            $this->mcq('L', 'ILLR', $n, $conv['respond'], $group, 1, [
                'question_text'    => 'Select the best response.',
                'audio_url'        => $this->clip("illr{$n}", $conv['respond_audio']),
                'audio_transcript' => $this->transcript($conv['respond_audio']),
                'max_replays'      => 1,
            ]);
            $this->mcq('L', 'ILLC', $n, $conv['complete'], $group, 2, [
                'question_text'    => $conv['complete_q'],
                'audio_url'        => $this->clip("illc{$n}", $conv['complete_audio']),
                'audio_transcript' => $this->transcript($conv['complete_audio']),
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

    private function buildWriting(array $c): void
    {
        foreach ($c['wap'] ?? [] as $i => $photo) {
            $this->question('W', 'WAP', $i + 1, [
                'image_url'      => self::IMAGE_DIR . '/' . $photo['image'],
                'image_alt_text' => $photo['alt'],
            ]);
        }

        foreach ($c['iwr'] ?? [] as $i => [$initial, $followUp]) {
            $q = $this->question('W', 'IWR', $i + 1, ['question_text' => $initial]);
            $this->turns($q, [
                ['turn_order' => 1, 'prompt_text' => $initial,  'response_time_sec' => self::WRITING_STAGE_1_SEC],
                ['turn_order' => 2, 'prompt_text' => $followUp, 'response_time_sec' => self::WRITING_STAGE_2_SEC],
            ]);
        }

        foreach ($c['wsm'] ?? [] as $i => $prompt) {
            $this->question('W', 'WSM', $i + 1, ['question_text' => $prompt]);
        }
    }

    private function buildSpeaking(array $c): void
    {
        foreach ($c['sap'] ?? [] as $i => $photo) {
            $this->question('S', 'SAP', $i + 1, [
                'image_url'      => self::IMAGE_DIR . '/' . $photo['image'],
                'image_alt_text' => $photo['alt'],
            ]);
        }

        foreach ($c['rts'] ?? [] as $i => $prompt) {
            $this->question('S', 'RTS', $i + 1, ['question_text' => $prompt]);
        }

        foreach ($c['lts'] ?? [] as $i => $segments) {
            $n = $i + 1;
            $this->question('S', 'LTS', $n, [
                'answer_time_sec'  => self::LISTEN_THEN_SPEAK_SEC,
                'audio_url'        => $this->clip("lts{$n}", $segments),
                'audio_transcript' => $this->transcript($segments),
                'max_replays'      => 1,
            ]);
        }

        foreach ($c['isp'] ?? [] as $i => $s) {
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

        foreach ($c['ssm'] ?? [] as $i => $prompt) {
            $this->question('S', 'SSM', $i + 1, ['question_text' => $prompt]);
        }
    }

    /* ── Exam, module and assembly ──────────────────────────────────── */

    /** A free Duolingo exam with one module that opens in the Duolingo player. */
    private function freeExamModule(string $examName, string $moduleName, string $examInfo, string $moduleInfo): Module
    {
        $exam = Exam::updateOrCreate(
            ['tag' => 'duolingo', 'name' => $examName],
            ['exam_information' => $examInfo, 'is_active' => true, 'is_public_visible' => false],
        );

        return Module::updateOrCreate(
            ['exam_id' => $exam->id, 'name' => $moduleName],
            [
                'module_type'        => DuolingoModule::MODULE_TYPE,
                'module_information' => $moduleInfo,
                'type'               => 'free',
                'price_in_bdt'       => 0,
                'price_in_usd'       => 0,
                'duration_minutes'   => 0, // Set from the assembled timers in assemble().
            ],
        );
    }

    /**
     * Places the questions in $this->plan into the module, in order, and sets the
     * section and module times from the question timers. Positions beyond the new
     * question count are removed, so a test that shrinks on re-seeding stays exact.
     */
    private function assemble(Module $module): void
    {
        $totalMinutes = 0;

        foreach ($this->plan as $tag => $questionIds) {
            $minutes = $this->sectionMinutes($questionIds);

            $bridge = DuolingoModule::updateOrCreate(
                ['module_id' => $module->id, 'duolingo_section_id' => $this->sections[$tag]->id],
                ['time_allowed_minutes' => $minutes],
            );

            foreach (array_values($questionIds) as $i => $questionId) {
                DuolingoModuleWiseQuestion::updateOrCreate(
                    ['duolingo_module_id' => $bridge->id, 'display_order' => $i + 1],
                    ['duolingo_question_id' => $questionId, 'is_active' => true],
                );
            }

            DuolingoModuleWiseQuestion::where('duolingo_module_id', $bridge->id)
                ->where('display_order', '>', count($questionIds))
                ->delete();

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

    /** Question counts per section of the test just built, for log output. */
    private function planSummary(): string
    {
        return collect($this->plan)->map(fn ($ids, $tag) => "{$tag}=" . count($ids))->implode(' ');
    }

    /* ── Builders ───────────────────────────────────────────────────── */

    private function question(string $section, string $tag, int $n, array $attributes): DuolingoQuestion
    {
        $question = DuolingoQuestion::updateOrCreate(
            ['question_code' => sprintf('%s%s%02d', $this->prefix, $tag, $n)],
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

    /**
     * A question whose text contains "[prefix|answer]" blanks. Each blank earns one mark.
     * "[|turn/switch]" accepts "turn" or "switch".
     */
    private function blankQuestion(string $section, string $tag, int $n, string $markup, array $attributes = []): DuolingoQuestion
    {
        $blanks = [];
        $text   = preg_replace_callback('/\[([^|\]]*)\|([^\]]+)\]/', function (array $m) use (&$blanks) {
            $answers  = explode('/', $m[2]);
            $blanks[] = [
                'visible_prefix'    => $m[1] === '' ? null : $m[1],
                'correct_answer'    => array_shift($answers),
                'accepted_variants' => $answers ?: null,
            ];
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
        $correctAt = ($n + $groupOrder + strlen($this->prefix)) % count($options);
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
        $name = ($this->prefix === '' ? '' : strtolower($this->prefix) . '_') . $name;
        $this->clips[$name] = array_map(fn ($s) => ['voice' => $s[0], 'text' => $s[1]], $segments);

        return self::AUDIO_DIR . "/{$name}.wav";
    }

    private function transcript(array $segments): string
    {
        return implode("\n", array_column($segments, 1));
    }

    /**
     * Synthesizes any missing clips with Windows speech synthesis. Existing clips are
     * left alone; on other systems missing clips are only reported.
     */
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
