@extends('layouts.app')

@section('title', 'My Evaluations')

@section('content')
<style>
    .eval-card {
        border: 1px solid #e9ecef;
        border-radius: 12px;
        background: #fff;
        transition: transform .15s, box-shadow .2s;
        height: 100%;
    }
    .eval-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(13,110,253,.08);
    }
    .eval-card .card-header {
        background: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
        padding: 12px 16px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 8px;
    }
    .eval-card .card-body { padding: 12px 16px; }

    .chip {
        display:inline-flex; align-items:center; gap:5px;
        font-size:11px; font-weight:600; padding:3px 10px;
        border-radius:999px; letter-spacing:.3px;
        text-transform:uppercase; border:1px solid transparent;
    }
    .chip-assigned    { background:#fff3cd; color:#664d03; border-color:#ffe69c; }
    .chip-progress    { background:#cff4fc; color:#055160; border-color:#9eeaf9; }
    .chip-completed   { background:#d1e7dd; color:#0a5934; border-color:#a3cfbb; }
    .chip-writing     { background:#cfe2ff; color:#084298; border-color:#9ec5fe; }
    .chip-speaking    { background:#fde0fc; color:#6f1e7a; border-color:#f1aef1; }

    .info-row { display:flex; align-items:center; gap:8px; font-size:.82rem; color:#495057; margin-bottom:6px; }
    .info-row:last-child { margin-bottom:0; }
    .info-row i { color:#6c757d; font-size:13px; width:16px; text-align:center; }
    .info-row .info-label { color:#6c757d; font-weight:500; }
    .info-row .info-value { color:#212529; font-weight:600; margin-left:auto; }
</style>

<div class="container-fluid py-3">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle-fill me-1"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0 fw-bold"><i class="bi bi-clipboard-check text-primary me-2"></i>My Evaluations</h4>
            <small class="text-muted">Exams assigned to you for review</small>
        </div>
    </div>

    <div class="row g-3">
        @forelse($evaluations as $ev)
            @php
                $student   = $ev->attempt?->user;
                $module    = $ev->attempt?->module;
                $exam      = $module?->exam;
                $type      = $ev->module_type;
                $statusKey = $ev->status;
            @endphp
            <div class="col-md-6 col-lg-4">
                <div class="eval-card">
                    <div class="card-header">
                        <h6 class="fw-bold mb-0">
                            <span class="chip chip-{{ $type }}">
                                <i class="bi bi-{{ $type === 'speaking' ? 'mic-fill' : 'pencil-fill' }}"></i>
                                {{ ucfirst($type) }}
                            </span>
                        </h6>
                        @switch($statusKey)
                            @case('completed')
                                <span class="chip chip-completed"><i class="bi bi-check-circle-fill"></i> Completed</span>
                                @break
                            @case('in_progress')
                                <span class="chip chip-progress"><i class="bi bi-hourglass-split"></i> In progress</span>
                                @break
                            @default
                                <span class="chip chip-assigned"><i class="bi bi-bell-fill"></i> Assigned</span>
                        @endswitch
                    </div>
                    <div class="card-body">
                        <div class="info-row">
                            <i class="bi bi-person-fill"></i>
                            <span class="info-label">Student</span>
                            <span class="info-value">{{ $student?->name }}</span>
                        </div>
                        <div class="info-row">
                            <i class="bi bi-mortarboard-fill"></i>
                            <span class="info-label">Exam</span>
                            <span class="info-value">{{ $exam?->name ?? '—' }}</span>
                        </div>
                        <div class="info-row">
                            <i class="bi bi-bookmark-star-fill"></i>
                            <span class="info-label">Module</span>
                            <span class="info-value">{{ $module?->name }}</span>
                        </div>
                        <div class="info-row">
                            <i class="bi bi-calendar-event"></i>
                            <span class="info-label">Assigned</span>
                            <span class="info-value">{{ $ev->assigned_at?->format('Y-m-d H:i') }}</span>
                        </div>
                    </div>
                    <div class="card-footer text-end" style="background:transparent;border-top:1px solid #f1f3f5;">
                        <a href="{{ route('evaluator.evaluations.show', $ev->id) }}"
                           class="btn btn-sm btn-primary">
                            <i class="bi bi-pencil-square me-1"></i>
                            {{ $statusKey === 'completed' ? 'Review' : 'Open' }}
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info"><i class="bi bi-info-circle-fill me-1"></i>No evaluations assigned yet.</div>
            </div>
        @endforelse
    </div>
</div>
@endsection
