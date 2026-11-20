<?php

namespace App\Repositories\Duolingo;

use App\Models\Duolingo\DuolingoAnswer;
use App\Repositories\BaseRepository;
use App\Repositories\Interfaces\Duolingo\DuolingoAnswerRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class DuolingoAnswerRepository extends BaseRepository implements DuolingoAnswerRepositoryInterface
{
    public function __construct(DuolingoAnswer $model)
    {
        parent::__construct($model);
    }

    public function byAttempt(int $attemptId): Collection
    {
        return $this->model->where('duolingo_attempt_id', $attemptId)->get();
    }

    public function upsertForMapping(int $attemptId, int $mappingId, int $questionId, array $payload): DuolingoAnswer
    {
        return $this->model->updateOrCreate(
            [
                'duolingo_attempt_id'              => $attemptId,
                'duolingo_module_wise_question_id' => $mappingId,
            ],
            array_merge($payload, ['duolingo_question_id' => $questionId]),
        );
    }
}
