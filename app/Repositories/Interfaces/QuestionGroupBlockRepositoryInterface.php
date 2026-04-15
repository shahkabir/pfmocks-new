<?php

namespace App\Repositories\Interfaces;

interface QuestionGroupBlockRepositoryInterface extends BaseRepositoryInterface
{
    public function byGroup(int $groupId): \Illuminate\Database\Eloquent\Collection;
}
