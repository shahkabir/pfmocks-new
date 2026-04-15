<?php

namespace App\Repositories\Interfaces;

interface ModuleRepositoryInterface extends BaseRepositoryInterface
{
    public function byExam(int $examId): \Illuminate\Database\Eloquent\Collection;
}
