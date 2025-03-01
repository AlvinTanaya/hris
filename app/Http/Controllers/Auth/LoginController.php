<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Cookie;



class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    // Override the login method  

    public function login(Request $request)
    {
        // Validate the request  
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Attempt to find the user  
        $user = User::where('email', $request->email)->first();

        // Check if user exists and validate status  
        if ($user) {
            if ($user->user_status === 'Banned') {
                return back()->withErrors(['email' => 'You have been banned.']);
            }

            if ($user->employee_status === 'Inactive') {
                return back()->withErrors(['email' => 'Your account is inactive.']);
            }
        }

        // Attempt to log the user in with "Remember Me" feature  
        $remember = $request->has('remember');

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password], $remember)) {
            $request->session()->regenerate();
            return redirect()->intended(route('home'));
        }

        // If login fails, redirect back with an error  
        return back()->withInput($request->only('email'))->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    protected function authenticated(Request $request, $user)
    {
        return redirect()->route('home'); // Arahkan ke dashboard setelah login
    }


    public function logout(Request $request)
    {
        $user = Auth::user();

        Auth::logout();

        // Hapus sesi pengguna
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Hapus Remember Me token dari database jika Remember Me tidak dicentang
        if ($user && $user instanceof \App\Models\User && !$request->has('remember')) {
            $user->forceFill(['remember_token' => null])->save();
        }

        return redirect('/login');
    }
}
