<?php

namespace App\Repositories\Interfaces\Pte;

use App\Repositories\Interfaces\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

interface PteModuleWiseQuestionRepositoryInterface extends BaseRepositoryInterface
{
    public function byPteModule(int $pteModuleId): Collection;
}
