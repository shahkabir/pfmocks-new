<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>PerfectMocks — Register</title>
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
            background: linear-gradient(135deg, #0d6efd, #0a58ca);
            padding: 1.75rem 2rem;
            text-align: center;
            color: #fff;
        }

        .auth-card-header .brand { font-size: 1.9rem; font-weight: 700; letter-spacing: -0.5px; }
        .auth-card-header .brand span { font-weight: 300; }
        .auth-card-header .subtitle { font-size: .85rem; opacity: .85; margin-top: .25rem; }

        .auth-card-body { padding: 2rem; }

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
        <div class="brand">Perfect<span>Mocks</span></div>
        <div class="subtitle">Create your account</div>
    </div>

    <div class="auth-card-body">

        <div id="formAlert" class="alert alert-danger alert-inline d-none" role="alert"></div>
        <div id="formSuccess" class="alert alert-success alert-inline d-none" role="alert"></div>

        <form id="registerForm" novalidate>
            @csrf

            <div class="mb-3">
                <label class="form-label fw-semibold small" for="regName">Full Name <span class="text-danger">*</span></label>
                <div class="input-icon-group">
                    <input id="regName" name="name" type="text" class="form-control"
                           placeholder="John Doe" autocomplete="name" required />
                    <i class="bi bi-person field-icon"></i>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold small" for="regEmail">Email Address <span class="text-danger">*</span></label>
                <div class="input-icon-group">
                    <input id="regEmail" name="email" type="email" class="form-control"
                           placeholder="you@example.com" autocomplete="email" required />
                    <i class="bi bi-envelope field-icon"></i>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold small" for="regMobile">Mobile Number</label>
                <div class="input-icon-group">
                    <input id="regMobile" name="mobile" type="tel" class="form-control"
                           placeholder="01XXXXXXXXX" autocomplete="tel" />
                    <i class="bi bi-phone field-icon"></i>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold small" for="regPassword">Password <span class="text-danger">*</span></label>
                <div class="input-icon-group">
                    <input id="regPassword" name="password" type="password" class="form-control"
                           placeholder="Min. 8 characters" autocomplete="new-password" required />
                    <button type="button" class="password-toggle" data-target="regPassword">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold small" for="regPasswordConfirm">Confirm Password <span class="text-danger">*</span></label>
                <div class="input-icon-group">
                    <input id="regPasswordConfirm" name="password_confirmation" type="password"
                           class="form-control" placeholder="Repeat password"
                           autocomplete="new-password" required />
                    <button type="button" class="password-toggle" data-target="regPasswordConfirm">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold small" for="regRef">Referral Code <span class="text-muted">(optional)</span></label>
                <div class="input-icon-group">
                    <input id="regRef" name="referral_code" type="text" class="form-control text-uppercase"
                           placeholder="e.g. PM-AB12CD34" maxlength="20"
                           value="{{ request()->query('ref') }}"
                           style="letter-spacing:1px;">
                    <i class="bi bi-gift field-icon"></i>
                </div>
                <div class="form-text">Have a friend's code? Enter it to claim a discount on your first paid module.</div>
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
