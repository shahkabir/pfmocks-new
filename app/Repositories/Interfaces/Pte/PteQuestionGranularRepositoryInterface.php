<?php

namespace App\Repositories\Interfaces\Pte;

use App\Repositories\Interfaces\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

interface PteQuestionGranularRepositoryInterface extends BaseRepositoryInterface
{
    public function bySubType(int $subTypeId): Collection;
    public function generateNextGranularId(string $subTypeTag): string;
}
