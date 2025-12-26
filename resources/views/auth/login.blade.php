<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/dist/css/adminlte.min.css') }}">
</head>
<body class="hold-transition login-page">

<div class="login-box">
<div class="card card-outline card-primary">
    <div class="card-header text-center"><b>Exam Portal</b></div>
    <div class="card-body">

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="input-group mb-3">
                <input type="email" name="email" class="form-control" placeholder="Email/Mobile Number" required>
                <div class="input-group-append">
                    <div class="input-group-text"><span class="fas fa-envelope"></span></div>
                </div>
            </div>

            {{-- <div class="input-group mb-3">
                <input type="text" name="otp" class="form-control" placeholder="OTP" required>
                <div class="input-group-append">
                    <div class="input-group-text"><span class="fas fa-lock"></span></div>
                </div>
            </div> --}}

            {{-- <div class="input-group mb-3">
                <input type="password" name="password" class="form-control" placeholder="Password" required>
                <div class="input-group-append">
                    <div class="input-group-text"><span class="fas fa-lock"></span></div>
                </div>
            </div> --}}

            <button type="submit" class="btn btn-primary btn-block">Login</button>
        </form>

    </div>
</div>
</div>

</body>
</html>
