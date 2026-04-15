<?php

namespace App\Repositories;

use App\Models\Module\Module;
use App\Repositories\Interfaces\ModuleRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ModuleRepository extends BaseRepository implements ModuleRepositoryInterface
{
    public function __construct(Module $model)
    {
        parent::__construct($model);
    }

    public function byExam(int $examId): Collection
    {
        return $this->model->where('exam_id', $examId)->orderBy('name')->get();
    }
}
