<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" href="{{ asset('adminlte/dist/css/adminlte.min.css') }}">
</head>
<body class="login-page">

<div class="login-box">
<div class="card">
    <div class="card-body">

<form method="POST" action="{{ route('register.sendOtp') }}">
@csrf

<input class="form-control mb-3" name="name" placeholder="Full Name" required>

<input class="form-control mb-3" name="identity"
       placeholder="Email or Mobile" required>

<button class="btn btn-primary btn-block">Send OTP</button>

</form>

</div>
</div>
</div>

</body>
</html>
