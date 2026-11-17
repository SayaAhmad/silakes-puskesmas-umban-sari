<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function form()
    {
        return view('auth.masuk');
    }

    public function masuk(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [], ['email' => 'email', 'password' => 'kata sandi']);

        $kunci = Str::transliterate(Str::lower($data['email']) . '|' . $request->ip());

        if (RateLimiter::tooManyAttempts($kunci, 5)) {
            throw ValidationException::withMessages([
                'email' => 'Terlalu banyak percobaan. Coba lagi dalam ' . RateLimiter::availableIn($kunci) . ' detik.',
            ]);
        }

        if (! Auth::attempt($data, $request->boolean('ingat'))) {
            RateLimiter::hit($kunci, 60);

            throw ValidationException::withMessages([
                'email' => 'Email atau kata sandi tidak sesuai.',
            ]);
        }

        if (! $request->user()->is_active) {
            Auth::logout();

            throw ValidationException::withMessages([
                'email' => 'Akun Anda dinonaktifkan. Hubungi administrator.',
            ]);
        }

        RateLimiter::clear($kunci);
        $request->session()->regenerate();

        $request->user()->forceFill(['last_login_at' => now()])->save();

        ActivityLog::catat('login', 'Sesi', 'Masuk ke panel pengelola');

        return redirect()->intended(route('panel.dasbor'));
    }

    public function keluar(Request $request)
    {
        ActivityLog::catat('logout', 'Sesi', 'Keluar dari panel pengelola');
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('beranda')->with('sukses', 'Anda telah keluar.');
    }
}
