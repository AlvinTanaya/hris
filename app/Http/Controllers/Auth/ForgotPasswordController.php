<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class ForgotPasswordController extends Controller
{
    use SendsPasswordResetEmails;

    public function showForgotForm()
    {
        return view('/forgot-password'); // Fixed path to use standard convention
    }

    public function sendOTP(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'Email not found in the Database!'], 404);
        }

        // Generate a 6-digit OTP
        $otp = sprintf('%06d', mt_rand(0, 999999));

        $user->update([
            'otp' => Hash::make($otp),
            'otp_expired_at' => Carbon::now()->addMinutes(2),
        ]);

        // Send OTP email
        Mail::to($user->email)->send(new \App\Mail\SendOTP($otp));

        return response()->json([
            'success' => true,
            'message' => 'OTP has been sent to your email.',
            'email' => $request->email
        ]);
    }


    public function resendOTP(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'Email not found in the Database!'], 404);
        }

        // Generate a 6-digit OTP
        $otp = sprintf('%06d', mt_rand(0, 999999));

        $user->update([
            'otp' => Hash::make($otp),
            'otp_expired_at' => Carbon::now()->addMinutes(2),
        ]);

        // Send OTP email
        Mail::to($user->email)->send(new \App\Mail\SendOTP($otp));

        return response()->json([
            'message' => 'OTP has been sent to your email.',
            'email' => $request->email
        ]);
    }



    public function showVerifyOTPForm(Request $request)
    {
        $email = $request->query('email');
        if (!$email) {
            return redirect()->route('password.forgot');
        }

        return view('/verify-otp', ['email' => $email]);
    }

    public function verifyOTP(Request $request)
    {

        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|numeric|digits:6'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !$user->otp || !Hash::check($request->otp, $user->otp)) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'OTP tidak valid.'
                ]);
            }
            return back()->withErrors(['otp' => 'OTP tidak valid.']);
        }

        if (Carbon::now()->greaterThan($user->otp_expired_at)) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'OTP Has Expired.'
                ]);
            }
            return back()->withErrors(['otp' => 'OTP Has Expired.']);
        }

        // Clear OTP after successful verification
        $user->update(['otp' => null, 'otp_expired_at' => null]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'redirect' => route('password.reset.form', ['email' => $request->email])
            ]);
        }

        return redirect()->route('password.reset.form', ['email' => $request->email])
            ->with('status', 'OTP berhasil diverifikasi. Silakan atur ulang kata sandi Anda.');
    }
}
