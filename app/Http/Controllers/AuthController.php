<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function loginForm()
    {
        if (Auth::check()) {
            if (Auth::user()->isPetugas()) {
                return redirect()->route('petugas.input');
            }

            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function registerForm()
    {
        if (Auth::check()) {
            if (Auth::user()->isPetugas()) {
                return redirect()->route('petugas.input');
            }

            return redirect()->route('dashboard');
        }

        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|min:3|max:50|alpha_dash|unique:users,username',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'name.required' => 'Nama lengkap petugas wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah terdaftar.',
            'username.alpha_dash' => 'Username hanya boleh berisi huruf, angka, tanda hubung, dan garis bawah.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $username = strtolower(trim($data['username']));
        $email = $username.'@dishub-bogor.go.id';

        // Pastikan email unik jika username dipakai
        if (User::where('email', $email)->exists()) {
            $email = $username.'_'.time().'@dishub-bogor.go.id';
        }

        $user = User::create([
            'name' => trim($data['name']),
            'username' => $username,
            'email' => $email,
            'password' => Hash::make($data['password']),
            'role' => 'petugas',
            'is_active' => false,
            'status_akun' => 'menunggu_persetujuan',
        ]);

        AuditLog::catat('register', "Pendaftaran akun petugas baru: {$user->username} ({$user->name})", $user);

        return redirect()->route('login')->with(
            'success',
            'Pendaftaran akun petugas berhasil! Akun Anda sedang menunggu persetujuan Super Admin sebelum dapat digunakan.'
        );
    }

    public function login(Request $request)
    {
        $loginInput = $request->input('login') ?: $request->input('email') ?: $request->input('username');

        $request->merge(['login_identifier' => $loginInput]);

        $request->validate([
            'login_identifier' => 'required|string',
            'password' => 'required|string',
        ], [
            'login_identifier.required' => 'Email atau username wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $throttleKey = strtolower($loginInput).'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            throw ValidationException::withMessages([
                'email' => "Terlalu banyak percobaan login. Coba lagi dalam {$seconds} detik.",
            ]);
        }

        $user = User::where('email', $loginInput)
            ->orWhere('username', $loginInput)
            ->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            RateLimiter::hit($throttleKey, 300);
            throw ValidationException::withMessages([
                'email' => 'Email/Username atau password tidak sesuai.',
            ]);
        }

        Auth::login($user, $request->boolean('remember'));

        // Pemeriksaan status persetujuan & keaktifan akun
        if ($user->status_akun === 'menunggu_persetujuan') {
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => 'Akun Anda sedang Menunggu Persetujuan dari Super Admin. Silakan hubungi administrator.',
            ]);
        }

        if (! $user->is_active || $user->status_akun === 'nonaktif') {
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => 'Akun Anda dinonaktifkan. Hubungi administrator.',
            ]);
        }

        RateLimiter::clear($throttleKey);
        $request->session()->regenerate();

        $user->update(['last_login_at' => now()]);

        AuditLog::catat('login', "Pengguna {$user->name} ({$user->role_label}) berhasil login");

        if ($user->isPetugas()) {
            return redirect()->route('petugas.input');
        }

        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            AuditLog::catat('logout', "Pengguna {$user->name} logout");
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda berhasil logout.');
    }
}
