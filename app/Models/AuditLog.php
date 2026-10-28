<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class AuditLog extends Model
{
    protected $table = 'audit_logs';

    public $timestamps = false;

    protected $fillable = [
        'user_id', 'user_name', 'aksi', 'model', 'model_id',
        'deskripsi', 'data_lama', 'data_baru', 'ip_address', 'user_agent',
        'created_at',
    ];

    protected $casts = [
        'data_lama' => 'array',
        'data_baru' => 'array',
        'created_at' => 'datetime',
    ];

    protected $appends = [
        'formatted_created_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Mengembalikan Carbon instance dalam zona waktu Asia/Jakarta (WIB, UTC+7).
     */
    public function getCreatedAtWibAttribute(): ?Carbon
    {
        if (! $this->created_at) {
            return null;
        }

        $carbon = $this->created_at instanceof CarbonInterface
            ? $this->created_at->copy()
            : Carbon::parse($this->created_at);

        return $carbon->timezone('Asia/Jakarta');
    }

    /**
     * Format tampilan tanggal dan waktu: DD/MM/YYYY HH:mm:ss WIB (24 jam, Asia/Jakarta).
     * Contoh: 20/10/2026 11:34:26 WIB.
     */
    public function getFormattedCreatedAtAttribute(): ?string
    {
        if (! $this->created_at) {
            return '-';
        }

        $carbon = $this->created_at instanceof CarbonInterface
            ? $this->created_at->copy()
            : Carbon::parse($this->created_at);

        return $carbon->timezone('Asia/Jakarta')->format('d/m/Y H:i:s \W\I\B');
    }

    public static function catat(string $aksi, string $deskripsi, ?Model $model = null, array $dataLama = [], array $dataBaru = []): void
    {
        $user = auth()->user();
        static::create([
            'user_id' => $user?->id,
            'user_name' => $user?->name ?? 'Sistem',
            'aksi' => $aksi,
            'model' => $model ? class_basename($model) : null,
            'model_id' => $model?->id,
            'deskripsi' => $deskripsi,
            'data_lama' => $dataLama ?: null,
            'data_baru' => $dataBaru ?: null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);
    }
}
