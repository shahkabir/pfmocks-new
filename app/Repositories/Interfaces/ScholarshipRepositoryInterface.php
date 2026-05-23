<?php

namespace App\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Builder;

interface ScholarshipRepositoryInterface extends BaseRepositoryInterface
{
    /** Builder ready for filtering by type / country / funding / level / search. */
    public function filtered(array $filters): Builder;
}
