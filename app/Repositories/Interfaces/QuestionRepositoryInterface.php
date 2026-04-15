<?php

namespace App\Repositories\Interfaces;

interface QuestionRepositoryInterface extends BaseRepositoryInterface
{
    public function byModule(int $moduleId): \Illuminate\Database\Eloquent\Collection;
}
