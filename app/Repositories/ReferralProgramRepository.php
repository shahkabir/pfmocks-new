<?php

namespace App\Repositories;

use App\Models\Referral\ReferralProgram;
use App\Repositories\Interfaces\ReferralProgramRepositoryInterface;

class ReferralProgramRepository extends BaseRepository implements ReferralProgramRepositoryInterface
{
    public function __construct(ReferralProgram $model)
    {
        parent::__construct($model);
    }

    /**
     * Returns the most recently created live program (active + within window).
     * Used at signup/checkout time when no specific program is targeted.
     */
    public function activeOne(): ?ReferralProgram
    {
        return $this->model->newQuery()
            ->active()
            ->where(function ($q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            })
            ->orderByDesc('id')
            ->first();
    }
}
