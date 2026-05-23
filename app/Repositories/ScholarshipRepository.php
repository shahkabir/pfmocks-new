<?php

namespace App\Repositories;

use App\Models\Scholarship\Scholarship;
use App\Repositories\Interfaces\ScholarshipRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;

class ScholarshipRepository extends BaseRepository implements ScholarshipRepositoryInterface
{
    public function __construct(Scholarship $model)
    {
        parent::__construct($model);
    }

    public function filtered(array $filters): Builder
    {
        $q = $this->model->newQuery();

        if (!empty($filters['type']))           $q->where('type', $filters['type']);
        if (!empty($filters['country']))        $q->where('country_code', $filters['country']);
        if (!empty($filters['funding_type']))   $q->where('funding_type', $filters['funding_type']);
        if (!empty($filters['program_level']))  $q->where('program_level', $filters['program_level']);
        if (!empty($filters['search'])) {
            $s = $filters['search'];
            $q->where(function ($qq) use ($s) {
                $qq->where('title', 'like', "%{$s}%")
                   ->orWhere('country_name', 'like', "%{$s}%")
                   ->orWhere('short_description', 'like', "%{$s}%");
            });
        }
        if (isset($filters['only_open']) && $filters['only_open']) {
            $q->where(function ($qq) {
                $qq->whereNull('deadline')->orWhere('deadline', '>=', today());
            });
        }
        if (isset($filters['only_active']) && $filters['only_active']) {
            $q->where('is_active', true);
        }

        return $q->orderByRaw('CASE WHEN is_featured = 1 THEN 0 ELSE 1 END')
                 ->orderBy('deadline', 'asc')
                 ->orderByDesc('created_at');
    }
}
