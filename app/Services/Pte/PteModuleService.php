<?php

namespace App\Services\Pte;

use App\Models\Pte\PteModule;
use App\Repositories\Interfaces\Pte\PteModuleRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class PteModuleService
{
    public function __construct(private readonly PteModuleRepositoryInterface $repo) {}

    public function getQuery(): Builder
    {
        return $this->repo->query()->with(['module.exam', 'section']);
    }

    public function byModule(int $moduleId): Collection
    {
        return $this->repo->byModule($moduleId);
    }

    public function findOrFail(int $id): PteModule
    {
        return $this->repo->find($id);
    }

    public function create(array $data): PteModule
    {
        return $this->repo->create($this->validate($data));
    }

    public function update(int $id, array $data): PteModule
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
            'module_id'      => 'required|exists:modules,id',
            'pte_section_id' => 'required|exists:pte_sections,id',
        ]);
        if ($v->fails()) throw new ValidationException($v);
        return $v->validated();
    }
}
