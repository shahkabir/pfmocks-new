<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment\Payment;
use App\Repositories\Interfaces\PaymentRepositoryInterface;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class PaymentController extends Controller
{
    public function __construct(
        private PaymentRepositoryInterface $payments,
        private PaymentService             $service
    ) {}

    /** List page (admin) */
    public function index(): View
    {
        return view('admin.payments.index');
    }

    /** DataTables JSON feed */
    public function list(Request $request)
    {
        $query = Payment::query()
            ->with(['user:id,name,email,mobile', 'module:id,name,module_type', 'module.exam:id,name'])
            ->orderByDesc('id');

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        return DataTables::eloquent($query)
            ->addColumn('user_name', fn (Payment $p) => $p->user?->name)
            ->addColumn('user_email', fn (Payment $p) => $p->user?->email)
            ->addColumn('exam_name', fn (Payment $p) => $p->module?->exam?->name)
            ->addColumn('module_name', fn (Payment $p) => $p->module?->name)
            ->editColumn('amount', fn (Payment $p) => '৳ ' . number_format((float) $p->amount, 2))
            ->editColumn('status', function (Payment $p) {
                return match ($p->status) {
                    Payment::STATUS_APPROVED => '<span class="badge bg-success">Approved</span>',
                    Payment::STATUS_REJECTED => '<span class="badge bg-danger">Rejected</span>',
                    default                  => '<span class="badge bg-warning text-dark">Pending</span>',
                };
            })
            ->editColumn('created_at', fn (Payment $p) => $p->created_at?->format('Y-m-d H:i'))
            ->addColumn('action', function (Payment $p) {
                return view('admin.payments.partials._actions', ['p' => $p])->render();
            })
            ->rawColumns(['status', 'action'])
            ->toJson();
    }

    /** Admin views one payment (details) */
    public function show(int $id): View
    {
        $payment = Payment::with([
            'user:id,name,email,mobile',
            'module:id,name,module_type,price_in_bdt',
            'module.exam:id,name',
            'verifier:id,name',
        ])->findOrFail($id);

        return view('admin.payments.show', compact('payment'));
    }

    /** Admin approves */
    public function approve(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'admin_note' => 'nullable|string|max:500',
        ]);

        $this->service->approve($id, auth()->id(), $request->input('admin_note'));

        return redirect()
            ->route('admin.payments.index')
            ->with('success', 'Payment approved. Exam unlocked for the student.');
    }

    /** Admin rejects (requires reason) */
    public function reject(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'admin_note' => 'required|string|min:3|max:500',
        ]);

        $this->service->reject($id, auth()->id(), $request->input('admin_note'));

        return redirect()
            ->route('admin.payments.index')
            ->with('success', 'Payment rejected and student has been notified via dashboard status.');
    }
}
