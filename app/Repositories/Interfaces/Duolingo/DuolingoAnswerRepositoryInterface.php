<?php

namespace App\Repositories\Interfaces\Duolingo;

use App\Models\Duolingo\DuolingoAnswer;
use App\Repositories\Interfaces\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

interface DuolingoAnswerRepositoryInterface extends BaseRepositoryInterface
{
    public function byAttempt(int $attemptId): Collection;
    public function upsertForMapping(int $attemptId, int $mappingId, int $questionId, array $payload): DuolingoAnswer;
}
