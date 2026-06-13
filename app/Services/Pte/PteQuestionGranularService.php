<?php

namespace App\Services\Pte;

use App\Models\Pte\PteQuestionGranular;
use App\Repositories\Interfaces\Pte\PteQuestionGranularRepositoryInterface;
use App\Repositories\Interfaces\Pte\PteQuestionSubTypeRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class PteQuestionGranularService
{
    public function __construct(
        private readonly PteQuestionGranularRepositoryInterface $repo,
        private readonly PteQuestionSubTypeRepositoryInterface  $subTypeRepo,
    ) {}

    public function getQuery(): Builder
    {
        return $this->repo->query()->with('subType.section');
    }

    public function findOrFail(int $id): PteQuestionGranular
    {
        return $this->repo->find($id);
    }

    /**
     * Create a question plus its satellite rows (options/blanks/segments/words),
     * auto-generating the granular_id (e.g. RA0001).
     */
    public function create(array $data): PteQuestionGranular
    {
        $validated = $this->validate($data);

        return DB::transaction(function () use ($validated, $data) {
            $subType = $this->subTypeRepo->find($validated['pte_sub_type_id']);
            $validated['question_granular_id'] = $this->repo->generateNextGranularId($subType->tag);

            $question = $this->repo->create($validated);
            $this->syncSatellites($question, $data);
            return $question->fresh(['options', 'blanks', 'segments', 'highlightWords']);
        });
    }

    public function update(int $id, array $data): PteQuestionGranular
    {
        $validated = $this->validate($data, $id);

        return DB::transaction(function () use ($id, $validated, $data) {
            $question = $this->repo->update($id, $validated);
            $this->syncSatellites($question, $data);
            return $question->fresh(['options', 'blanks', 'segments', 'highlightWords']);
        });
    }

    public function delete(int $id): bool
    {
        return $this->repo->delete($id);
    }

    private function syncSatellites(PteQuestionGranular $question, array $data): void
    {
        if (!empty($data['options']) && is_array($data['options'])) {
            $question->options()->delete();
            foreach ($data['options'] as $i => $opt) {
                $question->options()->create([
                    'option_text'   => $opt['option_text'] ?? '',
                    'is_correct'    => (bool) ($opt['is_correct'] ?? false),
                    'display_order' => $opt['display_order'] ?? ($i + 1),
                ]);
            }
        }

        if (!empty($data['blanks']) && is_array($data['blanks'])) {
            $question->blanks()->delete();
            foreach ($data['blanks'] as $i => $b) {
                $question->blanks()->create([
                    'blank_order'       => $b['blank_order'] ?? ($i + 1),
                    'correct_answer'    => $b['correct_answer'] ?? '',
                    'accepted_variants' => $b['accepted_variants'] ?? null,
                    'dropdown_options'  => $b['dropdown_options'] ?? null,
                ]);
            }
        }

        if (!empty($data['segments']) && is_array($data['segments'])) {
            $question->segments()->delete();
            foreach ($data['segments'] as $i => $s) {
                $question->segments()->create([
                    'segment_text'  => $s['segment_text'] ?? '',
                    'correct_order' => $s['correct_order'] ?? ($i + 1),
                ]);
            }
        }

        if (!empty($data['highlight_words']) && is_array($data['highlight_words'])) {
            $question->highlightWords()->delete();
            foreach ($data['highlight_words'] as $i => $w) {
                $question->highlightWords()->create([
                    'word_text'    => $w['word_text'] ?? '',
                    'word_order'   => $w['word_order'] ?? ($i + 1),
                    'is_incorrect' => (bool) ($w['is_incorrect'] ?? false),
                ]);
            }
        }
    }

    private function validate(array $data, ?int $ignoreId = null): array
    {
        $v = Validator::make($data, [
            'pte_sub_type_id'         => 'required|exists:pte_question_sub_types,id',
            'question_text'           => 'nullable|string',
            'audio_transcript'        => 'nullable|string',
            'audio_url'               => 'nullable|string|max:500',
            'image_url'               => 'nullable|string|max:500',
            'image_alt_text'          => 'nullable|string|max:255',
            'preparation_time_sec'    => 'nullable|integer|min:0',
            'answer_time_sec'         => 'nullable|integer|min:0',
            'marks'                   => 'nullable|integer|min:0|max:255',
            'correct_ans'             => 'nullable|string',
            'correct_ans_explanation' => 'nullable|string',
            'min_word_count'          => 'nullable|integer|min:0',
            'max_word_count'          => 'nullable|integer|min:0',
            'difficulty'              => 'nullable|in:easy,medium,hard',
            'topic_tags'              => 'nullable|array',
            'source_reference'        => 'nullable|string|max:255',
            'is_active'               => 'boolean',
        ]);
        if ($v->fails()) throw new ValidationException($v);
        return $v->validated();
    }
}
