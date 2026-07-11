<?php

namespace App\Repositories\Pte;

use App\Models\Pte\PteAnswer;
use App\Repositories\BaseRepository;
use App\Repositories\Interfaces\Pte\PteAnswerRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class PteAnswerRepository extends BaseRepository implements PteAnswerRepositoryInterface
{
    public function __construct(PteAnswer $model)
    {
        parent::__construct($model);
    }

    public function byAttempt(int $attemptId): Collection
    {
        return $this->model->where('pte_attempt_id', $attemptId)->get();
    }

    public function upsertForMapping(int $attemptId, int $mappingId, int $questionId, array $payload): PteAnswer
    {
        return $this->model->updateOrCreate(
            [
                'pte_attempt_id'              => $attemptId,
                'pte_module_wise_question_id' => $mappingId,
            ],
            array_merge($payload, ['pte_question_granular_id' => $questionId]),
        );
    }
}
