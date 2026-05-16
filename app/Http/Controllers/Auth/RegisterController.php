<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Auth\Otp;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Log;

class RegisterController extends Controller
{

    public function loginView()
    {
        return view('auth.login');
    }

    public function validateLogin(Request $request)
    {
        //dd($request->all());
        $credentials = $request->only('email', 'password');

        //$user = User::where('email', $credentials['email'])->first();

        // $user = User::where('email', $request->email)->first();

        // if ($user && md5($request->password) === $user->password) {

        //     // ✅ Update to bcrypt
        //     $user->password = Hash::make($request->password);
        //     $user->save();
        // }

        //dd($user);
        // dd(Auth::attempt($credentials));

        //Bypass OTP for admin, 1 student for testing
        // if (Auth::attempt($credentials)) {
        //     $user = Auth::user();

        //     if ($user->role === 'admin' || $user->email === 'shari1909@gmail.com') {
        //         auth()->login($user);
                
        //         if ($user->role === 'admin') {
                    
        //             return redirect()->route('dashboard.student');
        //         } else {
        //             return redirect()->route('dashboard.student');
        //         }
                
        //         //return response()->json(['message' => 'Login successful. Redirecting...'], 200);
        //     }
        // }

        // dd(Auth::attempt($credentials));

        if(Auth::attempt($credentials)){

            $user = Auth::user();
            session(['otp_user_id' => $user->id]);

            // dd(session()->all());

            $otp = $this->generateOtp();

            //Store OTP
            Otp::create([
                'user_id'    => $user->id,
                'otp'        => $otp,
                'expires_at' => now()->addMinutes(5),
            ]);

            // SEND MAIL/SMS
            // Mail::to($user->email)->send(new OtpMail($otp));
            // SMS::send($user->mobile, $otp);  

            if(env('APP_ENV') === 'Production'){

                Log::info("OTP for user {$user->email}: {$otp}");
            
                $mailData = [
                    'otp' => $otp,
                    'name' => $user->name
            ];

                $mailBody = "Hello {$mailData['name']},\n\nYour OTP for login is: {$mailData['otp']}\n\n
                This OTP is valid for 5 minutes.\n\n
                If you did not request this, please ignore this email.\n\nBest regards,\nPerfectMocks Team";

                try {
                    Mail::raw($mailBody, function ($message) use ($user) {
                        $message->to($user->email)
                                ->subject('PerfectMocks - Your OTP for Login');
                    });
                } catch (\Exception $e) {
                    // Handle email sending error
                    //Log::error('Failed to send OTP email: ' . $e->getMessage());
                    dd($e->getMessage());
                }
            }


            return response()->json([
                'message'    => 'OTP sent to your registered email/phone. Please provide OTP within 5 minutes.',
                'csrf_token' => csrf_token(),
            ], 200);
            //return redirect()->route('otp.verify.view')->with('message', 'OTP sent to your registered email/phone. Please provide OTP within 5 minutes.');

            // return $this->verifyView();
            // auth()->login($user);
            // return redirect()->route('dashboard');
        }else{
            $message = 'You are not a member. <a target="_blank" href="' . route('signup') . '">Sign Up Here</a>.';
            return response()->json(['message'=>$message], 422);
        }
        // if(Auth::attempt($credentials)){
        //     return redirect()->route('dashboard');
        // }
        return response()->json(['message'=>'Invalid credentials'], 422);
    }


    // public function sendOTP(Request $request)
    // {
    //     $otp = $this->generateOtp();

    //     $user = User::updateOrCreate(
    //         [
    //          'name' => $request->name,
    //          'email' => filter_var($request->identity, FILTER_VALIDATE_EMAIL) ? $request->identity : null,
    //          'mobile' => is_numeric($request->identity) ? $request->identity : null
    //         ],
    //         // [
    //         //     'name' => $request->name,
    //         //     'otp' => $otp,
    //         //     'otp_expires_at' => Carbon::now()->addMinutes(5),
    //         // ]
    //     );

