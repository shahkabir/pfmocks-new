@extends('layouts.app')

@section('title', 'Payment Details')

@section('content')
@php /** @var \App\Models\Payment\Payment $payment */ @endphp
<div class="container-fluid mt-3">
    <div class="card shadow-sm" style="max-width:780px">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Payment #{{ $payment->id }}</h5>
            <a href="{{ route('admin.payments.index') }}" class="btn btn-sm btn-outline-secondary">← Back</a>
        </div>
        <div class="card-body">

            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="fw-semibold small text-muted">Status</div>
                    <div>
                        @switch($payment->status)
                            @case('approved')
                                <span class="badge bg-success">Approved</span>
                                @break
                            @case('rejected')
                                <span class="badge bg-danger">Rejected</span>
                                @break
                            @default
                                <span class="badge bg-warning text-dark">Pending Verification</span>
                        @endswitch
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="fw-semibold small text-muted">Submitted</div>
                    <div>{{ $payment->created_at?->format('Y-m-d H:i') }}</div>
                </div>
            </div>

            <hr>

            <div class="row g-3">
                <div class="col-md-6">
                    <div class="fw-semibold small text-muted">User</div>
                    <div>{{ $payment->user?->name }}</div>
                    <div class="small text-muted">{{ $payment->user?->email }}</div>
                    <div class="small text-muted">{{ $payment->user?->mobile }}</div>
                </div>

                <div class="col-md-6">
                    <div class="fw-semibold small text-muted">Exam / Module</div>
                    <div>{{ $payment->module?->exam?->name }}</div>
                    <div class="small text-muted">{{ $payment->module?->name }}</div>
                </div>

                <div class="col-md-6">
                    <div class="fw-semibold small text-muted">Payment Method</div>
                    <div class="text-capitalize">{{ $payment->payment_method }}</div>
                </div>

                <div class="col-md-6">
                    <div class="fw-semibold small text-muted">Amount</div>
                    <div>৳ {{ number_format((float) $payment->amount, 2) }}</div>
                </div>

                <div class="col-md-6">
                    <div class="fw-semibold small text-muted">Transaction ID</div>
                    <div class="font-monospace">{{ $payment->transaction_id }}</div>
                </div>

                <div class="col-md-6">
                    <div class="fw-semibold small text-muted">Sender MSISDN</div>
                    <div>{{ $payment->sender_msisdn ?: '—' }}</div>
                </div>

                @if($payment->admin_note)
                <div class="col-12">
                    <div class="fw-semibold small text-muted">Admin Note</div>
                    <div class="alert alert-light border mb-0">{{ $payment->admin_note }}</div>
                </div>
                @endif

                @if($payment->verifier)
                <div class="col-md-6">
                    <div class="fw-semibold small text-muted">Verified By</div>
                    <div>{{ $payment->verifier->name }} @ {{ $payment->verified_at?->format('Y-m-d H:i') }}</div>
                </div>
                @endif
            </div>

            @if($payment->status === \App\Models\Payment\Payment::STATUS_PENDING)
                <hr>
                <div class="d-flex gap-2">
                    <form method="POST" action="{{ route('admin.payments.approve', $payment->id) }}"
                          onsubmit="return confirm('Approve this payment and unlock the exam?');">
                        @csrf
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-lg me-1"></i>Approve & unlock exam
                        </button>
                    </form>
                    <button type="button" class="btn btn-danger"
                            data-bs-toggle="modal" data-bs-target="#rejectModal">
                        <i class="bi bi-x-lg me-1"></i>Reject
                    </button>
                </div>

                <div class="modal fade" id="rejectModal" tabindex="-1">
                    <div class="modal-dialog">
                        <form method="POST" action="{{ route('admin.payments.reject', $payment->id) }}"
                              class="modal-content">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title">Reject payment</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <label class="form-label fw-semibold">Reason (required)</label>
                                <textarea name="admin_note" class="form-control" rows="3" required
                                          placeholder="e.g. Transaction ID not found in bKash statement"></textarea>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-danger">Reject</button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
