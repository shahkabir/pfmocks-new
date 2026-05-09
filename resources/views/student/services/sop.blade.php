@extends('layouts.app')

@section('title', 'SOP Service')

@section('content')
<style>
    .sop-hero {
        background: linear-gradient(135deg, #0d6efd, #0a58ca);
        color: #fff;
        border-radius: 14px;
        padding: 24px 28px;
        box-shadow: 0 8px 24px rgba(13,110,253,.18);
        margin-bottom: 18px;
    }
    .sop-card {
        border:1px solid #e9ecef; border-radius:12px; background:#fff;
        transition:transform .15s, box-shadow .2s;
    }
    .sop-card:hover { transform:translateY(-2px); box-shadow:0 6px 18px rgba(13,110,253,.08); }
    .sop-card.review { border-left:4px solid #0dcaf0; }
    .sop-card.new    { border-left:4px solid #198754; }
    .sop-card .price {
        font-size:1.6rem; font-weight:800; color:#0d6efd; line-height:1;
    }
    .sop-card .price small { font-size:.8rem; color:#6c757d; font-weight:500; }

    .chip {
        display:inline-flex; align-items:center; gap:5px;
        font-size:11px; font-weight:600; padding:3px 10px;
        border-radius:999px; letter-spacing:.3px; text-transform:uppercase;
        border:1px solid transparent;
    }
    .chip-pending   { background:#fff3cd; color:#664d03; border-color:#ffe69c; }
    .chip-approved  { background:#d1e7dd; color:#0a5934; border-color:#a3cfbb; }
    .chip-rejected  { background:#f8d7da; color:#58151c; border-color:#f1aeb5; }
    .chip-completed { background:#cff4fc; color:#055160; border-color:#9eeaf9; }
    .chip-review    { background:#cff4fc; color:#055160; border-color:#9eeaf9; }
    .chip-new       { background:#d1e7dd; color:#0a5934; border-color:#a3cfbb; }

    .btn-buy-sop {
        background:#198754; border-color:#198754; color:#fff;
        font-weight:600; font-size:13px; padding:6px 14px;
        border-radius:6px; transition:background-color .15s, box-shadow .15s;
    }
    .btn-buy-sop:hover { background:#146c43; border-color:#146c43; color:#fff; box-shadow:0 3px 8px rgba(25,135,84,.25); }

    /* Modal */
    #sopModal .modal-content { border-radius:14px; overflow:hidden; border:none; box-shadow:0 20px 60px rgba(0,0,0,.25); }
    #sopModal .modal-header  { background:linear-gradient(135deg,#0d6efd,#0a58ca); color:#fff; border:none; }
    #sopModal .bkash-pill {
        background:#fff; color:#e2136e; font-weight:700;
        border-radius:999px; padding:2px 10px; font-size:.75rem;
        display:inline-flex; align-items:center; gap:4px;
    }
    #sopModal .step-number {
        display:inline-flex; align-items:center; justify-content:center;
        width:22px; height:22px; background:#0d6efd; color:#fff;
        border-radius:50%; font-size:11px; font-weight:700; flex-shrink:0;
    }
    #sopModal .step { display:flex; gap:10px; margin-bottom:6px; font-size:.85rem; }
    #sopModal .msisdn-box {
        background:#fff3f7; border:1px dashed #e2136e;
        padding:8px 12px; border-radius:8px;
        font-weight:700; color:#e2136e; font-size:.95rem; letter-spacing:.5px;
        display:flex; justify-content:space-between; align-items:center;
    }

    .submission-row {
        background:#fff; border:1px solid #e9ecef; border-radius:10px;
        padding:14px 16px; margin-bottom:10px;
    }
    .submission-row .meta { font-size:.85rem; color:#495057; }
    .submission-row .meta strong { color:#212529; }
</style>

<div class="container-fluid py-3">

    {{-- HERO --}}
    <div class="sop-hero">
        <div class="row align-items-center g-3">
            <div class="col-md-8">
                <h4 class="fw-bold mb-2">
                    <i class="bi bi-file-earmark-text-fill me-2"></i>Statement of Purpose Service
                </h4>
                <p class="mb-0" style="opacity:.92;">
                    Get your SOP reviewed by an expert evaluator, or have one written from scratch
                    based on your résumé and target university. Final SOP file is yours to download.
                </p>
            </div>
            <div class="col-md-4 text-md-end">
                <i class="bi bi-mortarboard-fill" style="font-size:3.5rem;opacity:.35;"></i>
            </div>
        </div>
    </div>

    {{-- SERVICE CARDS --}}
    <div class="row g-3 mb-4">
        @forelse($modules as $module)
            @php
                $isReview = $module->module_type === 'sop_review';
                $cls      = $isReview ? 'review' : 'new';
            @endphp
            <div class="col-md-6">
                <div class="sop-card {{ $cls }} h-100 p-3">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h5 class="fw-bold mb-0">
                            <i class="bi bi-{{ $isReview ? 'pencil-square' : 'pen-fill' }} me-1 text-primary"></i>
                            {{ $module->name }}
                        </h5>
                        <span class="chip chip-{{ $cls }}">
                            {{ $isReview ? 'Review existing' : 'Write new' }}
                        </span>
                    </div>
                    <p class="text-muted small mb-2">
                        @if($isReview)
                            Upload your existing SOP — our evaluator will refine it and send back the final version.
                        @else
                            Upload your résumé and university details — our evaluator will draft your SOP from scratch.
                        @endif
                    </p>

                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="price">৳ {{ number_format((float) $module->price_in_bdt, 0) }}
                                <small>BDT</small>
                            </div>
                        </div>
                        <button type="button" class="btn btn-buy-sop sop-buy-btn"
                                data-module-id="{{ $module->id }}"
                                data-module-name="{{ $module->name }}"
                                data-service-type="{{ $isReview ? 'review' : 'new' }}"
                                data-price="{{ (float) $module->price_in_bdt }}">
                            <i class="bi bi-bag-plus-fill me-1"></i>Buy now
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle-fill me-1"></i>
                    SOP service hasn't been set up yet. Ask your admin to run the SOP seeder.
                </div>
            </div>
        @endforelse
    </div>

    {{-- MY SUBMISSIONS --}}
    @if($submissions->isNotEmpty())
        <h5 class="fw-bold mb-2"><i class="bi bi-clock-history me-1"></i>My SOP requests</h5>
        @foreach($submissions as $s)
            @php
                $payment = $s->payment;
                $userExam = $s->userExam;

                $payStatus = $payment?->status;
                $examStatus = $userExam?->status;

                $statusChip = match (true) {
                    $payStatus === 'rejected' => '<span class="chip chip-rejected"><i class="bi bi-x-circle-fill"></i> Rejected</span>',
                    $payStatus === 'pending_verification' => '<span class="chip chip-pending"><i class="bi bi-hourglass-split"></i> Awaiting payment verification</span>',
                    $s->final_sop_path => '<span class="chip chip-completed"><i class="bi bi-check-circle-fill"></i> Final SOP ready</span>',
                    $examStatus === 'purchased' => '<span class="chip chip-approved"><i class="bi bi-check-circle-fill"></i> Paid · evaluator working</span>',
                    default => '<span class="chip chip-pending">Pending</span>',
                };
            @endphp
            <div class="submission-row">
                <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                    <div>
                        <strong>{{ $s->module->name }}</strong>
                        <span class="chip chip-{{ $s->service_type }} ms-1">{{ ucfirst($s->service_type) }}</span>
                    </div>
                    {!! $statusChip !!}
                </div>
                <div class="meta">
                    <strong>University:</strong> {{ $s->intended_university }} ·
                    <strong>Country:</strong> {{ $s->country }} ·
                    <strong>Submitted:</strong> {{ $s->created_at?->format('Y-m-d H:i') }}
                </div>
                <div class="d-flex flex-wrap gap-2 mt-2">
                    <a href="{{ route('sop.download', [$s->id, 'resume']) }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-file-earmark-arrow-down me-1"></i>My résumé
                    </a>
                    @if($s->original_sop_path)
                        <a href="{{ route('sop.download', [$s->id, 'original-sop']) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-file-earmark-arrow-down me-1"></i>My original SOP
                        </a>
                    @endif
                    @if($s->final_sop_path)
                        <a href="{{ route('sop.download', [$s->id, 'final-sop']) }}" class="btn btn-sm btn-success">
                            <i class="bi bi-cloud-download-fill me-1"></i>Download final SOP
                        </a>
                    @endif
                </div>
            </div>
        @endforeach
    @endif
</div>

{{-- ============ PURCHASE MODAL ============ --}}
<div class="modal fade" id="sopModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form id="sopForm" enctype="multipart/form-data" class="modal-content">
            @csrf
            <div class="modal-header">
                <div>
                    <h5 class="modal-title fw-bold mb-0">
                        <span class="bkash-pill"><i class="bi bi-phone-fill"></i>bKash</span>
                        &nbsp;<span id="sopModalTitle">SOP Service</span>
                    </h5>
                    <div class="small" style="opacity:.85;" id="sopModalSubtitle">—</div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div id="sopAlert" class="alert alert-danger d-none mb-3"></div>

                <input type="hidden" name="module_id" id="sopModuleId">
                <input type="hidden" name="service_type" id="sopServiceType">

                <div class="row g-3">
                    {{-- LEFT: Form fields --}}
                    <div class="col-md-7">
                        <h6 class="fw-bold mb-2"><i class="bi bi-pencil-square me-1"></i>Your details</h6>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Intended University <span class="text-danger">*</span></label>
                            <input type="text" name="intended_university" class="form-control" required maxlength="200"
                                   placeholder="e.g. University of Toronto">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Country <span class="text-danger">*</span></label>
                            <input type="text" name="country" class="form-control" required maxlength="100"
                                   placeholder="e.g. Canada">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">
                                Résumé (PDF) <span class="text-danger">*</span>
                            </label>
                            <input type="file" name="resume" class="form-control" required
                                   accept=".pdf,.doc,.docx">
                        </div>
                        <div class="mb-3" id="originalSopRow" style="display:none;">
                            <label class="form-label fw-semibold small">
                                Existing SOP file (PDF) <span class="text-danger">*</span>
                            </label>
                            <input type="file" name="original_sop" class="form-control"
                                   accept=".pdf,.doc,.docx" id="originalSopInput">
                            <div class="form-text">Required for the SOP Review service.</div>
                        </div>
                    </div>

                    {{-- RIGHT: Payment --}}
                    <div class="col-md-5">
                        <h6 class="fw-bold mb-2"><i class="bi bi-cash-coin me-1"></i>Payment</h6>

                        <div class="mb-2">
                            <div class="small text-muted fw-semibold">Amount to pay</div>
                            <div class="fs-4 fw-bold text-success">৳ <span id="sopAmount">0.00</span></div>
                        </div>

                        <div class="msisdn-box mb-2">
                            <div>
                                <div class="small text-muted">bKash payee</div>
                                <div id="sopPayee">{{ $payeeMsisdn }}</div>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-secondary"
                                    onclick="navigator.clipboard.writeText(document.getElementById('sopPayee').innerText)">
                                <i class="bi bi-clipboard"></i>
                            </button>
                        </div>

                        <div class="mb-2">
                            <div class="step"><span class="step-number">1</span>Open bKash, tap <b>Make Payment</b></div>
                            <div class="step"><span class="step-number">2</span>Enter the payee number above</div>
                            <div class="step"><span class="step-number">3</span>Pay the exact amount</div>
                            <div class="step"><span class="step-number">4</span>Paste the Trx ID below</div>
                        </div>

                        <div class="mb-2">
                            <label class="form-label fw-semibold small">Your bKash # (optional)</label>
                            <input type="tel" name="sender_msisdn" class="form-control form-control-sm"
                                   placeholder="01XXXXXXXXX" maxlength="20">
                        </div>
                        <div class="mb-2">
                            <label class="form-label fw-semibold small">
                                Transaction ID <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="transaction_id" class="form-control form-control-sm"
                                   required minlength="4" maxlength="80" placeholder="e.g. BJA1234ABCD" autocomplete="off">
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-buy-sop" id="sopSubmitBtn">
                    <i class="bi bi-check2-circle me-1"></i>Submit for Verification
                </button>
            </div>
        </form>
    </div>
</div>

<script>
$(function () {
    const sopModal = new bootstrap.Modal(document.getElementById('sopModal'));
    const $alert   = $('#sopAlert');

    $('.sop-buy-btn').on('click', function () {
        const $btn        = $(this);
        const mid         = $btn.data('module-id');
        const mname       = $btn.data('module-name');
        const serviceType = $btn.data('service-type');
        const price       = parseFloat($btn.data('price') || 0);

        $alert.addClass('d-none').text('');
        $('#sopForm')[0].reset();
        $('#sopModuleId').val(mid);
        $('#sopServiceType').val(serviceType);
        $('#sopModalTitle').text(mname);
        $('#sopModalSubtitle').text(serviceType === 'review' ? 'Review existing SOP' : 'Write new SOP');
        $('#sopAmount').text(price.toFixed(2));

        // Show/hide existing-SOP upload based on service type
        if (serviceType === 'review') {
            $('#originalSopRow').show();
            $('#originalSopInput').prop('required', true);
        } else {
            $('#originalSopRow').hide();
            $('#originalSopInput').prop('required', false);
        }

        sopModal.show();
    });

    $('#sopForm').on('submit', function (e) {
        e.preventDefault();
        const $btn = $('#sopSubmitBtn');
        const orig = $btn.html();
        $btn.prop('disabled', true).html('<i class="bi bi-arrow-repeat me-1"></i>Submitting…');
        $alert.addClass('d-none');

        const fd = new FormData(this);

        $.ajax({
            url: '{{ route("sop.submit") }}',
            type: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            data: fd,
            processData: false,
            contentType: false,
            success: function (res) {
                sopModal.hide();
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Submitted',
                        text: res.message,
                        confirmButtonText: 'OK',
                    }).then(() => window.location.reload());
                } else {
                    alert(res.message);
                    window.location.reload();
                }
            },
            error: function (xhr) {
                let msg = xhr.responseJSON?.message || 'Could not submit. Please try again.';
                if (xhr.responseJSON?.errors) {
                    msg = Object.values(xhr.responseJSON.errors).flat().join('\n');
                }
                $alert.text(msg).removeClass('d-none');
            },
            complete: function () {
                $btn.prop('disabled', false).html(orig);
            }
        });
    });
});
</script>
@endsection
