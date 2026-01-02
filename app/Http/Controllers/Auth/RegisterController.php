<?php
namespace App\Http\Controllers\Auth;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Auth\Otp;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function sendOtp(Request $request)
    {
        $otp = $this->generateOtp();

        $user = User::updateOrCreate(
            [
             'name' => $request->name,
             'email' => filter_var($request->identity, FILTER_VALIDATE_EMAIL) ? $request->identity : null,
             'mobile' => is_numeric($request->identity) ? $request->identity : null
            ],
            // [
            //     'name' => $request->name,
            //     'otp' => $otp,
            //     'otp_expires_at' => Carbon::now()->addMinutes(5),
            // ]
        );

        // SEND OTP (pseudo)
        // Mail::to($user->email)->send(new OtpMail($otp));
        // SMS::send($user->mobile, $otp);

        //Store in OTP table
        Otp::create([
            'user_id'    => $user->id,
            'otp'        => $otp,
            'expires_at' => now()->addMinutes(5),
        ]);

        session(['otp_user_id' => $user->id]);
        //session(['otp_created' => $otp]);

        return redirect()->route('otp.verify.view');
    }

    public function verifyView()
    {
        return view('auth.verify-otp');
    }

    public function verifyOtp(Request $request)
    {
        $user = User::find(session('otp_user_id'));
        $otp_details = Otp::where('user_id', $user->id)->latest()->first();

        //$otp_created = session('otp_created');

        //dd($user, $otp_details, $request->otp, now()->lt($otp_details->expires_at));
        //dd(session());
        //dd($user->otp, $request->otp);
        //dd(!$user, $otp_details->otp !== $request->otp, now()->gt($otp_details->expires_at));
        //Check if user exists and OTP matches and not expired
        if (!$user || $otp_details->otp !== $request->otp || now()->gt($otp_details->expires_at)) {
            dd('match failed');
            //return back()->withErrors(['otp'=>'Invalid or expired OTP']);
        }
        // else {
        //     dd('match success');
        // }

        $user->update([
            //'otp' => null,
            //'otp_expires_at' => null,
            'is_verified' => 1,
            //'password' => bcrypt(Str::random(10))
        ]);

        auth()->login($user);
        //dd($user);
        //Depend on user role, redirect
        if($user->role == 'admin'){

            return redirect()->route('dashboard.admin');
        }else if($user->role == 'user'){

            return redirect()->route('dashboard.student');
        }else{
            //Not defined role, logout
            Auth::logout();
            return redirect()->route('login');
        }

        //return redirect()->route('dashboard');
    }

    public function loginView()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        //dd($request->all());
        $credentials = $request->only('email'); //,'password'

        $user = User::where('email', $credentials)->first();

        //dd($user);

        if($user){
            session(['otp_user_id' => $user->id]);

            $otp = $this->generateOtp();

            //Store OTP
            Otp::create([
                'user_id'    => $user->id,
                'otp'        => $otp,
                'expires_at' => now()->addMinutes(5),
            ]);

            return $this->verifyView();
            // auth()->login($user);
            // return redirect()->route('dashboard');
        }else{
            return back()->withErrors(['email'=>'You are not registered']);
        }
        // if(Auth::attempt($credentials)){
        //     return redirect()->route('dashboard');
        // }
        return back()->withErrors(['email'=>'Invalid credentials']);
    }

    public function generateOtp()
    {
        return rand(1000,9999);
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}

