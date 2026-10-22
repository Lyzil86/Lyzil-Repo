<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('superadmin');
        abort_if(! auth()->user()?->isSuperadmin(), 403, 'Akses terbatas hanya untuk Super Admin.');

        $admins = User::when($request->search, fn ($q) => $q->where(function ($q) use ($request) {
            $q->where('name', 'like', "%{$request->search}%")
                ->orWhere('email', 'like', "%{$request->search}%");
        }))
            ->orderBy('name')
            ->paginate(15)->withQueryString();

        return view('admin.index', compact('admins'));
    }

    public function create()
    {
        $this->authorize('superadmin');
        abort_if(! auth()->user()?->isSuperadmin(), 403, 'Akses terbatas hanya untuk Super Admin.');

        return view('admin.create');
    }

    public function store(Request $request)
    {
        $this->authorize('superadmin');
        abort_if(! auth()->user()?->isSuperadmin(), 403, 'Akses terbatas hanya untuk Super Admin.');

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => ['required', Password::min(8)->letters()->numbers()],
            'role' => 'required|in:superadmin,admin,operator',
        ], [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
            'is_active' => true,
        ]);

        AuditLog::catat('create', "Menambah admin baru: {$user->name} ({$user->email})", $user);

        return redirect()->route('admin.index')->with('success', "Admin {$user->name} berhasil ditambahkan.");
    }

    public function edit(User $admin)
    {
        $this->authorize('superadmin');
        abort_if(! auth()->user()?->isSuperadmin(), 403, 'Akses terbatas hanya untuk Super Admin.');

        return view('admin.edit', compact('admin'));
    }

    public function update(Request $request, User $admin)
    {
        $this->authorize('superadmin');
        abort_if(! auth()->user()?->isSuperadmin(), 403, 'Akses terbatas hanya untuk Super Admin.');

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$admin->id,
            'role' => 'required|in:superadmin,admin,operator',
            'is_active' => 'boolean',
        ];

        if ($request->filled('password')) {
            $rules['password'] = ['nullable', Password::min(8)->letters()->numbers()];
        }

        $data = $request->validate($rules);

        $updateData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
            'is_active' => $request->boolean('is_active'),
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($data['password']);
        }

        $dataLama = $admin->toArray();
        $admin->update($updateData);
        AuditLog::catat('update', "Memperbarui admin: {$admin->name}", $admin, $dataLama, $admin->fresh()->toArray());

        return redirect()->route('admin.index')->with('success', "Admin {$admin->name} berhasil diperbarui.");
    }

    public function destroy(User $admin)
    {
        $this->authorize('superadmin');
        abort_if(! auth()->user()?->isSuperadmin(), 403, 'Akses terbatas hanya untuk Super Admin.');

        if ($admin->id === auth()->id()) {
            return redirect()->route('admin.index')->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        AuditLog::catat('delete', "Menghapus admin: {$admin->name} ({$admin->email})", $admin, $admin->toArray());
        $admin->delete();

        return redirect()->route('admin.index')->with('success', "Admin {$admin->name} berhasil dihapus.");
    }

    // ==========================================
    // PENGELOLAAN AKUN PETUGAS LAPANGAN
    // ==========================================

    public function petugasIndex(Request $request)
    {
        $this->authorize('superadmin');
        abort_if(! auth()->user()?->isSuperadmin(), 403, 'Akses terbatas hanya untuk Super Admin.');

        $statusFilter = $request->input('status');

        $petugasList = User::where('role', 'petugas')
            ->when($statusFilter, fn ($q) => $q->where('status_akun', $statusFilter))
            ->when($request->search, fn ($q) => $q->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('username', 'like', "%{$request->search}%");
            }))
            ->orderByRaw("CASE WHEN status_akun = 'menunggu_persetujuan' THEN 0 ELSE 1 END")
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        $countMenunggu = User::where('role', 'petugas')->where('status_akun', 'menunggu_persetujuan')->count();

        return view('admin.petugas.index', compact('petugasList', 'countMenunggu', 'statusFilter'));
    }

    public function petugasToggleStatus(Request $request, User $user)
    {
        $this->authorize('superadmin');
        abort_if(! auth()->user()?->isSuperadmin(), 403, 'Akses terbatas hanya untuk Super Admin.');

        $targetStatus = $request->input('status');
        if (! in_array($targetStatus, ['aktif', 'nonaktif', 'menunggu_persetujuan'], true)) {
            $targetStatus = $user->is_active ? 'nonaktif' : 'aktif';
        }

        $isActive = ($targetStatus === 'aktif');
        $user->update([
            'status_akun' => $targetStatus,
            'is_active' => $isActive,
        ]);

        AuditLog::catat(
            'update',
            "Super Admin mengubah status akun petugas {$user->username} menjadi: {$targetStatus}",
            $user
        );

        return back()->with('success', "Status akun petugas {$user->name} ({$user->username}) berhasil diubah menjadi: ".$user->status_akun_label.'.');
    }

    public function petugasUpdate(Request $request, User $user)
    {
        $this->authorize('superadmin');
        abort_if(! auth()->user()?->isSuperadmin(), 403, 'Akses terbatas hanya untuk Super Admin.');

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|min:3|max:50|alpha_dash|unique:users,username,'.$user->id,
            'status_akun' => 'required|in:aktif,menunggu_persetujuan,nonaktif',
        ], [
            'name.required' => 'Nama petugas wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan akun lain.',
        ]);

        $data['is_active'] = ($data['status_akun'] === 'aktif');

        $dataLama = $user->toArray();
        $user->update($data);

        AuditLog::catat('update', "Super Admin memperbarui data petugas: {$user->username}", $user, $dataLama, $user->fresh()->toArray());

        return back()->with('success', "Data akun petugas {$user->name} berhasil diperbarui.");
    }

    public function petugasResetPassword(Request $request, User $user)
    {
        $this->authorize('superadmin');
        abort_if(! auth()->user()?->isSuperadmin(), 403, 'Akses terbatas hanya untuk Super Admin.');

        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.min' => 'Kata sandi baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        AuditLog::catat('update', "Super Admin mereset password petugas: {$user->username}", $user);

        return back()->with('success', "Kata sandi untuk petugas {$user->name} ({$user->username}) berhasil diatur ulang.");
    }

    public function petugasDestroy(User $user)
    {
        $this->authorize('superadmin');
        abort_if(! auth()->user()?->isSuperadmin(), 403, 'Akses terbatas hanya untuk Super Admin.');

        AuditLog::catat('delete', "Super Admin menghapus akun petugas: {$user->username} ({$user->name})", $user, $user->toArray());
        $user->delete();

        return back()->with('success', "Akun petugas {$user->name} ({$user->username}) berhasil dihapus.");
    }
}
