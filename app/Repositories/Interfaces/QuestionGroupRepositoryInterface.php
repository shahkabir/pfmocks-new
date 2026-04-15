<?php

namespace App\Repositories\Interfaces;

interface QuestionGroupRepositoryInterface extends BaseRepositoryInterface
{
    public function byQuestion(int $questionId): ?\Illuminate\Database\Eloquent\Model;
}
