<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Exam\Exam;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * Public, read-only API consumed by the external business website.
 * Returns active exams with their modules (name, info, price).
 */
class ExamApiController extends Controller
{
    /** GET /api/exams  — list active exams with nested modules. Filter by ?tag=ielts */
    public function index(Request $request): JsonResponse
    {
        $query = Exam::with(['modules' => function ($q) {
            $q->orderBy('id');
        }])->where('is_active', true)
           ->where('is_public_visible', true);

        if ($tag = $request->query('tag')) {
            $query->where('tag', $tag);
        }

        $exams = $query->orderBy('name')->get();

        return response()->json([
            'data' => $exams->map(fn (Exam $e) => $this->shape($e))->values(),
        ]);
    }

    /** GET /api/exams/{tag}  — single exam (by tag) with its modules */
    public function show(string $tag): JsonResponse
    {
        $exam = Exam::with(
                ['modules' => fn ($q) => $q->orderBy('id')]
            )
            ->where('tag', $tag)
            ->where('is_active', true)
            ->where('is_public_visible', true)
            ->get();

        return response()->json(
            ['data' => $this->shape($exam)]
        );
    }

    private function shape(Collection $exams): array
    {
        return $exams->map(function ($exam){
        
            return [
                'id'               => $exam->id,
                'name'             => $exam->name,
                'tag'              => $exam->tag,
                'exam_information' => $exam->exam_information,
                'modules'          => $exam->modules->map(fn ($m) => [
                        'id'                 => $m->id,
                        'name'                => $m->name,
                        'module_type'         => $m->module_type,
                        'module_information'  => $m->module_information,
                        'type'                => $m->type, // free | paid
                        'duration_minutes'    => (int) $m->duration_minutes,
                        'price_in_bdt'        => (float) $m->price_in_bdt,
                        'price_in_usd'        => (float) $m->price_in_usd,
                    ])->values(),
            ];
        })->values()->toArray();
    }
}
