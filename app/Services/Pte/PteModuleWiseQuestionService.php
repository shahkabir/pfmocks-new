<?php

namespace App\Services\Pte;

use App\Models\Pte\PteModuleWiseQuestion;
use App\Repositories\Interfaces\Pte\PteModuleWiseQuestionRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class PteModuleWiseQuestionService
{
    public function __construct(private readonly PteModuleWiseQuestionRepositoryInterface $repo) {}

    public function getQuery(): Builder
    {
        return $this->repo->query()->with(['pteModule.section', 'question']);
    }

    public function byPteModule(int $pteModuleId): Collection
    {
        return $this->repo->byPteModule($pteModuleId);
    }

    public function findOrFail(int $id): PteModuleWiseQuestion
    {
        return $this->repo->find($id);
    }

    public function create(array $data): PteModuleWiseQuestion
    {
        return $this->repo->create($this->validate($data));
    }

    public function update(int $id, array $data): PteModuleWiseQuestion
    {
        return $this->repo->update($id, $this->validate($data));
    }

    public function delete(int $id): bool
    {
        return $this->repo->delete($id);
    }

    private function validate(array $data): array
    {
        $v = Validator::make($data, [
            'pte_module_id'            => 'required|exists:pte_module,id',
            'pte_question_granular_id' => 'required|exists:pte_question_granular,id',
            'display_order'            => 'required|integer|min:0',
            'is_active'                => 'boolean',
        ]);
        if ($v->fails()) throw new ValidationException($v);
        return $v->validated();
    }
}
