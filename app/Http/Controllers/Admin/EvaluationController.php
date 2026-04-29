<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Evaluation\Evaluation;
use App\Models\Exam\ExamAttempt;
use App\Models\User;
use App\Services\EvaluationService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class EvaluationController extends Controller
{
    public function __construct(private EvaluationService $service) {}

    public function index()
    {
        $evaluators = User::where('role', 'evaluator')
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        return view('admin.evaluations.index', compact('evaluators'));
    }

    /**
     * DataTables JSON: every completed writing/speaking attempt + its evaluation status.
     */
    public function list(Request $request)
    {
        $query = ExamAttempt::query()
            ->join('modules', 'modules.id', '=', 'exam_attempts.module_id')
            ->join('users',   'users.id',   '=', 'exam_attempts.user_id')
            ->leftJoin('exams', 'exams.id', '=', 'modules.exam_id')
            ->leftJoin('evaluations', 'evaluations.exam_attempt_id', '=', 'exam_attempts.id')
            ->whereIn('modules.module_type', Evaluation::EVALUATABLE_MODULE_TYPES)
            ->where('exam_attempts.status', 'completed')
            ->select([
                'exam_attempts.id as attempt_id',
                'exam_attempts.ended_at',
                'modules.id          as module_id',
                'modules.name        as module_name',
                'modules.module_type as module_type',
                'exams.name          as exam_name',
                'users.id            as student_id',
                'users.name          as student_name',
                'users.email         as student_email',
                'evaluations.id            as evaluation_id',
                'evaluations.assigned_to   as evaluator_id',
                'evaluations.status        as evaluation_status',
            ])
            ->orderByDesc('exam_attempts.ended_at');

        if ($request->input('module_type')) {
            $query->where('modules.module_type', $request->input('module_type'));
        }
        if ($request->input('eval_status') === 'unassigned') {
            $query->whereNull('evaluations.id');
        } elseif ($request->input('eval_status')) {
            $query->where('evaluations.status', $request->input('eval_status'));
        }

        return DataTables::of($query)
            ->editColumn('module_type', fn ($row) => ucfirst($row->module_type))
            ->editColumn('ended_at', fn ($row) => $row->ended_at
                ? \Carbon\Carbon::parse($row->ended_at)->format('Y-m-d H:i')
                : '—')
            ->addColumn('evaluator_name', function ($row) {
                if (!$row->evaluator_id) return '—';
                return User::where('id', $row->evaluator_id)->value('name') ?? '—';
            })
            ->editColumn('evaluation_status', function ($row) {
                if (!$row->evaluation_id) {
                    return '<span class="badge bg-secondary">Unassigned</span>';
                }
                return match ($row->evaluation_status) {
                    Evaluation::STATUS_ASSIGNED    => '<span class="badge bg-warning text-dark">Assigned</span>',
                    Evaluation::STATUS_IN_PROGRESS => '<span class="badge bg-info text-dark">In progress</span>',
                    Evaluation::STATUS_COMPLETED   => '<span class="badge bg-success">Completed</span>',
                    default => '<span class="badge bg-secondary">—</span>',
                };
            })
            ->addColumn('action', function ($row) {
                return view('admin.evaluations.partials._actions', ['row' => $row])->render();
            })
            ->rawColumns(['evaluation_status', 'action'])
            ->toJson();
    }

    public function assign(Request $request)
    {
        $data = $request->validate([
            'exam_attempt_id' => 'required|integer|exists:exam_attempts,id',
            'evaluator_id'    => 'required|integer|exists:users,id',
        ]);

        try {
            $this->service->assign(
                examAttemptId:   $data['exam_attempt_id'],
                evaluatorUserId: $data['evaluator_id'],
                adminUserId:     auth()->id(),
            );
        } catch (\Throwable $e) {
            return back()->with('error', 'Could not assign: ' . $e->getMessage());
        }

        return back()->with('success', 'Evaluation assigned. Email notifications sent to evaluator and student.');
    }
}
