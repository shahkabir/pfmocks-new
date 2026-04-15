<?php

namespace App\Services;

use App\Models\Question\QuestionGroup;
use App\Repositories\Interfaces\QuestionGroupRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

class QuestionGroupService
{
    public function __construct(private readonly QuestionGroupRepositoryInterface $repo) {}

    public function getQuery(): Builder
    {
        return $this->repo->query()->with(['question.module', 'blocks']);
    }

    public function findOrFail(int $id): QuestionGroup
    {
        return $this->repo->find($id);
    }

    public function create(array $data): QuestionGroup
    {
        $validated = $this->validate($data);
        $validated['question_options_group_ids'] = json_encode(
            array_map('intval', $validated['question_options_group_ids'] ?? [])
        );
        return $this->repo->create($validated);
    }

    public function update(int $id, array $data): QuestionGroup
    {
        $validated = $this->validate($data);
        $validated['question_options_group_ids'] = json_encode(
            array_map('intval', $validated['question_options_group_ids'] ?? [])
        );
        return $this->repo->update($id, $validated);
    }

    public function delete(int $id): bool
    {
        return $this->repo->delete($id);
    }

    private function validate(array $data): array
    {
        $validator = Validator::make($data, [
            'question_id'                 => 'required|exists:questions,id',
            'question_options_group_ids'  => 'nullable|array',
            'question_options_group_ids.*'=> 'integer|exists:question_options,id',
            'part_number'                 => 'nullable|integer|min:1|max:10',
            'part_audio_url'              => 'nullable|string|max:500',
            'part_image_url'              => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }
}
