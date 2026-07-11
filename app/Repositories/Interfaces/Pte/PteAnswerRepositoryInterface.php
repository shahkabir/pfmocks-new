<?php

namespace App\Repositories\Interfaces\Pte;

use App\Models\Pte\PteAnswer;
use App\Repositories\Interfaces\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

interface PteAnswerRepositoryInterface extends BaseRepositoryInterface
{
    public function byAttempt(int $attemptId): Collection;
    public function upsertForMapping(int $attemptId, int $mappingId, int $questionId, array $payload): PteAnswer;
}
