<?php

namespace App\Services\Pte;

use App\Models\Pte\PteQuestionSubType;
use App\Repositories\Interfaces\Pte\PteQuestionSubTypeRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class PteQuestionSubTypeService
{
    public function __construct(private readonly PteQuestionSubTypeRepositoryInterface $repo) {}

    public function getQuery(): Builder
    {
        return $this->repo->query()->with('section');
    }

    public function bySection(int $sectionId): Collection
    {
        return $this->repo->bySection($sectionId);
    }

    public function findOrFail(int $id): PteQuestionSubType
    {
        return $this->repo->find($id);
    }

    public function create(array $data): PteQuestionSubType
    {
        return $this->repo->create($this->validate($data));
    }

    public function update(int $id, array $data): PteQuestionSubType
    {
        return $this->repo->update($id, $this->validate($data, $id));
    }

    public function delete(int $id): bool
    {
        return $this->repo->delete($id);
    }

    private function validate(array $data, ?int $ignoreId = null): array
    {
        $tagRule = 'required|string|max:5|regex:/^[A-Z0-9]+$/|unique:pte_question_sub_types,tag';
        if ($ignoreId) $tagRule .= ',' . $ignoreId;

        $v = Validator::make($data, [
            'pte_section_id'               => 'required|exists:pte_sections,id',
            'name'                         => 'required|string|max:100',
            'tag'                          => $tagRule,
            'response_type'                => 'required|in:audio_record,text_write,single_choice,multi_choice,fill_blank,reorder,highlight_words,select_option',
            'stimulus_type'                => 'required|in:text,audio,image,audio_image,none',
            'preparation_time_sec_default' => 'nullable|integer|min:0',
            'answer_time_sec_default'      => 'nullable|integer|min:0',
            'marks_default'                => 'nullable|integer|min:0|max:255',
            'typical_count_in_exam'        => 'nullable|integer|min:0|max:255',
            'instructions'                 => 'nullable|string',
            'display_order'                => 'nullable|integer|min:0|max:255',
            'is_active'                    => 'boolean',
        ]);
        if ($v->fails()) throw new ValidationException($v);
        return $v->validated();
    }
}
