<?php

namespace App\Repositories;

use App\Models\Question\QuestionGroup;
use App\Repositories\Interfaces\QuestionGroupRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class QuestionGroupRepository extends BaseRepository implements QuestionGroupRepositoryInterface
{
    public function __construct(QuestionGroup $model)
    {
        parent::__construct($model);
    }

    public function byQuestion(int $questionId): ?Model
    {
        return $this->model->where('question_id', $questionId)->first();
    }
}
