<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PengaturanController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        return view('pengaturan.index', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
        ], [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email sudah digunakan akun lain.',
        ]);

        $user->update($data);
        AuditLog::catat('update', 'Memperbarui profil admin', $user);

        return redirect()->route('pengaturan')->with('success', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();
        $data = $request->validate([
            'password_lama' => 'required',
            'password_baru' => ['required', Password::min(8)->letters()->numbers()],
            'password_baru_confirmation' => 'required|same:password_baru',
        ], [
            'password_lama.required' => 'Password lama wajib diisi.',
            'password_baru.required' => 'Password baru wajib diisi.',
            'password_baru_confirmation.same' => 'Konfirmasi password tidak cocok.',
        ]);

        if (! Hash::check($data['password_lama'], $user->password)) {
            return back()->withErrors(['password_lama' => 'Password lama tidak sesuai.']);
        }

        $user->update(['password' => Hash::make($data['password_baru'])]);
        AuditLog::catat('update', 'Mengubah password admin');

        return redirect()->route('pengaturan')->with('success', 'Password berhasil diubah.');
    }
}
