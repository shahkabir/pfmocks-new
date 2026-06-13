<?php

namespace App\Repositories\Pte;

use App\Models\Pte\PteModule;
use App\Repositories\BaseRepository;
use App\Repositories\Interfaces\Pte\PteModuleRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class PteModuleRepository extends BaseRepository implements PteModuleRepositoryInterface
{
    public function __construct(PteModule $model)
    {
        parent::__construct($model);
    }

    public function byModule(int $moduleId): Collection
    {
        return $this->model
            ->where('module_id', $moduleId)
            ->with('section')
            ->get();
    }
}
