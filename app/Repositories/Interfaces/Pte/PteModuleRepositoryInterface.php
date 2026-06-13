<?php

namespace App\Repositories\Interfaces\Pte;

use App\Repositories\Interfaces\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

interface PteModuleRepositoryInterface extends BaseRepositoryInterface
{
    public function byModule(int $moduleId): Collection;
}
