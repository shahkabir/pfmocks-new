<?php

namespace App\Http\Controllers\Admin;

use App\Constants\ScholarshipConstants;
use App\Http\Controllers\Controller;
use App\Models\Scholarship\Scholarship;
use App\Services\ScholarshipService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ScholarshipController extends Controller
{
    public function __construct(private ScholarshipService $service) {}

    public function index()
    {
        return view('admin.scholarships.index');
    }

    public function list(Request $request)
    {
        $query = Scholarship::query()->orderByDesc('id');

        if ($status = $request->input('type'))        $query->where('type', $status);
        if ($status = $request->input('funding'))     $query->where('funding_type', $status);
        if ($status = $request->input('is_active'))   $query->where('is_active', $status === '1');

        return DataTables::eloquent($query)
            ->editColumn('type', fn (Scholarship $s) => ScholarshipConstants::TYPES[$s->type] ?? $s->type)
            ->editColumn('country_name', function (Scholarship $s) {
                if (!$s->country_code) return '—';
                return '<img src="' . $s->flagUrl(20) . '" alt="" '
                     . 'style="width:18px;height:auto;border:1px solid #dee2e6;margin-right:6px;'
                     . 'border-radius:2px;vertical-align:middle;"> '
                     . e($s->country_name);
            })
            ->editColumn('funding_type', function (Scholarship $s) {
                $cls = $s->funding_type === 'fully_funded' ? 'bg-success' : 'bg-warning text-dark';
                return '<span class="badge ' . $cls . '">'
                     . (ScholarshipConstants::FUNDING_TYPES[$s->funding_type] ?? $s->funding_type)
                     . '</span>';
            })
            ->editColumn('deadline', fn (Scholarship $s) => $s->deadline?->format('Y-m-d') ?? '—')
            ->editColumn('is_active', fn (Scholarship $s) =>
                $s->is_active
                    ? '<span class="badge bg-success">Active</span>'
                    : '<span class="badge bg-secondary">Inactive</span>'
            )
            ->addColumn('action', function (Scholarship $s) {
                return view('admin.scholarships.partials._actions', ['s' => $s])->render();
            })
            ->rawColumns(['country_name', 'funding_type', 'is_active', 'action'])
            ->toJson();
    }

    public function create()
    {
        return view('admin.scholarships.create', [
            'countries' => $this->service->countryList(),
        ]);
    }

    public function store(Request $request)
    {
        $this->service->create($request->all(), auth()->id());
        return redirect()->route('admin.scholarships.index')
                         ->with('success', 'Scholarship created.');
    }

    public function edit(int $id)
    {
        return view('admin.scholarships.edit', [
            'scholarship' => Scholarship::findOrFail($id),
            'countries'   => $this->service->countryList(),
        ]);
    }

    public function update(Request $request, int $id)
    {
        $this->service->update($id, $request->all());
        return redirect()->route('admin.scholarships.index')
                         ->with('success', 'Scholarship updated.');
    }

    public function destroy(int $id)
    {
        $this->service->delete($id);
        return redirect()->route('admin.scholarships.index')
                         ->with('success', 'Scholarship deleted.');
    }
}
