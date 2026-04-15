<?php

namespace App\Repositories;

use App\Models\Question\QuestionOptions;
use App\Repositories\Interfaces\QuestionOptionsRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class QuestionOptionsRepository extends BaseRepository implements QuestionOptionsRepositoryInterface
{
    public function __construct(QuestionOptions $model)
    {
        parent::__construct($model);
    }

    public function byQuestion(int $questionId): Collection
    {
        return $this->model->where('question_id', $questionId)->orderBy('sort_order')->get();
    }
}
