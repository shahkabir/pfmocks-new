<?php

namespace App\Services;

use App\Models\Exam\Exam;
use App\Repositories\Interfaces\ExamRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

class ExamService
{
    public function __construct(private readonly ExamRepositoryInterface $repo) {}

    public function getQuery(): Builder
    {
        return $this->repo->query();
    }

    public function all(): Collection
    {
        return $this->repo->all();
    }

    public function allActive(): Collection
    {
        return $this->repo->allActive();
    }

    public function findOrFail(int $id): Exam
    {
        return $this->repo->find($id);
    }

    public function create(array $data): Exam
    {
        $validated = $this->validate($data);
        return $this->repo->create($validated);
    }

    public function update(int $id, array $data): Exam
    {
        $validated = $this->validate($data, $id);
        return $this->repo->update($id, $validated);
    }

    public function delete(int $id): bool
    {
        return $this->repo->delete($id);
    }

    private function validate(array $data, ?int $ignoreId = null): array
    {
        $tagRule = 'required|string|max:100|regex:/^[a-z0-9_]+$/|unique:exams,tag';
        if ($ignoreId) {
            $tagRule .= ',' . $ignoreId;
        }

        $validator = Validator::make($data, [
            'name'      => 'required|string|max:255',
            'tag'       => $tagRule,
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }
}
