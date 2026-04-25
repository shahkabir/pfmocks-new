<?php

namespace App\Repositories\Interfaces;

use App\Models\Referral\ReferralProgram;

interface ReferralProgramRepositoryInterface extends BaseRepositoryInterface
{
    public function activeOne(): ?ReferralProgram;
}
