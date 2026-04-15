<?php

namespace App\Services;

use App\Models\Question\QuestionGroupBlock;
use App\Repositories\Interfaces\QuestionGroupBlockRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

class QuestionGroupBlockService
{
    public function __construct(private readonly QuestionGroupBlockRepositoryInterface $repo) {}

    public function getQuery(): Builder
    {
        return $this->repo->query()->with('group.question');
    }

    public function byGroup(int $groupId): \Illuminate\Database\Eloquent\Collection
    {
        return $this->repo->byGroup($groupId);
    }

    public function findOrFail(int $id): QuestionGroupBlock
    {
        return $this->repo->find($id);
    }

    public function create(array $data): QuestionGroupBlock
    {
        $validated = $this->validate($data);
        $validated['question_option_ids'] = json_encode(
            array_map('intval', $validated['question_option_ids'] ?? [])
        );
        return $this->repo->create($validated);
    }

    public function update(int $id, array $data): QuestionGroupBlock
    {
        $validated = $this->validate($data);
        $validated['question_option_ids'] = json_encode(
            array_map('intval', $validated['question_option_ids'] ?? [])
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
            'question_group_id'    => 'required|exists:question_groups,id',
            'instruction_text'     => 'required|string',
            'question_option_ids'  => 'nullable|array',
            'question_option_ids.*'=> 'integer|exists:question_options,id',
            'sort_order'           => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }
}
