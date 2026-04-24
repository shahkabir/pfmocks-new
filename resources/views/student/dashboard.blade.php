@extends('layouts.app')

@section('title', 'My Exams')

@section('content')
<style>
    .exam-card {
        border: 1px solid #e9ecef;
        border-radius: 12px;
        background: #fff;
        transition: transform .15s, box-shadow .2s;
        overflow: hidden;
        height: 100%;
    }
    .exam-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(13,110,253,.08);
    }

    .exam-card .card-header {
        background: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
        padding: 12px 16px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 8px;
    }
    .exam-card .card-header h5 {
        font-size: .95rem;
        font-weight: 700;
        margin: 0;
        color: #212529;
        line-height: 1.2;
    }
    .exam-card .card-body {
        padding: 12px 16px;
    }
    .exam-card .card-footer {
        background: transparent;
        border-top: 1px solid #f1f3f5;
        padding: 10px 16px;
    }

    /* Left accent stripe by exam type */
    .exam-card.type-free   { border-left: 4px solid #198754; }
    .exam-card.type-paid   { border-left: 4px solid #0d6efd; }

    /* ===== Chips ===== */
    .chip {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 11px;
        font-weight: 600;
        padding: 3px 10px;
        border-radius: 999px;
        letter-spacing: .3px;
        text-transform: uppercase;
        border: 1px solid transparent;
    }
    .chip i { font-size: 12px; }

    .chip-free       { background: #d1e7dd; color: #0a5934; border-color: #a3cfbb; }
    .chip-paid       { background: #cfe2ff; color: #084298; border-color: #9ec5fe; }

    .chip-completed  { background: #cff4fc; color: #055160; border-color: #9eeaf9; }
    .chip-purchased  { background: #e0cffc; color: #432874; border-color: #c5a3f7; }
    .chip-pending    { background: #fff3cd; color: #664d03; border-color: #ffe69c; }
    .chip-cancelled  { background: #f8d7da; color: #58151c; border-color: #f1aeb5; }

    .status-dot {
        width: 8px; height: 8px;
        border-radius: 50%;
        display: inline-block;
        flex-shrink: 0;
    }
    .status-dot.pending {
        background-color: #ffc107;
        animation: pulse 1.5s infinite;
    }
    @keyframes pulse {
        0%   { box-shadow: 0 0 0 0 rgba(255,193,7,.5); }
        70%  { box-shadow: 0 0 0 6px rgba(255,193,7,0); }
        100% { box-shadow: 0 0 0 0 rgba(255,193,7,0); }
    }

    /* ===== Info rows ===== */
    .info-row {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: .82rem;
        color: #495057;
        margin-bottom: 6px;
    }
    .info-row:last-child { margin-bottom: 0; }
    .info-row i {
        color: #6c757d;
        font-size: 13px;
        width: 16px;
        text-align: center;
    }
    .info-row .info-label {
        color: #6c757d;
        font-weight: 500;
    }
    .info-row .info-value {
        color: #212529;
        font-weight: 600;
        margin-left: auto;
    }

    /* ===== Action buttons ===== */
    .btn-action {
        font-size: 12px;
        font-weight: 600;
        padding: 5px 14px;
        border-radius: 6px;
        transition: transform .1s, box-shadow .15s;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    .btn-action:hover:not(:disabled) {
        transform: translateY(-1px);
        box-shadow: 0 3px 8px rgba(0,0,0,.12);
    }

    .btn-start {
        background: #198754;
        border-color: #198754;
        color: #fff;
    }
    .btn-start:hover  { background: #146c43; border-color: #146c43; color: #fff; }

    .btn-result {
        background: #0dcaf0;
        border-color: #0dcaf0;
        color: #05505c;
    }
    .btn-result:hover { background: #31d2f2; border-color: #25cff2; color: #05505c; }

    .text-small { font-size: .8rem; }
</style>

<div class="container-fluid py-3">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0 fw-bold">My Exams</h4>
            <small class="text-muted">Your purchased and free exam modules</small>
        </div>
        <span class="chip chip-paid">
            <i class="bi bi-collection"></i>
            {{ count($exams ?? []) }} {{ \Illuminate\Support\Str::plural('Exam', count($exams ?? [])) }}
        </span>
    </div>

    <div class="row g-3">
        @forelse($exams as $userExam)
            @php
                $type   = $userExam->type;
                $status = $userExam->status;
            @endphp
            <div class="col-md-6 col-lg-4">
                <div class="exam-card type-{{ $type }}">
                    <div class="card-header">
                        <h5 title="{{ $userExam->module->exam->name ?? '' }}">
                            <i class="bi bi-mortarboard-fill text-primary me-1"></i>
                            {{ $userExam->module->exam->name ?? '—' }}
                        </h5>
                        <span class="chip">
                            {{-- $type === 'free' ? 'chip-free' : 'chip-paid' --}}
                            <i class="bi bi-{{ $type === 'free' ? 'gift-fill' : 'currency-dollar' }}"></i>
                            {{ strtoupper($type) }}
                        </span>
                    </div>

                    <div class="card-body">
                        <div class="info-row">
                            <i class="bi bi-bookmark-star-fill"></i>
                            <span class="info-label">Module</span>
                            <span class="info-value text-truncate" style="max-width:60%;"
                                  title="{{ $userExam->module->name }}">
                                {{ $userExam->module->name }}
                            </span>
                        </div>
                        <div class="info-row">
                            <i class="bi bi-clock"></i>
                            <span class="info-label">Duration</span>
                            <span class="info-value">{{ $userExam->module->duration_minutes }} min</span>
                        </div>
                        <div class="info-row">
                            <i class="bi bi-cash-coin"></i>
                            <span class="info-label">Price</span>
                            <span class="info-value">
                                @if($type === 'paid')
                                    ৳{{ number_format($userExam->price, 2) }}
                                @else
                                    Free
                                @endif
                            </span>
                        </div>
                        <div class="info-row">
                            <i class="bi bi-activity"></i>
                            <span class="info-label">Status</span>
                            <span class="info-value">
                                @switch($status)
                                    @case('completed')
                                        <span class="chip chip-completed"><i class="bi bi-check-circle-fill"></i> Completed</span>
                                        @break
                                    @case('purchased')
                                        <span class="chip chip-purchased"><i class="bi bi-bag-check-fill"></i> Purchased</span>
                                        @break
                                    @case('payment_pending')
                                        <span class="chip chip-pending">
                                            <span class="status-dot pending"></span> Pending
                                        </span>
                                        @break
                                    @case('cancelled')
                                        <span class="chip chip-cancelled"><i class="bi bi-x-circle-fill"></i> Cancelled</span>
                                        @break
                                    @case('free')
                                        <span class="chip chip-free"><i class="bi bi-unlock-fill"></i> Available</span>
                                        @break
                                    @default
                                        <span class="chip chip-completed">{{ ucfirst($status) }}</span>
                                @endswitch
                            </span>
                        </div>
                    </div>

                    <div class="card-footer text-end">
                        @if($status === 'free' || $status === 'purchased')
                            <a href="{{ route('exam.start', $userExam->module_id) }}"
                               target="_blank"
                               class="btn btn-action btn-start">
                                <i class="bi bi-play-fill"></i> Start Exam
                            </a>
                        @elseif($status === 'completed')
                            <a href="{{ route('exam.result', $userExam->id) }}"
                               target="_blank"
                               class="btn btn-action btn-result">
                                <i class="bi bi-clipboard-data-fill"></i> Show Result
                            </a>
                        @elseif($status === 'payment_pending')
                            <span class="text-warning text-small fw-semibold">
                                <i class="bi bi-hourglass-split me-1"></i>Awaiting payment verification
                            </span>
                        @elseif($status === 'cancelled')
                            <span class="text-danger text-small fw-semibold">
                                <i class="bi bi-slash-circle me-1"></i>Exam cancelled
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info d-flex align-items-center">
                    <i class="bi bi-info-circle-fill me-2"></i>
                    No exams available yet.
                </div>
            </div>
        @endforelse
    </div>

</div>
@endsection
