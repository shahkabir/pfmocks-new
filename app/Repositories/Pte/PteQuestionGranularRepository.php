<?php

namespace App\Repositories\Pte;

use App\Models\Pte\PteQuestionGranular;
use App\Repositories\BaseRepository;
use App\Repositories\Interfaces\Pte\PteQuestionGranularRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class PteQuestionGranularRepository extends BaseRepository implements PteQuestionGranularRepositoryInterface
{
    public function __construct(PteQuestionGranular $model)
    {
        parent::__construct($model);
    }

    public function bySubType(int $subTypeId): Collection
    {
        return $this->model
            ->where('pte_sub_type_id', $subTypeId)
            ->orderByDesc('id')
            ->get();
    }

    /**
     * Build the next sequential question_granular_id: e.g. "RA" -> "RA0001", "RA0002"…
     */
    public function generateNextGranularId(string $subTypeTag): string
    {
        $prefix = strtoupper(trim($subTypeTag));
        $last   = $this->model
            ->where('question_granular_id', 'like', $prefix . '%')
            ->orderByDesc('question_granular_id')
            ->value('question_granular_id');

        $next = $last
            ? ((int) substr($last, strlen($prefix))) + 1
            : 1;

        return $prefix . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }
}
