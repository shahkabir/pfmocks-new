<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Referral\ReferralProgram;
use App\Repositories\Interfaces\ReferralProgramRepositoryInterface;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ReferralProgramController extends Controller
{
    public function __construct(private ReferralProgramRepositoryInterface $programs) {}

    public function index()
    {
        return view('admin.referral-programs.index');
    }

    public function list(Request $request)
    {
        $query = ReferralProgram::query()->orderByDesc('id');

        return DataTables::eloquent($query)
            ->editColumn('referee_discount_value', function (ReferralProgram $p) {
                return $p->referee_discount_type === 'percent'
                    ? rtrim(rtrim((string) $p->referee_discount_value, '0'), '.') . '%'
                    : '৳ ' . number_format((float) $p->referee_discount_value, 2);
            })
            ->editColumn('referrer_reward_value', function (ReferralProgram $p) {
                if (!$p->referrer_reward_type) return '—';
                return $p->referrer_reward_type === 'percent'
                    ? rtrim(rtrim((string) $p->referrer_reward_value, '0'), '.') . '%'
                    : '৳ ' . number_format((float) $p->referrer_reward_value, 2);
            })
            ->editColumn('is_active', fn (ReferralProgram $p) =>
                $p->is_active
                    ? '<span class="badge bg-success">Active</span>'
                    : '<span class="badge bg-secondary">Inactive</span>'
            )
            ->editColumn('starts_at', fn (ReferralProgram $p) => $p->starts_at?->format('Y-m-d') ?? '—')
            ->editColumn('ends_at',   fn (ReferralProgram $p) => $p->ends_at?->format('Y-m-d') ?? '—')
            ->addColumn('action', function (ReferralProgram $p) {
                return view('admin.referral-programs.partials._actions', ['p' => $p])->render();
            })
            ->rawColumns(['is_active', 'action'])
            ->toJson();
    }

    public function create()
    {
        return view('admin.referral-programs.create');
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $this->programs->create($data);
        return redirect()->route('admin.referral-programs.index')
                         ->with('success', 'Referral program created.');
    }

    public function edit(int $id)
    {
        $program = $this->programs->find($id);
        return view('admin.referral-programs.edit', compact('program'));
    }

    public function update(Request $request, int $id)
    {
        $data = $this->validateData($request);
        $this->programs->update($id, $data);
        return redirect()->route('admin.referral-programs.index')
                         ->with('success', 'Referral program updated.');
    }

    public function destroy(int $id)
    {
        $this->programs->delete($id);
        return redirect()->route('admin.referral-programs.index')
                         ->with('success', 'Referral program deleted.');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'name'                      => 'required|string|max:120',
            'referee_discount_type'     => 'required|in:fixed,percent',
            'referee_discount_value'    => 'required|numeric|min:0',
            'referrer_reward_type'      => 'nullable|in:fixed,percent',
            'referrer_reward_value'     => 'nullable|numeric|min:0',
            'min_first_purchase_amount' => 'nullable|numeric|min:0',
            'max_discount_amount'       => 'nullable|numeric|min:0',
            'starts_at'                 => 'nullable|date',
            'ends_at'                   => 'nullable|date|after_or_equal:starts_at',
            'is_active'                 => 'sometimes|boolean',
        ]) + ['is_active' => $request->boolean('is_active')];
    }
}
