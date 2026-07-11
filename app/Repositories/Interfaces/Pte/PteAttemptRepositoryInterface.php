<?php

namespace App\Repositories\Interfaces\Pte;

use App\Models\Pte\PteAttempt;
use App\Repositories\Interfaces\BaseRepositoryInterface;

interface PteAttemptRepositoryInterface extends BaseRepositoryInterface
{
    public function currentInProgress(int $userId, int $moduleId): ?PteAttempt;
}
