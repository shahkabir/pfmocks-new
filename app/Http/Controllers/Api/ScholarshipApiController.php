<?php

namespace App\Http\Controllers\Api;

use App\Constants\ScholarshipConstants;
use App\Http\Controllers\Controller;
use App\Models\Scholarship\Scholarship;
use App\Repositories\Interfaces\ScholarshipRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Public, read-only API consumed by the external business website.
 * No auth — only returns active scholarships.
 */
class ScholarshipApiController extends Controller
{
    public function __construct(private ScholarshipRepositoryInterface $repo) {}

    /** GET /api/scholarships  (supports type/country/funding_type/program_level/search filters) */
    public function index(Request $request): JsonResponse
    {
        $page = $this->repo
            ->filtered(array_merge($request->only(['type','country','funding_type','program_level','search']), [
                'only_active' => true,
                'only_open'   => $request->boolean('only_open', false),
            ]))
            ->paginate(min((int) $request->input('per_page', 24), 100))
            ->withQueryString();

        return response()->json([
            'data' => $page->getCollection()->map(fn (Scholarship $s) => $this->shape($s, false)),
            'meta' => [
                'current_page' => $page->currentPage(),
                'last_page'    => $page->lastPage(),
                'per_page'     => $page->perPage(),
                'total'        => $page->total(),
            ],
        ]);
    }

    /** GET /api/scholarships/{slug}  — full detail */
    public function show(string $slug): JsonResponse
    {
        $s = Scholarship::where('slug', $slug)->where('is_active', true)->firstOrFail();
        return response()->json(['data' => $this->shape($s, true)]);
    }

    /** GET /api/scholarships-meta  — dropdown options for filter UIs */
    public function meta(): JsonResponse
    {
        return response()->json([
            'types'          => ScholarshipConstants::TYPES,
            'funding_types'  => ScholarshipConstants::FUNDING_TYPES,
            'program_levels' => ScholarshipConstants::PROGRAM_LEVELS,
            'countries'      => \Symfony\Component\Intl\Countries::getNames(),
        ]);
    }

    private function shape(Scholarship $s, bool $detail): array
    {
        $base = [
            'id'             => $s->id,
            'title'          => $s->title,
            'slug'           => $s->slug,
            'type'           => $s->type,
            'type_label'     => ScholarshipConstants::TYPES[$s->type] ?? $s->type,
            'country_code'   => $s->country_code,
            'country_name'   => $s->country_name,
            'flag_url'       => $s->flagUrl(80),
            'funding_type'   => $s->funding_type,
            'funding_label'  => ScholarshipConstants::FUNDING_TYPES[$s->funding_type] ?? $s->funding_type,
            'program_level'  => $s->program_level,
            'program_label'  => $s->program_level ? (ScholarshipConstants::PROGRAM_LEVELS[$s->program_level] ?? $s->program_level) : null,
            'deadline'       => $s->deadline?->toDateString(),
            'days_left'      => $s->daysToDeadline(),
            'is_expired'     => $s->isExpired(),
            'is_featured'    => $s->is_featured,
            'short_description' => $s->short_description,
        ];

        if ($detail) {
            $base += [
                'eligibility_criteria' => $s->eligibility_criteria,
                'benefits'             => $s->benefits,
                'required_documents'   => $s->required_documents,
                'official_link_1'      => $s->official_link_1,
                'official_link_2'      => $s->official_link_2,
            ];
        }

        return $base;
    }
}
