@extends('layouts.app')

@section('title', 'Available Exams')

@section('content')
@php
    $userId = auth()->id();
    // Build a lookup: module_id => UserExam (for the current user)
    $userExams = collect();
    if ($userId) {
        $moduleIds = $exams->flatMap->modules->pluck('id')->all();
        $userExams = \App\Models\Exam\UserExam::where('user_id', $userId)
            ->whereIn('module_id', $moduleIds)
            ->get()
            ->keyBy('module_id');
    }
@endphp

<style>
    .exam-card {
        border: 1px solid #e9ecef;
        border-radius: 12px;
        background: #fff;
        transition: transform .15s, box-shadow .2s;
        overflow: hidden;
    }
    .exam-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(13,110,253,.08);
    }

    .exam-card-header {
        background: linear-gradient(135deg, #0d6efd, #0a58ca);
        color: #fff;
        padding: 14px 16px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 8px;
    }
    .exam-card-header h5 {
        font-size: 1rem;
        font-weight: 700;
        margin: 0;
    }

    .module-row {
        padding: 10px 12px;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        margin-bottom: 8px;
        transition: background-color .15s, border-color .15s;
    }
    .module-row:hover {
        background: #f8f9fa;
        border-color: #cfd6dc;
    }

    .chip {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 11px;
        font-weight: 600;
        padding: 3px 9px;
        border-radius: 999px;
        letter-spacing: .3px;
        text-transform: uppercase;
        border: 1px solid transparent;
    }
    .chip-free   { background:#d1e7dd; color:#0a5934; border-color:#a3cfbb; }
    .chip-paid   { background:#cfe2ff; color:#084298; border-color:#9ec5fe; }
    .chip-duration { background:#e7f5ff; color:#0b648f; border-color:#a5d8ff; }
    .chip-price  { background:#fff3cd; color:#664d03; border-color:#ffe69c; }

    .chip-completed { background:#cff4fc; color:#055160; border-color:#9eeaf9; }
    .chip-purchased { background:#e0cffc; color:#432874; border-color:#c5a3f7; }
    .chip-pending   { background:#fff3cd; color:#664d03; border-color:#ffe69c; }

    .btn-buy {
        background: #198754;
        border-color: #198754;
        color: #fff;
        font-weight: 600;
        font-size: 12px;
        padding: 5px 14px;
        border-radius: 6px;
        transition: background-color .15s, box-shadow .15s;
    }
    .btn-buy:hover { background:#146c43; border-color:#146c43; color:#fff; box-shadow:0 3px 8px rgba(25,135,84,.25); }

    /* Payment modal */
    #paymentModal .modal-content { border-radius:14px; overflow:hidden; border:none; box-shadow:0 20px 60px rgba(0,0,0,.25); }
    #paymentModal .modal-header { background:linear-gradient(135deg, #e2136e, #bb0b5c); color:#fff; border:none; }
    #paymentModal .bkash-pill {
        background:#fff; color:#e2136e;
        font-weight:700; border-radius:999px;
        padding:2px 10px; font-size:.75rem;
        display:inline-flex; align-items:center; gap:4px;
    }
    #paymentModal .step-number {
        display:inline-flex; align-items:center; justify-content:center;
        width:22px; height:22px; background:#e2136e; color:#fff;
        border-radius:50%; font-size:11px; font-weight:700;
        flex-shrink:0;
    }
    #paymentModal .step { display:flex; gap:10px; margin-bottom:8px; font-size:.88rem; }
    #paymentModal .msisdn-box {
        background:#fff3f7; border:1px dashed #e2136e;
        padding:10px 14px; border-radius:8px;
        font-weight:700; color:#e2136e;
        font-size:1.05rem; letter-spacing:.5px;
        display:flex; justify-content:space-between; align-items:center;
    }
    #paymentModal .copy-btn { font-size:11px; }
</style>

<div class="container-fluid py-3">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0 fw-bold">
                Available Exams <span class="text-primary">— {{ strtoupper(request()->segment(2)) }}</span>
            </h4>
            <small class="text-muted">Pick a module to buy and start practicing</small>
        </div>
    </div>

    <div class="row g-3">
        @forelse($exams as $exam)
            <div class="col-md-6 col-lg-4">
                <div class="exam-card h-100">
                    <div class="exam-card-header">
                        <h5>{{ $exam->name }}</h5>
                        <span class="chip chip-free" style="background:rgba(255,255,255,.15); color:#fff; border-color:rgba(255,255,255,.3);">
                            <i class="bi bi-tag-fill"></i>{{ strtoupper($exam->tag) }}
                        </span>
                    </div>

                    <div class="card-body p-3">
                        @forelse($exam->modules as $module)
                            @php
                                $ue = $userExams[$module->id] ?? null;
                                $status = $ue?->status;
                            @endphp

                            <div class="module-row">
                                <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                    <strong class="small">{{ $module->name }}</strong>
                                    <span class="chip chip-duration"><i class="bi bi-clock"></i>{{ $module->duration_minutes }}m</span>
                                </div>

                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        @if(($module->type ?? null) === 'free' || (float) $module->price_in_bdt == 0)
                                            <span class="chip chip-free"><i class="bi bi-gift-fill"></i>Free</span>
                                        @else
                                            <span class="chip chip-price">
                                                <i class="bi bi-currency-exchange"></i>
                                                ৳ {{ number_format((float) $module->price_in_bdt, 0) }}
                                            </span>
                                        @endif
                                    </div>

                                    <div>
                                        @if($status === 'completed')
                                            <span class="chip chip-completed"><i class="bi bi-check-circle-fill"></i>Completed</span>
                                        @elseif($status === 'purchased' || $status === 'free')
                                            <span class="chip chip-purchased"><i class="bi bi-bag-check-fill"></i>Unlocked</span>
                                        @elseif($status === 'payment_pending')
                                            <span class="chip chip-pending"><i class="bi bi-hourglass-split"></i>Pending</span>
                                        @elseif(($module->type ?? null) === 'free' || (float) $module->price_in_bdt == 0)
                                            <a href="{{ route('exam.start', $module->id) }}" target="_blank" class="btn btn-buy">
                                                <i class="bi bi-play-fill"></i> Start
                                            </a>
                                        @else
                                            <button type="button" class="btn btn-buy buy-btn"
                                                    data-module-id="{{ $module->id }}"
                                                    data-module-name="{{ $module->name }}"
                                                    data-exam-name="{{ $exam->name }}"
                                                    data-price="{{ (float) $module->price_in_bdt }}">
                                                <i class="bi bi-bag-plus-fill"></i> Buy
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-muted small">No modules available.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">No exams available yet.</div>
            </div>
        @endforelse
    </div>
</div>

{{-- ================= PAYMENT MODAL ================= --}}
<div class="modal fade" id="paymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title fw-bold mb-0">
                        <span class="bkash-pill"><i class="bi bi-phone-fill"></i>bKash</span>
                        &nbsp;Complete your payment
                    </h5>
                    <div class="small" style="opacity:.85;" id="paymentSubtitle">—</div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <div id="paymentAlert" class="alert alert-danger d-none mb-3"></div>

                <div class="row g-4">
                    <div class="col-md-5 text-center">
                        <div class="border rounded p-3 bg-light">
                            @php $qrPath = config('payment.bkash_qr'); @endphp
                            @if(file_exists(public_path($qrPath)))
                                <img src="{{ asset($qrPath) }}" alt="bKash QR" class="img-fluid" style="max-width:220px;">
                            @else
                                <div class="text-muted small p-4 border border-dashed rounded">
                                    <i class="bi bi-qr-code fs-1 d-block mb-2"></i>
                                    QR placeholder<br>
                                    <span class="text-muted" style="font-size:.75rem;">
                                        (Admin can upload at <code>public/{{ $qrPath }}</code>)
                                    </span>
                                </div>
                            @endif
                            <div class="mt-2 fw-semibold small">Scan with bKash app</div>
                        </div>

                        <div class="msisdn-box mt-3">
                            <div>
                                <div class="small text-muted">Payee bKash number</div>
                                <div id="payeeMsisdn">{{ config('payment.bkash_msisdn', '01962424219') }}</div>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-secondary copy-btn"
                                    onclick="navigator.clipboard.writeText(document.getElementById('payeeMsisdn').innerText)">
                                <i class="bi bi-clipboard"></i> Copy
                            </button>
                        </div>
                    </div>

                    <div class="col-md-7">
                        {{-- Pricing block: original / discount / final --}}
                        <div class="mb-3" id="pricingBlock">
                            {{-- Loading state shown while moduleInfo() resolves --}}
                            <div id="pricingLoader" class="text-muted small">
                                <i class="bi bi-arrow-repeat spin me-1"></i>Loading price…
                            </div>

                            <div id="pricingDetails" class="d-none">
                                {{-- Original price (struck through when discount applies) --}}
                                <div id="originalPriceRow" class="d-none small text-muted mb-1">
                                    Original price:
                                    <span class="text-decoration-line-through">৳ <span id="payOriginal">0.00</span></span>
                                </div>

                                {{-- Discount line --}}
                                <div id="discountRow" class="d-none small text-success fw-semibold mb-1">
                                    <i class="bi bi-gift-fill me-1"></i>
                                    Referral discount:
                                    <span>− ৳ <span id="payDiscount">0.00</span></span>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle ms-1"
                                          id="refCodeBadge"></span>
                                </div>

                                {{-- Final amount --}}
                                <div class="small text-muted fw-semibold">Amount to pay</div>
                                <div class="fs-4 fw-bold text-success">৳ <span id="payAmount">0.00</span></div>
                            </div>
                        </div>

                        <style>
                            .spin { animation: spin .8s linear infinite; display:inline-block; }
                            @keyframes spin { to { transform: rotate(360deg); } }
                        </style>

                        <div class="mb-3">
                            <div class="small fw-semibold mb-1">Payment steps:</div>
                            <div class="step"><span class="step-number">1</span>Open your bKash app</div>
                            <div class="step"><span class="step-number">2</span>Tap <b>Make Payment</b> and enter <b id="payeeMsisdnStep">{{ config('payment.bkash_msisdn', '01962424219') }}</b></div>
                            <div class="step"><span class="step-number">3</span>Enter the amount and confirm payment</div>
                            <div class="step"><span class="step-number">4</span>Copy the Transaction ID from the confirmation SMS</div>
                            <div class="step"><span class="step-number">5</span>Paste it in the box below and submit</div>
                        </div>

                        <form id="paymentForm">
                            @csrf
                            <input type="hidden" name="module_id" id="payModuleId">
                            <input type="hidden" name="payment_method" value="bkash">

                            <div class="mb-3">
                                <label class="form-label fw-semibold small">Your bKash number (optional)</label>
                                <input type="tel" name="sender_msisdn" class="form-control"
                                       placeholder="01XXXXXXXXX" maxlength="20">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold small">
                                    Transaction ID <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="transaction_id" id="payTrxId"
                                       class="form-control" placeholder="e.g. BJA1234ABCD"
                                       required minlength="4" maxlength="80" autocomplete="off">
                                <div class="form-text">The Trx ID is shown in your bKash confirmation SMS (usually 10 characters).</div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-buy" id="paySubmitBtn">
                                    <i class="bi bi-check2-circle me-1"></i>Submit for Admin Verification
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(function () {
    const paymentModal = new bootstrap.Modal(document.getElementById('paymentModal'));
    const $alert = $('#paymentAlert');

    $('.buy-btn').on('click', function () {
        const $btn  = $(this);
        const mid   = $btn.data('module-id');
        const mname = $btn.data('module-name');
        const ename = $btn.data('exam-name');

        // Reset modal state and show loader
        $alert.addClass('d-none').text('');
        $('#paymentForm')[0].reset();
        $('#payModuleId').val(mid);
        $('#paymentSubtitle').text(ename + ' — ' + mname);

        $('#pricingDetails').addClass('d-none');
        $('#pricingLoader').removeClass('d-none');
        $('#originalPriceRow').addClass('d-none');
        $('#discountRow').addClass('d-none');
        $('#refCodeBadge').text('');

        paymentModal.show();

        // Pull fresh module info (server applies referral discount if pending claim exists)
        $.ajax({
            url: '{{ url("/payment/module-info") }}/' + mid,
            type: 'GET',
            success: function (info) {
                $('#pricingLoader').addClass('d-none');
                $('#pricingDetails').removeClass('d-none');

                $('#payAmount').text(Number(info.final_amount).toFixed(2));

                if (info.has_referral && info.discount > 0) {
                    $('#payOriginal').text(Number(info.price_bdt).toFixed(2));
                    $('#payDiscount').text(Number(info.discount).toFixed(2));
                    $('#originalPriceRow').removeClass('d-none');
                    $('#discountRow').removeClass('d-none');
                    if (info.referral_code) {
                        $('#refCodeBadge').text('Code: ' + info.referral_code);
                    }
                }
            },
            error: function () {
                $('#pricingLoader').addClass('d-none');
                $('#pricingDetails').removeClass('d-none');
                $('#payAmount').text(parseFloat($btn.data('price') || 0).toFixed(2));
                $alert.text('Could not load latest price; showing default.').removeClass('d-none');
            }
        });
    });

    $('#paymentForm').on('submit', function (e) {
        e.preventDefault();
        const $btn = $('#paySubmitBtn');
        const original = $btn.html();
        $btn.prop('disabled', true).html('<i class="bi bi-arrow-repeat me-1"></i>Submitting…');
        $alert.addClass('d-none');

        $.ajax({
            url: '{{ route("payment.submit") }}',
            type: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            data: $(this).serialize(),
            success: function (res) {
                paymentModal.hide();
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Payment submitted',
                        text: res.message,
                        confirmButtonText: 'OK',
                    }).then(() => { window.location.reload(); });
                } else {
                    alert(res.message);
                    window.location.reload();
                }
            },
            error: function (xhr) {
                const msg = xhr.responseJSON?.message || 'Could not submit payment. Please try again.';
                $alert.text(msg).removeClass('d-none');
            },
            complete: function () {
                $btn.prop('disabled', false).html(original);
            }
        });
    });
});
</script>
@endsection
