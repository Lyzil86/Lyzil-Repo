<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'username', 'email', 'password', 'role', 'is_active', 'status_akun', 'last_login_at',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_login_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function isSuperadmin(): bool
    {
        return $this->role === 'superadmin';
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, ['superadmin', 'admin']);
    }

    public function isPetugas(): bool
    {
        return $this->role === 'petugas';
    }

    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            'superadmin' => 'Super Admin',
            'admin' => 'Admin',
            'operator' => 'Operator',
            'petugas' => 'Petugas Pelapor',
            default => ! empty($this->role) ? ucfirst((string) $this->role) : 'Operator',
        };
    }

    public function getStatusAkunLabelAttribute(): string
    {
        return match ($this->status_akun) {
            'aktif' => 'Aktif',
            'menunggu_persetujuan' => 'Menunggu Persetujuan',
            'nonaktif' => 'Dinonaktifkan',
            default => ! empty($this->status_akun) ? ucfirst(str_replace('_', ' ', (string) $this->status_akun)) : 'Aktif',
        };
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class, 'user_id');
    }
}
