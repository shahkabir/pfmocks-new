<?php

namespace App\Services;

use App\Models\Question\Question;
use App\Repositories\Interfaces\QuestionRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

class QuestionService
{
    public function __construct(private readonly QuestionRepositoryInterface $repo) {}

    public function getQuery(): Builder
    {
        return $this->repo->query()->with('module');
    }

    public function byModule(int $moduleId): Collection
    {
        return $this->repo->byModule($moduleId);
    }

    public function findOrFail(int $id): Question
    {
        return $this->repo->find($id);
    }

    public function create(array $data): Question
    {
        $validated = $this->validate($data);
        $validated['meta'] = isset($validated['meta']) ? json_encode($validated['meta']) : null;
        return $this->repo->create($validated);
    }

    public function update(int $id, array $data): Question
    {
        $validated = $this->validate($data);
        $validated['meta'] = isset($validated['meta']) ? json_encode($validated['meta']) : null;
        return $this->repo->update($id, $validated);
    }

    public function delete(int $id): bool
    {
        return $this->repo->delete($id);
    }

    private function validate(array $data): array
    {
        $validator = Validator::make($data, [
            'module_id'       => 'required|exists:modules,id',
            'type'            => 'required|in:mcq_single,mcq_multiple,text,essay,audio,speaking',
            'question_header'      => 'nullable|string',
            'passage'              => 'nullable|string',
            'passage_instruction'  => 'nullable|string',
            'audio_url'            => 'nullable|string|max:500',
            'image_url'       => 'nullable|string|max:500',
            'marks'           => 'nullable|integer|min:0',
            'sort_order'      => 'nullable|integer|min:0',
            'meta'            => 'nullable',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }
}
