<!doctype html>
<html lang="en">
@include('layouts.header')
<body>
<div class="login-box" style="align-content: center;">
    <div class="card card-outline card-primary">
        <div class="card-header text-center">
            <b>Verify OTP</b>
        </div>

        <div class="card-body">
            <p class="login-box-msg">Please enter the OTP sent to your email.</p>
            <form id="otpForm" method="POST">
                @csrf
                <div class="mb-3">
                    {{-- <label for="otpInput" class="form-label">One-Time Password</label> --}}
                    <input type="text" name="otp" class="form-control" id="otpInput" placeholder="Enter OTP">
                </div>
                <button type="submit" class="btn btn-primary">Verify OTP</button>
            </form>
            <div id="otpResponseMessage" class="mt-3"></div>
        </div>
    </div>
</div>


<!-- AdminLTE JS -->
{{-- <script src="{{ asset('adminlte/plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('adminlte/dist/js/adminlte.min.js') }}"></script> --}}

@include('layouts.scripts')

<script>
    // Handle OTP form submission
      $('#otpForm').on('submit', function(e) {
        e.preventDefault();

        $.ajax({
          url: '{{ route("otp.verify") }}', 
          type: 'POST',        
          data: $(this).serialize(),
          // {
          //   otp: this.otp.value,
          //   '_token': $('meta[name="csrf-token"]').attr('content')
          // },
          success: function(response) {
            console.log('OTP verification successful:', response);
            document.getElementById('otpResponseMessage').innerHTML = '';
            document.getElementById('otpResponseMessage').innerHTML = response.message || 'OTP verified successfully. Redirecting...';
            // Redirect to dashboard or home page after successful OTP verification
            setTimeout(function() {
              window.location.href = '{{ route("dashboard") }}'; 
            }, 2000); 
          },
          error: function(response) {
            console.error('OTP verification failed:', response);
            // $('#otpModal').modal('hide');
            document.getElementById('otpResponseMessage').innerHTML='';
            document.getElementById('otpResponseMessage').innerHTML = 
            response.message || 'OTP verification failed. Please try again.';
          }
        });
      });
</script>
@include('layouts.footer')
</body>
</html>
