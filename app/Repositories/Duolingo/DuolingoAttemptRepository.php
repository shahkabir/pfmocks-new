<?php

namespace App\Repositories\Duolingo;

use App\Models\Duolingo\DuolingoAttempt;
use App\Repositories\BaseRepository;
use App\Repositories\Interfaces\Duolingo\DuolingoAttemptRepositoryInterface;

class DuolingoAttemptRepository extends BaseRepository implements DuolingoAttemptRepositoryInterface
{
    public function __construct(DuolingoAttempt $model)
    {
        parent::__construct($model);
    }

    public function currentInProgress(int $userId, int $moduleId): ?DuolingoAttempt
    {
        return $this->model
            ->where('user_id', $userId)
            ->where('module_id', $moduleId)
            ->where('status', DuolingoAttempt::STATUS_IN_PROGRESS)
            ->latest('id')
            ->first();
    }
}
