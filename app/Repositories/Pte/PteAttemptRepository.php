<?php

namespace App\Repositories\Pte;

use App\Models\Pte\PteAttempt;
use App\Repositories\BaseRepository;
use App\Repositories\Interfaces\Pte\PteAttemptRepositoryInterface;

class PteAttemptRepository extends BaseRepository implements PteAttemptRepositoryInterface
{
    public function __construct(PteAttempt $model)
    {
        parent::__construct($model);
    }

    public function currentInProgress(int $userId, int $moduleId): ?PteAttempt
    {
        return $this->model
            ->where('user_id', $userId)
            ->where('module_id', $moduleId)
            ->where('status', PteAttempt::STATUS_IN_PROGRESS)
            ->latest('id')
            ->first();
    }
}
