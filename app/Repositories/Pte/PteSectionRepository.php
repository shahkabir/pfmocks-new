<?php

namespace App\Repositories\Pte;

use App\Models\Pte\PteSection;
use App\Repositories\BaseRepository;
use App\Repositories\Interfaces\Pte\PteSectionRepositoryInterface;

class PteSectionRepository extends BaseRepository implements PteSectionRepositoryInterface
{
    public function __construct(PteSection $model)
    {
        parent::__construct($model);
    }
}
