<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>PerfectMocks — Register</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}" />
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
            padding: 1.5rem 0;
        }

        .auth-card {
            width: 100%;
            max-width: 460px;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,.25);
            overflow: hidden;
        }

        .auth-card-header {
            background: #fff;
            padding: 1rem;
            text-align: center;
            border-bottom: 4px solid #0d6efd;
        }

        .auth-card-header img {
            display: block;
            width: 100%;
            height: auto;
            max-height: 160px;
            object-fit: contain;
        }

        .auth-card-body {
            padding: 2rem;
            background: #eaf2ff;
        }

        .form-control {
            border-radius: 8px;
            transition: border-color .2s, box-shadow .2s;
        }

        .form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 .2rem rgba(13,110,253,.15);
        }

        .btn-submit {
            border-radius: 8px;
            padding: .65rem 1.5rem;
            font-weight: 600;
            letter-spacing: .3px;
            transition: transform .15s ease, box-shadow .15s ease, background-color .2s;
        }

        .btn-submit:hover:not(:disabled) {
            transform: translateY(-1px);
            box-shadow: 0 4px 14px rgba(13,110,253,.4);
        }

        .btn-submit:active:not(:disabled) { transform: translateY(0); }
        .btn-submit:disabled { opacity: .75; cursor: not-allowed; }

        .spin { animation: spin .8s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }

        .fade-up { animation: fadeUp .35s ease both; }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(10px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .alert-inline { border-radius: 8px; font-size: .875rem; padding: .6rem .9rem; }

        a.auth-link {
            color: #0d6efd;
            text-decoration: none;
            font-weight: 500;
            transition: color .15s;
        }
        a.auth-link:hover { color: #0a58ca; text-decoration: underline; }

        .divider {
            display: flex; align-items: center; gap: .75rem;
            color: #adb5bd; font-size: .8rem; margin: 1.25rem 0;
        }
        .divider::before, .divider::after { content: ''; flex: 1; height: 1px; background: #e9ecef; }

        .input-icon-group { position: relative; }
        .input-icon-group .form-control { padding-right: 2.5rem; }
        .input-icon-group .field-icon {
            position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
            color: #adb5bd; pointer-events: none;
        }

        .password-toggle {
            position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
            background: none; border: none; color: #adb5bd;
            cursor: pointer; padding: 0; z-index: 5; transition: color .15s;
        }
        .password-toggle:hover { color: #6c757d; }
    </style>
</head>
<body>

<div class="auth-card fade-up">
    <div class="auth-card-header">
        <img src="{{ asset('logo.png') }}" alt="PerfectMocks">
    </div>

    <div class="auth-card-body">

        <div id="formAlert" class="alert alert-danger alert-inline d-none" role="alert"></div>
        <div id="formSuccess" class="alert alert-success alert-inline d-none" role="alert"></div>

        <div class="d-grid mb-3">
            <a href="{{ route('auth.google.redirect') }}" class="btn btn-outline-secondary btn-submit d-flex align-items-center justify-content-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 48 48">
                    <path fill="#FFC107" d="M43.6 20.5H42V20H24v8h11.3C33.7 32.4 29.3 35.5 24 35.5c-6.4 0-11.5-5.1-11.5-11.5S17.6 12.5 24 12.5c2.9 0 5.6 1.1 7.6 2.9l5.7-5.7C33.9 6.5 29.2 4.5 24 4.5 13.2 4.5 4.5 13.2 4.5 24S13.2 43.5 24 43.5c10.9 0 19.5-8 19.5-19.5 0-1.3-.1-2.3-.4-3.5z"/>
                    <path fill="#FF3D00" d="M6.3 14.7l6.6 4.8C14.6 16 19 12.5 24 12.5c2.9 0 5.6 1.1 7.6 2.9l5.7-5.7C33.9 6.5 29.2 4.5 24 4.5 16.3 4.5 9.7 8.9 6.3 14.7z"/>
                    <path fill="#4CAF50" d="M24 43.5c5.1 0 9.7-1.9 13.2-5.1l-6.1-5c-1.9 1.4-4.4 2.3-7.1 2.3-5.3 0-9.7-3.1-11.3-7.5l-6.5 5C9.5 39 16.2 43.5 24 43.5z"/>
                    <path fill="#1976D2" d="M43.6 20.5H42V20H24v8h11.3c-.8 2.2-2.3 4.1-4.2 5.4l6.1 5c4.3-3.9 7-9.7 7-15.9 0-1.3-.1-2.3-.4-3.5z"/>
                </svg>
                <span>Sign up with Google</span>
            </a>
        </div>

        <div class="divider">or sign up with email</div>

        <form id="registerForm" novalidate>
            @csrf

            <div class="mb-3">
                <label class="form-label fw-semibold small" for="regName">Full Name <span class="text-danger">*</span></label>
                <div class="input-icon-group">
                    <input id="regName" name="name" type="text" class="form-control"
                           autocomplete="name" required />
                    <i class="bi bi-person field-icon"></i>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold small" for="regEmail">Email Address <span class="text-danger">*</span></label>
                <div class="input-icon-group">
                    <input id="regEmail" name="email" type="email" class="form-control"
                           autocomplete="email" required />
                    <i class="bi bi-envelope field-icon"></i>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold small" for="regMobile">Mobile Number <span class="text-danger">*</span></label>
                <div class="input-icon-group">
                    <input id="regMobile" name="mobile" type="tel" class="form-control"
                           autocomplete="tel" required />
                    <i class="bi bi-phone field-icon"></i>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold small" for="regPassword">Password <span class="text-danger">*</span></label>
                <div class="input-icon-group">
                    <input id="regPassword" name="password" type="password" class="form-control"
                           autocomplete="new-password" required />
                    <button type="button" class="password-toggle" data-target="regPassword">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold small" for="regPasswordConfirm">Confirm Password <span class="text-danger">*</span></label>
                <div class="input-icon-group">
                    <input id="regPasswordConfirm" name="password_confirmation" type="password"
                           class="form-control"
                           autocomplete="new-password" required />
                    <button type="button" class="password-toggle" data-target="regPasswordConfirm">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold small" for="regRef">Referral Code <span class="text-muted">(optional)</span></label>
                <div class="input-icon-group">
                    <input id="regRef" name="referral_code" type="text" class="form-control text-uppercase"
                           maxlength="20"
                           value="{{ request()->query('ref') }}"
                           style="letter-spacing:1px;">
                    <i class="bi bi-gift field-icon"></i>
                </div>
                <div class="form-text">Have a friend's code? Enter it to claim a discount on your first paid module.</div>
            </div>

            <div class="form-check mb-4">
                <input class="form-check-input" type="checkbox" name="sms_terms_agreed" id="smsTermsAgreed" value="1" required>
                <label class="form-check-label small" for="smsTermsAgreed">
                    I agree to receive SMS with all terms and conditions of services from PerfectMocks.com
                    <span class="text-danger">*</span>
                </label>
            </div>

            <div class="d-grid">
                <button type="submit" id="regBtn" class="btn btn-primary btn-submit">
                    <span id="regBtnText">Create Account</span>
                    <i id="regSpinner" class="bi bi-arrow-repeat spin d-none ms-1"></i>
                </button>
            </div>
        </form>

        <div class="divider">or</div>

        <div class="text-center" style="font-size:.875rem;">
            <span class="text-muted">Already have an account?</span>
            <a href="{{ route('login') }}" class="auth-link ms-1">Sign in</a>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
$.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

document.querySelectorAll('.password-toggle').forEach(btn => {
    btn.addEventListener('click', () => {
        const input = document.getElementById(btn.dataset.target);
        const icon  = btn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('bi-eye', 'bi-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('bi-eye-slash', 'bi-eye');
        }
    });
});

$('#registerForm').on('submit', function (e) {
    e.preventDefault();

    $('#formAlert').addClass('d-none');
    $('#formSuccess').addClass('d-none');
    $('#regBtnText').text('Creating account…');
    $('#regSpinner').removeClass('d-none');
    $('#regBtn').prop('disabled', true);

    $.ajax({
        url: '{{ route("register.submit") }}',
        type: 'POST',
        data: $(this).serialize(),
        success: function (res) {
            $('#formSuccess').text(res.message || 'Account created! Redirecting to sign in…').removeClass('d-none');
            setTimeout(() => { window.location.href = '{{ route("login") }}'; }, 2000);
        },
        error: function (xhr) {
            let msg = xhr.responseJSON?.message || 'Registration failed.';
            if (xhr.responseJSON?.errors) {
                msg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
            }
            $('#formAlert').html(msg).removeClass('d-none');
        },
        complete: function () {
            $('#regBtnText').text('Create Account');
            $('#regSpinner').addClass('d-none');
            $('#regBtn').prop('disabled', false);
        }
    });
});
</script>
</body>
</html>
