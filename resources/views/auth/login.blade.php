<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>PerfectMocks</title>
    <!--begin::Accessibility Meta Tags-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes" />
    <meta name="color-scheme" content="light dark" />
    <meta name="theme-color" content="#007bff" media="(prefers-color-scheme: light)" />
    <meta name="theme-color" content="#1a1a1a" media="(prefers-color-scheme: dark)" />
    <!--end::Accessibility Meta Tags-->
    <!--begin::Primary Meta Tags-->
    <meta name="title" content="PerfectMocks" />
    <meta name="author" content="ColorlibHQ" />
    <meta
      name="description"
      content="PerfectMocks is a comprehensive online platform designed to provide students with high-quality mock exams and practice tests. Our mission is to help students prepare effectively for their exams by offering a wide range of resources, including realistic mock exams, detailed solutions, and performance analytics. With PerfectMocks, students can build confidence, identify areas for improvement, and achieve their academic goals with ease."
    />
    <meta
      name="keywords"
      content="perfectmocks, mock exams, practice tests, online platform, students, academic goals, exam preparation"
    />
    <!--end::Primary Meta Tags-->
    <!--begin::Accessibility Features-->
    <!-- Skip links will be dynamically added by accessibility.js -->
    <meta name="supported-color-schemes" content="light dark" />
    <link rel="preload" href="{{ asset('admin/dist/css/adminlte.css') }}" as="style" />
    <!--end::Accessibility Features-->
    <!--begin::Fonts-->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
      integrity="sha256-tXJfXfp6Ewt1ilPzLDtQnJV4hclT9XuaZUKyUvmyr+Q="
      crossorigin="anonymous"
      media="print"
      onload="this.media='all'"
    />
    <!--end::Fonts-->
    <!--begin::Third Party Plugin(OverlayScrollbars)-->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/styles/overlayscrollbars.min.css"
      crossorigin="anonymous"
    />
    <!--end::Third Party Plugin(OverlayScrollbars)-->
    <!--begin::Third Party Plugin(Bootstrap Icons)-->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
      crossorigin="anonymous"
    />
    <!--end::Third Party Plugin(Bootstrap Icons)-->
    <!--begin::Required Plugin(AdminLTE)-->
    <link rel="stylesheet" href="{{ asset('admin/dist/css/adminlte.css') }}" />
    <!--end::Required Plugin(AdminLTE)-->

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

    {{-- <meta name="csrf-token" content="{{ csrf_token() }}"> --}}

  </head>
  <!--end::Head-->
  <!--begin::Body-->
  <body class="login-page bg-body-secondary">
    <div class="login-box">
      <div class="card card-outline card-primary">
        <div class="card-header">
          <a
            href="https://perfectmocks.com"
            class="link-dark text-center link-offset-2 link-opacity-100 link-opacity-50-hover"
          >
            <h1 class="mb-0"><b>Perfect</b>Mocks</h1>
          </a>
        </div>
        <div class="card-body login-card-body">
          {{-- <p class="login-box-msg">Sign in to start your session</p> --}}
          <form action="" method="POST" id="loginForm">
            @csrf
            <div class="input-group mb-1">
              <div class="form-floating">
                <input id="loginEmail" name="email" type="email" class="form-control" value="" placeholder="Email/Mobile Number" />
                <label for="loginEmail">Email</label>
              </div>
              <div class="input-group-text"><span class="bi bi-envelope"></span></div>
            </div>
            <div class="input-group mb-1">
              <div class="form-floating">
                <input id="loginPassword" name="password" type="password" class="form-control" placeholder="" />
                <label for="loginPassword">Password</label>
              </div>
              <div class="input-group-text"><span class="bi bi-lock-fill"></span></div>
            </div>
            <!--begin::Row-->
            <div class="row">
              <div class="col-8 d-inline-flex align-items-center">
               <span id="responseMessage"></span>
              </div>
              <!-- /.col -->
              <div class="col-4">
                <div class="d-grid gap-2">
                  <button type="submit" id="loginButton" class="btn btn-primary">Sign In</button>
                </div>
              </div>
              <!-- /.col -->
            </div>
            <!--end::Row-->
          </form>
          <div class="social-auth-links text-center mb-3 d-grid gap-2">
            <p>- OR -</p>
            <a href="#" class="btn btn-primary">
              <i class="bi bi-facebook me-2"></i> Sign in using Facebook
            </a>
            <a href="#" class="btn btn-danger">
              <i class="bi bi-google me-2"></i> Sign in using Google
            </a>
          </div>
          <!-- /.social-auth-links -->
          <p class="mb-1"><a href="forgot-password.html">Forgot password?</a></p>
          <p class="mb-1">
            <a href="register.html" class="text-center"> Register</a>
          </p>
        </div>
        <!-- /.login-card-body -->
      </div>
    </div>
    <!-- /.login-box -->

   
    
    <!--end::Required Plugin(Bootstrap 5)-->
    
    <!--begin::Required Plugin(AdminLTE)-->
    <script src="{{ asset('admin/dist/js/adminlte.js') }}"></script>
    <!--end::Required Plugin(AdminLTE)-->
    <!--begin::OverlayScrollbars Configure-->
   
    <!--end::OverlayScrollbars Configure-->
    <!--end::Script-->

    <script>
    // $(document).ready(function() {

    //   $.ajaxSetup({
    //     headers: {
    //         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //     }
    // });


      //Handle form submit then show OTP modal
      $('#loginForm').on('submit', function(e) {
        e.preventDefault();

        $.ajax({
          url: '{{ route("login.submit") }}', 
          type: 'POST',        
          data: $(this).serialize(),
          // {
          //   email: this.email.value,
          //   password: this.password.value
          // },

          success: function(data, textStatus, xhr) {
            // On successful login, show OTP modal
            console.log('Login successful:', data);
            // document.getElementById('otpModal').show();
            // document.getElementById('loginResponseMessage').innerHTML = '';
            // document.getElementById('loginResponseMessage').innerHTML = response.message || 'Please enter the OTP sent to your email.';
            // $('#loginResponseMessage').innerHTML = 
            // $('#otpModal').modal('show');

            if (xhr.status === 200) {
                window.location.href = '{{ route("otp.verify.view") }}';
            }
          },
          error: function(response) {
            // Display error message
            console.error('Login failed:', response);
            document.getElementById('responseMessage').innerHTML = '';
            document.getElementById('responseMessage').innerHTML = 
            response.responseJSON.message || 'Login failed. Please try again.';
          }
        });
      });

      

    </script>
  </body>
  <!--end::Body-->
</html>
