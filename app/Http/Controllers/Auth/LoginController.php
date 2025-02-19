<?php  

namespace App\Http\Controllers\Auth;  

use App\Http\Controllers\Controller;  
use Illuminate\Foundation\Auth\AuthenticatesUsers;  
use Illuminate\Http\Request;  
use Illuminate\Support\Facades\Auth;  
use App\Models\User;  

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
        $this->validate($request, [  
            'email' => 'required|email',  
            'password' => 'required',  
        ]);  

        // Attempt to log the user in  
        $user = User::where('email', $request->email)->first();  

        // Check if user exists and validate status  
        if ($user) {  
            if ($user->user_status === 'Banned') {  
                return back()->withErrors(['email' => 'You have been banned.']);  
            }  

            if ($user->employee_status === 'Inactive') {  
                return back()->withErrors(['email' => 'Your account is inactive.']);  
            }  

            // If the user is valid, attempt to log them in  
            if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {  
                return redirect()->intended($this->redirectTo);  
            }  
        }  

        // If login fails, redirect back with an error  
        return back()->withInput($request->only('email'))->withErrors([  
            'email' => 'The provided credentials do not match our records.',  
        ]);  
    }  
}