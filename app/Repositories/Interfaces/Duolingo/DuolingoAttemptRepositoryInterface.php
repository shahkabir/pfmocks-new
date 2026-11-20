<?php

namespace App\Repositories\Interfaces\Duolingo;

use App\Models\Duolingo\DuolingoAttempt;
use App\Repositories\Interfaces\BaseRepositoryInterface;

interface DuolingoAttemptRepositoryInterface extends BaseRepositoryInterface
{
    public function currentInProgress(int $userId, int $moduleId): ?DuolingoAttempt;
}
