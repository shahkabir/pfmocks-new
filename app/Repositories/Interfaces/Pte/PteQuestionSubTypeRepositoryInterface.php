<?php

namespace App\Repositories\Interfaces\Pte;

use App\Repositories\Interfaces\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

interface PteQuestionSubTypeRepositoryInterface extends BaseRepositoryInterface
{
    public function bySection(int $sectionId): Collection;
}
