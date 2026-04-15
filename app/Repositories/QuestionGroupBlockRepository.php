<?php

namespace App\Repositories;

use App\Models\Question\QuestionGroupBlock;
use App\Repositories\Interfaces\QuestionGroupBlockRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class QuestionGroupBlockRepository extends BaseRepository implements QuestionGroupBlockRepositoryInterface
{
    public function __construct(QuestionGroupBlock $model)
    {
        parent::__construct($model);
    }

    public function byGroup(int $groupId): Collection
    {
        return $this->model->where('question_group_id', $groupId)->orderBy('sort_order')->get();
    }
}
