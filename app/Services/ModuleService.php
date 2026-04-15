<?php

namespace App\Services;

use App\Models\Module\Module;
use App\Repositories\Interfaces\ModuleRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

class ModuleService
{
    public function __construct(private readonly ModuleRepositoryInterface $repo) {}

    public function getQuery(): Builder
    {
        return $this->repo->query()->with('exam');
    }

    public function all(): Collection
    {
        return $this->repo->all();
    }

    public function byExam(int $examId): Collection
    {
        return $this->repo->byExam($examId);
    }

    public function findOrFail(int $id): Module
    {
        return $this->repo->find($id);
    }

    public function create(array $data): Module
    {
        $validated = $this->validate($data);
        return $this->repo->create($validated);
    }

    public function update(int $id, array $data): Module
    {
        $validated = $this->validate($data);
        return $this->repo->update($id, $validated);
    }

    public function delete(int $id): bool
    {
        return $this->repo->delete($id);
    }

    private function validate(array $data): array
    {
        $validator = Validator::make($data, [
            'exam_id'          => 'required|exists:exams,id',
            'name'             => 'required|string|max:255',
            'module_type'      => 'required|in:reading,writing,listening,speaking,general_mcq',
            'type'             => 'required|in:free,paid',
            'price_in_bdt'     => 'nullable|numeric|min:0',
            'price_in_usd'     => 'nullable|numeric|min:0',
            'duration_minutes' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }
}
