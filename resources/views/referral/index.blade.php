@extends('layouts.app')

@section('title', 'Refer a Friend')

@section('content')
@php
    $shareText = "Join me on PerfectMocks! Use my code {$code} to get a discount on your first paid module.";
    $waLink    = 'https://wa.me/?text=' . rawurlencode($shareText . ' ' . $link);
    $fbLink    = 'https://www.facebook.com/sharer/sharer.php?u=' . urlencode($link)
                . '&quote=' . urlencode($shareText);
    $tgLink    = 'https://t.me/share/url?url=' . urlencode($link)
                . '&text=' . urlencode($shareText);
    $tweet     = 'https://twitter.com/intent/tweet?url=' . urlencode($link)
                . '&text=' . urlencode($shareText);
@endphp

<style>
    .ref-hero {
        background: linear-gradient(135deg, #0d6efd, #0a58ca);
        color: #fff;
        border-radius: 14px;
        padding: 28px 32px;
        box-shadow: 0 8px 24px rgba(13,110,253,.18);
    }
    .code-pill {
        background: rgba(255,255,255,.18);
        border: 1px dashed rgba(255,255,255,.7);
        border-radius: 12px;
        padding: 14px 22px;
        font-family: 'Courier New', monospace;
        font-size: 1.7rem;
        font-weight: 800;
        letter-spacing: 3px;
        display: inline-flex;
        align-items: center;
        gap: 12px;
    }
    .copy-btn {
        background: rgba(255,255,255,.92);
        border: none;
        color: #0a58ca;
        font-weight: 600;
        font-size: .8rem;
        padding: 6px 12px;
        border-radius: 8px;
        transition: background-color .15s;
    }
    .copy-btn:hover { background:#fff; }

    .share-card {
        background: #fff;
        border: 1px solid #e9ecef;
        border-radius: 12px;
        padding: 18px;
        height: 100%;
        transition: transform .15s, box-shadow .15s;
    }
    .share-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0,0,0,.06);
    }

    .share-icon-btn {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        color: #fff;
        text-decoration: none;
        transition: transform .12s, box-shadow .12s;
    }
    .share-icon-btn:hover { transform: scale(1.08); color:#fff; box-shadow:0 4px 14px rgba(0,0,0,.18); }
    .share-icon-btn.whatsapp { background:#25d366; }
    .share-icon-btn.facebook { background:#1877f2; }
    .share-icon-btn.telegram { background:#26a5e4; }
    .share-icon-btn.twitter  { background:#000000; }
    .share-icon-btn.copy     { background:#6c757d; }
    .share-icon-btn.email    { background:#ea4335; }

    .info-stat {
        text-align: center;
        padding: 12px 8px;
    }
    .info-stat .num {
        font-size: 1.6rem; font-weight: 800; color: #0d6efd; line-height: 1;
    }
    .info-stat .lbl {
        font-size: 11px; text-transform: uppercase; letter-spacing: .5px;
        color: #6c757d; margin-top: 4px; font-weight: 600;
    }

    .toast-container { z-index: 1090; }
</style>

<div class="container-fluid py-3">
    <div class="d-flex align-items-center mb-3">
        <h4 class="mb-0 fw-bold"><i class="bi bi-gift-fill text-primary me-2"></i>Refer a Friend</h4>
    </div>

    {{-- Hero / share area --}}
    <div class="ref-hero mb-4">
        <div class="row align-items-center g-3">
            <div class="col-md-7">
                <h5 class="fw-bold mb-2">Share PerfectMocks &amp; help your friends save</h5>
                <p class="mb-3" style="opacity:.92;">
                    They get a discount on their first paid module. You build the community we all benefit from.
                </p>
                <div class="d-inline-block">
                    <div class="code-pill">
                        <span id="refCode">{{ $code }}</span>
                        <button class="copy-btn" data-copy="{{ $code }}" data-toast="Code copied!">
                            <i class="bi bi-clipboard"></i> Copy
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <div class="bg-white text-dark rounded-3 p-3 shadow-sm">
                    <div class="small fw-semibold text-muted mb-1">Your shareable link</div>
                    <div class="d-flex gap-2 align-items-center">
                        <input type="text" class="form-control form-control-sm" id="refLink"
                               value="{{ $link }}" readonly>
                        <button class="btn btn-sm btn-primary" data-copy="{{ $link }}" data-toast="Link copied!">
                            <i class="bi bi-clipboard"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Share via channels --}}
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="share-card">
                <h6 class="fw-bold mb-3"><i class="bi bi-share-fill text-primary me-1"></i>Share to</h6>
                <div class="d-flex flex-wrap gap-3 align-items-center">
                    <a href="{{ $waLink }}" target="_blank" rel="noopener" class="share-icon-btn whatsapp"
                       title="Share on WhatsApp">
                        <i class="bi bi-whatsapp"></i>
                    </a>
                    <a href="{{ $fbLink }}" target="_blank" rel="noopener" class="share-icon-btn facebook"
                       title="Share on Facebook">
                        <i class="bi bi-facebook"></i>
                    </a>
                    <a href="{{ $tgLink }}" target="_blank" rel="noopener" class="share-icon-btn telegram"
                       title="Share on Telegram">
                        <i class="bi bi-telegram"></i>
                    </a>
                    <a href="{{ $tweet }}" target="_blank" rel="noopener" class="share-icon-btn twitter"
                       title="Share on X (Twitter)">
                        <i class="bi bi-twitter-x"></i>
                    </a>
                    <button type="button" class="share-icon-btn copy"
                            data-copy="{{ $shareText . ' ' . $link }}" data-toast="Message copied!"
                            title="Copy message">
                        <i class="bi bi-clipboard"></i>
                    </button>
                    <button type="button" class="share-icon-btn email"
                            data-bs-toggle="modal" data-bs-target="#emailShareModal"
                            title="Send via email">
                        <i class="bi bi-envelope-fill"></i>
                    </button>
                </div>
                <div class="small text-muted mt-3">
                    Tip: WhatsApp / Facebook open the platform's share dialog with the link prefilled.
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="share-card h-100">
                <h6 class="fw-bold mb-3"><i class="bi bi-info-circle me-1 text-primary"></i>How it works</h6>
                <ol class="mb-0 ps-3" style="font-size:.92rem;">
                    <li class="mb-1">Share your code or link with a friend.</li>
                    <li class="mb-1">They sign up using the code or referral link.</li>
                    <li class="mb-1">When they make their <strong>first paid purchase</strong>, the discount is applied automatically.</li>
                    <li>You may also earn a reward — see the active program for details.</li>
                </ol>
            </div>
        </div>
    </div>
</div>

{{-- ================= EMAIL SHARE MODAL ================= --}}
<div class="modal fade" id="emailShareModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="emailShareForm" class="modal-content">
            @csrf
            <div class="modal-header" style="background:linear-gradient(135deg,#0d6efd,#0a58ca);color:#fff;border:none;">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-envelope-fill me-1"></i>Send invite by email
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="emailAlert" class="alert alert-danger d-none mb-3"></div>
                <div id="emailSuccess" class="alert alert-success d-none mb-3"></div>

                <div class="mb-3">
                    <label class="form-label fw-semibold small">Recipient email <span class="text-danger">*</span></label>
                    <input type="email" name="recipient_email" class="form-control"
                           placeholder="friend@example.com" required maxlength="120">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Personal message (optional)</label>
                    <textarea name="personal_message" class="form-control" rows="3"
                              placeholder="Hey! Thought you'd like this site I've been using…"
                              maxlength="500"></textarea>
                    <div class="form-text">Up to 500 characters.</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary" id="emailSubmitBtn">
                    <i class="bi bi-send-fill me-1"></i>Send Invite
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Toast container --}}
<div class="toast-container position-fixed bottom-0 end-0 p-3" id="toastBox"></div>

<script>
$(function () {
    // ── Copy buttons ───────────────────────────────────────────────────
    $(document).on('click', '[data-copy]', function () {
        const text = $(this).data('copy');
        const msg  = $(this).data('toast') || 'Copied!';
        navigator.clipboard.writeText(text).then(() => showToast(msg, 'success'));
    });

    function showToast(message, type = 'success') {
        const bg = type === 'error' ? 'bg-danger' : 'bg-success';
        const $t = $(`
            <div class="toast align-items-center text-white ${bg} border-0 mb-2" role="alert">
                <div class="d-flex">
                    <div class="toast-body"><i class="bi bi-check-circle-fill me-1"></i>${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto"
                            data-bs-dismiss="toast"></button>
                </div>
            </div>`);
        $('#toastBox').append($t);
        new bootstrap.Toast($t[0], { delay: 2200 }).show();
    }

    // ── Email share ────────────────────────────────────────────────────
    $('#emailShareForm').on('submit', function (e) {
        e.preventDefault();

        const $btn  = $('#emailSubmitBtn');
        const orig  = $btn.html();
        const $alert= $('#emailAlert');
        const $ok   = $('#emailSuccess');

        $alert.addClass('d-none').text('');
        $ok.addClass('d-none').text('');
        $btn.prop('disabled', true).html('<i class="bi bi-arrow-repeat me-1"></i>Sending…');

        $.ajax({
            url: '{{ route("referral.send-email") }}',
            type: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            data: $(this).serialize(),
            success: function (res) {
                $ok.text(res.message).removeClass('d-none');
                $('#emailShareForm input[name=recipient_email]').val('');
                $('#emailShareForm textarea[name=personal_message]').val('');
                setTimeout(() => bootstrap.Modal.getInstance(document.getElementById('emailShareModal'))?.hide(), 1300);
            },
            error: function (xhr) {
                const msg = xhr.responseJSON?.message
                          || xhr.responseJSON?.errors?.recipient_email?.[0]
                          || 'Could not send email. Please try again.';
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
