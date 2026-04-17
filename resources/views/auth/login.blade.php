<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>PerfectMocks — Sign In</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #0d6efd 0%, #8aa5cd 50%, #084298 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', system-ui, sans-serif;
        }

        .auth-card {
            width: 100%;
            max-width: 420px;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,.25);
            overflow: hidden;
        }

        .auth-card-header {
            background: linear-gradient(135deg, #0d6efd, #0a58ca);
            padding: 2rem;
            text-align: center;
            color: #fff;
        }

        .auth-card-header .brand {
            font-size: 1.9rem;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .auth-card-header .brand span {
            font-weight: 300;
        }

        .auth-card-header .subtitle {
            font-size: .85rem;
            opacity: .85;
            margin-top: .25rem;
        }

        .auth-card-body {
            padding: 2rem;
        }

        .form-floating label {
            color: #6c757d;
        }

        .form-control {
            border-radius: 8px;
            transition: border-color .2s, box-shadow .2s;
        }

        .form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 .2rem rgba(13,110,253,.15);
        }

        .btn-signin {
            border-radius: 8px;
            padding: .65rem 1.5rem;
            font-weight: 600;
            letter-spacing: .3px;
            transition: transform .15s ease, box-shadow .15s ease, background-color .2s;
        }

        .btn-signin:hover:not(:disabled) {
            transform: translateY(-1px);
            box-shadow: 0 4px 14px rgba(13,110,253,.4);
        }

        .btn-signin:active:not(:disabled) {
            transform: translateY(0);
        }

        .btn-signin:disabled {
            opacity: .75;
            cursor: not-allowed;
        }

        .divider {
            display: flex;
            align-items: center;
            gap: .75rem;
            color: #adb5bd;
            font-size: .8rem;
            margin: 1.25rem 0;
        }

        .divider::before, .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e9ecef;
        }

        .input-icon-group {
            position: relative;
        }

        .input-icon-group .input-icon {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #adb5bd;
            pointer-events: none;
            z-index: 5;
        }

        .input-icon-group .form-control {
            padding-right: 2.5rem;
        }

        /* OTP digit boxes */
        .otp-inputs {
            display: flex;
            gap: .75rem;
            justify-content: center;
        }

        .otp-digit {
            width: 56px;
            height: 56px;
            text-align: center;
            font-size: 1.4rem;
            font-weight: 700;
            border: 2px solid #dee2e6;
            border-radius: 10px;
            outline: none;
            transition: border-color .2s, box-shadow .2s;
        }

        .otp-digit:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 .2rem rgba(13,110,253,.15);
        }

        .otp-digit.filled {
            border-color: #0d6efd;
            background: #f0f5ff;
        }

        .spin {
            animation: spin .8s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .shake {
            animation: shake .4s ease;
        }

        @keyframes shake {
            0%,100% { transform: translateX(0); }
            20%,60%  { transform: translateX(-6px); }
            40%,80%  { transform: translateX(6px); }
        }

        .fade-up {
            animation: fadeUp .35s ease both;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(10px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .alert-inline {
            border-radius: 8px;
            font-size: .875rem;
            padding: .6rem .9rem;
        }

        a.auth-link {
            color: #0d6efd;
            text-decoration: none;
            font-weight: 500;
            transition: color .15s;
        }

        a.auth-link:hover {
            color: #0a58ca;
            text-decoration: underline;
        }

        .resend-btn {
            background: none;
            border: none;
            color: #0d6efd;
            font-weight: 500;
            padding: 0;
            cursor: pointer;
            transition: color .15s;
        }

        .resend-btn:hover { color: #0a58ca; }
        .resend-btn:disabled { color: #adb5bd; cursor: not-allowed; }
    </style>
</head>
<body>

<div class="auth-card fade-up">
    <div class="auth-card-header">
        <div class="brand">Perfect<span>Mocks</span></div>
        <div class="subtitle">Sign in to your account</div>
    </div>

    <div class="auth-card-body">

        <div id="loginAlert" class="alert alert-danger alert-inline d-none" role="alert"></div>

        <form id="loginForm" novalidate>
            @csrf
            <div class="mb-3 input-icon-group">
                <div class="form-floating">
                    <input id="loginEmail" name="email" type="email" class="form-control"
                           placeholder="Email" autocomplete="email" required />
                    <label for="loginEmail">Email address</label>
                </div>
                <i class="bi bi-envelope input-icon"></i>
            </div>

            <div class="mb-4 input-icon-group">
                <div class="form-floating">
                    <input id="loginPassword" name="password" type="password" class="form-control"
                           placeholder="Password" autocomplete="current-password" required />
                    <label for="loginPassword">Password</label>
                </div>
                <i class="bi bi-lock input-icon"></i>
            </div>

            <div class="d-grid">
                <button type="submit" id="loginBtn" class="btn btn-primary btn-signin">
                    <span id="loginBtnText">Sign In</span>
                    <i id="loginSpinner" class="bi bi-arrow-repeat spin d-none ms-1"></i>
                </button>
            </div>
        </form>

        <div class="divider">or</div>

        <div class="text-center" style="font-size:.875rem;">
            <span class="text-muted">New here?</span>
            <a href="{{ route('register') }}" class="auth-link ms-1">Create an account</a>
        </div>
    </div>
</div>

<!-- OTP Modal -->
<div class="modal fade" id="otpModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
     aria-labelledby="otpModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px;overflow:hidden;border:none;">
            <div class="modal-header" style="background:linear-gradient(135deg,#0d6efd,#0a58ca);color:#fff;border:none;">
                <div>
                    <h5 class="modal-title fw-bold mb-0" id="otpModalLabel">
                        <i class="bi bi-shield-lock me-2"></i>Verify Your Identity
                    </h5>
                    <p class="mb-0 mt-1" style="font-size:.82rem;opacity:.85;">
                        Enter the 4-digit code sent to your email
                    </p>
                </div>
            </div>
            <div class="modal-body p-4">

                <div id="otpAlert" class="alert alert-danger alert-inline d-none mb-3"></div>
                <div id="otpSuccess" class="alert alert-success alert-inline d-none mb-3">
                    <i class="bi bi-check-circle me-1"></i>
                    <span id="otpSuccessText">OTP verified! Redirecting…</span>
                </div>

                <div class="otp-inputs mb-4" id="otpInputsWrapper">
                    <input class="otp-digit" type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" />
                    <input class="otp-digit" type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" />
                    <input class="otp-digit" type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" />
                    <input class="otp-digit" type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" />
                </div>

                <div class="d-grid mb-3">
                    <button id="otpVerifyBtn" class="btn btn-primary btn-signin" disabled>
                        <span id="otpBtnText">Verify OTP</span>
                        <i id="otpSpinner" class="bi bi-arrow-repeat spin d-none ms-1"></i>
                    </button>
                </div>

                <div class="text-center" style="font-size:.84rem;color:#6c757d;">
                    Didn't receive it?
                    <button id="resendBtn" class="resend-btn ms-1" disabled>
                        Resend <span id="resendTimer"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
function csrfToken() { return $('meta[name="csrf-token"]').attr('content'); }

const otpModalEl = document.getElementById('otpModal');
const otpModal   = new bootstrap.Modal(otpModalEl);
const digits     = Array.from(document.querySelectorAll('.otp-digit'));

// ── Login form ──────────────────────────────────────────────────────────────
$('#loginForm').on('submit', function (e) {
    e.preventDefault();

    const $btn = $('#loginBtn');
    const $alert = $('#loginAlert');

    $alert.addClass('d-none').text('');
    $('#loginBtnText').text('Signing in…');
    $('#loginSpinner').removeClass('d-none');
    $btn.prop('disabled', true);

    $.ajax({
        url: '{{ route("login.submit") }}',
        type: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken() },
        data: $(this).serialize(),
        success: function (res) {
            // Refresh CSRF token (Auth::attempt regenerates the session)
            if (res.csrf_token) {
                $('meta[name="csrf-token"]').attr('content', res.csrf_token);
            }
            resetOtpModal();
            otpModal.show();
            setTimeout(() => digits[0].focus(), 350);
            startResendTimer(60);
        },
        error: function (xhr) {
            const msg = xhr.responseJSON?.message || 'Login failed. Please try again.';
            $alert.html(msg).removeClass('d-none');
            $('#loginForm').addClass('shake');
            setTimeout(() => $('#loginForm').removeClass('shake'), 400);
        },
        complete: function () {
            $('#loginBtnText').text('Sign In');
            $('#loginSpinner').addClass('d-none');
            $btn.prop('disabled', false);
        }
    });
});

// ── OTP digit input behaviour ───────────────────────────────────────────────
digits.forEach((el, i) => {
    el.addEventListener('input', () => {
        el.value = el.value.replace(/[^0-9]/g, '').slice(-1);
        el.classList.toggle('filled', el.value !== '');
        if (el.value && i < digits.length - 1) digits[i + 1].focus();
        syncVerifyBtn();
    });

    el.addEventListener('keydown', e => {
        if (e.key === 'Backspace' && !el.value && i > 0) {
            digits[i - 1].value = '';
            digits[i - 1].classList.remove('filled');
            digits[i - 1].focus();
            syncVerifyBtn();
        }
    });

    el.addEventListener('paste', e => {
        e.preventDefault();
        const text = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '');
        digits.forEach((d, j) => {
            d.value = text[j] || '';
            d.classList.toggle('filled', !!d.value);
        });
        const last = Math.min(text.length, digits.length) - 1;
        if (last >= 0) digits[last].focus();
        syncVerifyBtn();
    });
});

function syncVerifyBtn() {
    const full = digits.every(d => d.value !== '');
    $('#otpVerifyBtn').prop('disabled', !full);
}

// ── OTP verify ──────────────────────────────────────────────────────────────
$('#otpVerifyBtn').on('click', function () {
    const otp = digits.map(d => d.value).join('');

    $('#otpAlert').addClass('d-none');
    $('#otpBtnText').text('Verifying…');
    $('#otpSpinner').removeClass('d-none');
    $(this).prop('disabled', true);

    $.ajax({
        url: '{{ route("otp.verify") }}',
        type: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken() },
        data: { otp: otp, _token: csrfToken() },
        success: function (res) {
            $('#otpSuccess').removeClass('d-none');
            $('#otpSuccessText').text(res.message || 'OTP verified! Redirecting…');
            setTimeout(() => { window.location.href = '{{ route("dashboard") }}'; }, 1800);
        },
        error: function (xhr) {
            const msg = xhr.responseJSON?.message || 'Invalid or expired OTP.';
            $('#otpAlert').text(msg).removeClass('d-none');
            $('#otpInputsWrapper').addClass('shake');
            setTimeout(() => $('#otpInputsWrapper').removeClass('shake'), 400);
            digits.forEach(d => { d.value = ''; d.classList.remove('filled'); });
            digits[0].focus();
            syncVerifyBtn();
        },
        complete: function () {
            $('#otpBtnText').text('Verify OTP');
            $('#otpSpinner').addClass('d-none');
            syncVerifyBtn();
        }
    });
});

// ── Resend countdown ────────────────────────────────────────────────────────
let resendInterval;

function startResendTimer(seconds) {
    clearInterval(resendInterval);
    const $btn = $('#resendBtn');
    $btn.prop('disabled', true);
    let remaining = seconds;

    function tick() {
        $('#resendTimer').text('(' + remaining + 's)');
        if (--remaining < 0) {
            clearInterval(resendInterval);
            $('#resendTimer').text('');
            $btn.prop('disabled', false);
        }
    }
    tick();
    resendInterval = setInterval(tick, 1000);
}

$('#resendBtn').on('click', function () {
    $.ajax({
        url: '{{ route("login.submit") }}',
        type: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken() },
        data: $('#loginForm').serialize(),
        success: function () {
            resetOtpDigits();
            startResendTimer(60);
            $('#otpAlert').addClass('d-none');
        },
        error: function () { /* silent */ }
    });
});

function resetOtpDigits() {
    digits.forEach(d => { d.value = ''; d.classList.remove('filled'); });
    syncVerifyBtn();
}

function resetOtpModal() {
    resetOtpDigits();
    $('#otpAlert').addClass('d-none');
    $('#otpSuccess').addClass('d-none');
}
</script>
</body>
</html>
