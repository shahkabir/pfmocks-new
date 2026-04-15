<?php

namespace App\Repositories;

use App\Models\Exam\Exam;
use App\Repositories\Interfaces\ExamRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ExamRepository extends BaseRepository implements ExamRepositoryInterface
{
    public function __construct(Exam $model)
    {
        parent::__construct($model);
    }

    public function allActive(): Collection
    {
        return $this->model->where('is_active', true)->orderBy('name')->get();
    }
}
