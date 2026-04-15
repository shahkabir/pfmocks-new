<?php

namespace App\Repositories\Interfaces;

interface QuestionOptionsRepositoryInterface extends BaseRepositoryInterface
{
    public function byQuestion(int $questionId): \Illuminate\Database\Eloquent\Collection;
}
