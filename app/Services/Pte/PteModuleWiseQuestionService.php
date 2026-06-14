<?php

namespace App\Services\Pte;

use App\Models\Pte\PteModuleWiseQuestion;
use App\Repositories\Interfaces\Pte\PteModuleWiseQuestionRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
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

    /**
     * Bulk-attach question(s) to a PTE module. Skips any pair that already exists.
     * Auto-assigns display_order continuing from the current max for that module.
     *
     * @param  int[]  $questionIds
     * @return int    Count of new mappings created.
     */
    public function bulkCreate(int $pteModuleId, array $questionIds): int
    {
        $clean = collect($questionIds)
            ->map(fn ($v) => (int) $v)
            ->filter()
            ->unique()
            ->values();

        if ($clean->isEmpty()) {
            return 0;
        }

        return DB::transaction(function () use ($pteModuleId, $clean) {
            $existing = PteModuleWiseQuestion::where('pte_module_id', $pteModuleId)
                ->pluck('pte_question_granular_id')
                ->map(fn ($v) => (int) $v)
                ->all();

            $toCreate = $clean->reject(fn ($id) => in_array($id, $existing, true));
            if ($toCreate->isEmpty()) return 0;

            $nextOrder = (int) PteModuleWiseQuestion::where('pte_module_id', $pteModuleId)->max('display_order');

            foreach ($toCreate as $questionId) {
                $nextOrder++;
                PteModuleWiseQuestion::create([
                    'pte_module_id'            => $pteModuleId,
                    'pte_question_granular_id' => $questionId,
                    'display_order'            => $nextOrder,
                    'is_active'                => true,
                ]);
            }
            return $toCreate->count();
        });
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
