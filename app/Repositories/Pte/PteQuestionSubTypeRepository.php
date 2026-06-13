<?php

namespace App\Repositories\Pte;

use App\Models\Pte\PteQuestionSubType;
use App\Repositories\BaseRepository;
use App\Repositories\Interfaces\Pte\PteQuestionSubTypeRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class PteQuestionSubTypeRepository extends BaseRepository implements PteQuestionSubTypeRepositoryInterface
{
    public function __construct(PteQuestionSubType $model)
    {
        parent::__construct($model);
    }

    public function bySection(int $sectionId): Collection
    {
        return $this->model
            ->where('pte_section_id', $sectionId)
            ->orderBy('display_order')
            ->get();
    }
}
