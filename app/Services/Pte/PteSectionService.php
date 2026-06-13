<?php

namespace App\Services\Pte;

use App\Models\Pte\PteSection;
use App\Repositories\Interfaces\Pte\PteSectionRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class PteSectionService
{
    public function __construct(private readonly PteSectionRepositoryInterface $repo) {}

    public function getQuery(): Builder
    {
        return $this->repo->query();
    }

    public function all(): Collection
    {
        return $this->repo->query()->orderBy('display_order')->get();
    }

    public function findOrFail(int $id): PteSection
    {
        return $this->repo->find($id);
    }

    public function create(array $data): PteSection
    {
        return $this->repo->create($this->validate($data));
    }

    public function update(int $id, array $data): PteSection
    {
        return $this->repo->update($id, $this->validate($data, $id));
    }

    public function delete(int $id): bool
    {
        return $this->repo->delete($id);
    }

    private function validate(array $data, ?int $ignoreId = null): array
    {
        $tagRule = 'required|string|max:5|regex:/^[A-Z0-9]+$/|unique:pte_sections,tag';
        if ($ignoreId) $tagRule .= ',' . $ignoreId;

        $v = Validator::make($data, [
            'name'                 => 'required|string|max:100',
            'tag'                  => $tagRule,
            'display_order'        => 'required|integer|min:0|max:255',
            'time_allowed_minutes' => 'nullable|integer|min:0',
            'description'          => 'nullable|string',
        ]);
        if ($v->fails()) throw new ValidationException($v);
        return $v->validated();
    }
}
