<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends Controller
{
    public function showResetForm(Request $request)
    {
        // Validate that the email is present
        if (!$request->has('email')) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Email tidak ditemukan.']);
        }

        return view('/reset-password-form', ['email' => $request->email]);
    }
    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['errors' => ['email' => ['User not found.']]], 422);
        }

        if ($request->password !== $request->password_confirmation) {
            return response()->json(['errors' => ['password_confirmation' => ['Password confirmation does not match.']]], 422);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json(['message' => 'Password successfully updated. You will be redirected to login.'], 200);
    }

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/home';
}
