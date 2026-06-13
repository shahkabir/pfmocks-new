<?php

namespace App\Repositories\Pte;

use App\Models\Pte\PteModuleWiseQuestion;
use App\Repositories\BaseRepository;
use App\Repositories\Interfaces\Pte\PteModuleWiseQuestionRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class PteModuleWiseQuestionRepository extends BaseRepository implements PteModuleWiseQuestionRepositoryInterface
{
    public function __construct(PteModuleWiseQuestion $model)
    {
        parent::__construct($model);
    }

    public function byPteModule(int $pteModuleId): Collection
    {
        return $this->model
            ->where('pte_module_id', $pteModuleId)
            ->with('question.subType')
            ->orderBy('display_order')
            ->get();
    }
}
