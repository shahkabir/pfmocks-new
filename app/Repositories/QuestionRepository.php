<?php

namespace App\Repositories;

use App\Models\Question\Question;
use App\Repositories\Interfaces\QuestionRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class QuestionRepository extends BaseRepository implements QuestionRepositoryInterface
{
    public function __construct(Question $model)
    {
        parent::__construct($model);
    }

    public function byModule(int $moduleId): Collection
    {
        return $this->model->where('module_id', $moduleId)->orderBy('sort_order')->get();
    }
}
