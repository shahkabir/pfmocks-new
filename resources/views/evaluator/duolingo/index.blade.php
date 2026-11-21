@extends('layouts.app')

@section('title', 'Duolingo Evaluations')

@section('content')
<div class="container-fluid py-3">
    @foreach(['success' => 'success', 'error' => 'danger'] as $key => $class)
        @if(session($key))
            <div class="alert alert-{{ $class }} alert-dismissible fade show">
                {{ session($key) }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    @endforeach

    <div class="mb-3">
        <h4 class="mb-0 fw-bold"><i class="bi bi-translate text-primary me-2"></i>Duolingo Evaluations</h4>
        <small class="text-muted">
            Score the writing and speaking responses, then publish the result to the student.
            Unassigned tests are claimed by the first evaluator who opens them.
        </small>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Student</th>
                        <th>Test</th>
                        <th>Submitted</th>
                        <th>Evaluator</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($evaluations as $ev)
                        <tr>
                            <td>{{ $ev->id }}</td>
                            <td>
                                <div class="fw-semibold">{{ $ev->attempt?->user?->name }}</div>
                                <div class="small text-muted">{{ $ev->attempt?->user?->email }}</div>
                            </td>
                            <td>{{ $ev->attempt?->module?->name }}</td>
                            <td>{{ $ev->attempt?->ended_at?->format('Y-m-d H:i') }}</td>
                            <td>{{ $ev->evaluator?->name ?? 'Unassigned' }}</td>
                            <td>
                                @switch($ev->status)
                                    @case('completed')
                                        <span class="badge bg-success">Published</span>
                                        @break
                                    @case('in_progress')
                                        <span class="badge bg-info text-dark">In progress</span>
                                        @break
                                    @default
                                        <span class="badge bg-warning text-dark">Waiting</span>
                                @endswitch
                            </td>
                            <td class="text-end">
                                <a href="{{ route('evaluator.duolingo.show', $ev->id) }}" class="btn btn-sm btn-primary">
                                    {{ $ev->status === 'completed' ? 'Review' : 'Evaluate' }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">No Duolingo tests are waiting for evaluation.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
