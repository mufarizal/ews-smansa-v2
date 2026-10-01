<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors([
                'email' => 'Email/NIP/NIS atau password salah.',
            ])->onlyInput('email');
        }

        $user = Auth::user();

        if (! $user->is_active) {
            Auth::logout();

            return back()->withErrors([
                'email' => 'Akun Anda Telah Dinonaktifkan, silahkan hubungi Admin.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended($this->redirectPath($user));
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    protected function redirectPath($user): string
    {
        $activeRole = $user->defaultRole ?? $user->roles->first();

        if (! $activeRole) {
            abort(403, 'Akun tidak memiliki role yang valid.');
        }

        return route("{$activeRole->name}.dashboard");
    }
}
