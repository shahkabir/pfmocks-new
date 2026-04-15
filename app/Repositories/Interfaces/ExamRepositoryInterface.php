<?php

namespace App\Repositories\Interfaces;

interface ExamRepositoryInterface extends BaseRepositoryInterface
{
    public function allActive(): \Illuminate\Database\Eloquent\Collection;
}
