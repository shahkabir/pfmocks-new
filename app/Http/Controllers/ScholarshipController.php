<?php

namespace App\Http\Controllers;

use App\Constants\ScholarshipConstants;
use App\Repositories\Interfaces\ScholarshipRepositoryInterface;
use App\Services\ScholarshipService;
use Illuminate\Http\Request;

class ScholarshipController extends Controller
{
    public function __construct(
        private ScholarshipRepositoryInterface $repo,
        private ScholarshipService             $service
    ) {}

    public function index(Request $request)
    {
        $scholarships = $this->repo
            ->filtered(array_merge($request->only(['type','country','funding_type','program_level','search']), [
                'only_active' => true,
            ]))
            ->paginate(24)
            ->withQueryString();

        return view('student.scholarships.index', [
            'scholarships'   => $scholarships,
            'types'          => ScholarshipConstants::TYPES,
            'fundingTypes'   => ScholarshipConstants::FUNDING_TYPES,
            'programLevels'  => ScholarshipConstants::PROGRAM_LEVELS,
            'countries'      => $this->service->countryList(),
            'filters'        => $request->only(['type','country','funding_type','program_level','search']),
        ]);
    }
}