    //     // SEND OTP (pseudo)
    //     // Mail::to($user->email)->send(new OtpMail($otp));
    //     // SMS::send($user->mobile, $otp);

    //     //Store in OTP table
    //     Otp::create([
    //         'user_id'    => $user->id,
    //         'otp'        => $otp,
    //         'expires_at' => now()->addMinutes(5),
    //     ]);

    //     session(['otp_user_id' => $user->id]);
    //     //session(['otp_created' => $otp]);

    //     return redirect()->route('otp.verify.view');
    // }

    public function verifyView()
    {
        return view('auth.verify-otp');
    }

    public function verifyOTP(Request $request)
    {
    //     dd(
    //     session()->token(),          // session token
    //     $request->header('X-CSRF-TOKEN'),
    //     $request->_token
    // );

        $user = User::find(session('otp_user_id'));
        $otp_details = Otp::where('user_id', $user->id)->latest()->first();
        $providedOTP = $request->otp;

        // dd($user, $otp_details, $providedOTP, now()->lt($otp_details->expires_at));
        //$otp_created = session('otp_created');

        //dd($user, $otp_details, $request->otp, now()->lt($otp_details->expires_at));
        //dd(session());
        //dd($user->otp, $request->otp);
        //dd(!$user, $otp_details->otp !== $request->otp, now()->gt($otp_details->expires_at));
        //Check if user exists and OTP matches and not expired
        if (!$user || $otp_details->otp !== $providedOTP || now()->gt($otp_details->expires_at)) {
            //dd('match failed');
            //return back()->withErrors(['otp'=>'Invalid or expired OTP']);
            return response()->json(['message'=>'Invalid or expired OTP.'], 422);
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
        // dd($user);

        //OTP matches, log the user in
        return response()->json(['message'=>'OTP verified successfully. You will be logged in shortly.'], 200);
        
    }

    public function dashboard()
    {
        $user = auth()->user();
        $userSession = User::find(session('otp_user_id'));

        //Depend on user role, redirect
        if($userSession){
            if($user->role == 'admin'){

                // return redirect()->route('dashboard.admin');
                return redirect()->route('dashboard.student');

            }else if($user->role == 'user'){

                return redirect()->route('dashboard.student');
            }else if($user->role == 'evaluator'){

                return redirect()->route('dashboard.student');

            }else{
                //Not defined role, logout
                Auth::logout();
                return redirect()->route('login');
            }
        }else{
            //No session, logout
            Auth::logout();
            return redirect()->route('login');
        }
    }

    

    public function registerView()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|max:255|unique:users,email',
            'mobile'        => 'nullable|string|max:20',
            'password'      => 'required|string|min:8|confirmed',
            'referral_code' => 'nullable|string|max:20',
        ]);

        $newUser = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'mobile'   => $request->mobile,
            'password' => Hash::make($request->password),
            'role'     => 'user',
        ]);

        // Save a *pending* referral claim if the user signed up with a code
        // (no discount applied yet — that happens at first paid purchase).
        $referralMessage = null;
        if (!empty($request->referral_code)) {
            $invitation = app(\App\Services\ReferralService::class)
                ->createPendingInvitation(
                    $newUser->id,
                    strtoupper(trim($request->referral_code)),
                    'register-form'
                );
            $referralMessage = $invitation
                ? ' Your referral code has been applied — discount unlocks on your first paid module.'
                : ' (Note: the referral code you entered was invalid or could not be applied.)';
        }

        return response()->json([
            'message' => 'Account created successfully! Please sign in.' . ($referralMessage ?? ''),
        ], 201);
    }

    public function generateOtp()
    {
        return rand(1000,9999);
    }

    public function logout()
    {
        Auth::logout();
        session()->invalidate();
        return redirect()->route('login');
    }
}

