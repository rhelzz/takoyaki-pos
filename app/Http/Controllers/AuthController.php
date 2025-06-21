<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $credentials = $request->only('email', 'password');

        // Cek apakah user aktif
        $user = User::where('email', $request->email)->first();
        
        if ($user && !$user->is_active) {
            return back()->withErrors([
                'email' => 'Akun Anda tidak aktif. Hubungi administrator.',
            ])->onlyInput('email');
        }

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            
            // Redirect berdasarkan role
            $redirectTo = match(Auth::user()->role) {
                'admin' => '/dashboard',
                'manager' => '/dashboard',
                'cashier' => '/cashier',
                default => '/dashboard'
            };
            
            return redirect()->intended($redirectTo);
        }

        return back()->withErrors([
            'email' => 'Email atau password tidak sesuai.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/login');
    }
}
