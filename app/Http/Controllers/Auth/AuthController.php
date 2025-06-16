<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function index()
    {
        return view('pages.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        $user = \App\Models\User::where('username', $request->username)
            ->where('status_akun', 'aktif')
            ->first();

        if (!$user) {
            return back()->withErrors([
                'username' => 'Akun tidak aktif atau tidak ditemukan.',
            ])->withInput();
        }

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            // Redirect berdasarkan role
            $user = Auth::user();
            if ($user->role === 'super_admin') {
                return redirect()->intended('/dashboard');
            } elseif ($user->role === 'admin') {
                return redirect()->intended('/dashboard');
            } else {
                return redirect()->intended('/dashboard');
            }
        }

        return back()->withErrors([
            'username' => 'Username atau password salah.',
        ])->withInput();
    }

    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/login');
    }
}
