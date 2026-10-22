<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PemilikPengemudi extends Model
{
    use HasFactory;

    protected $table = 'pemilik_pengemudis';

    protected $fillable = [
        'kendaraan_id', 'peran', 'nama', 'no_sim', 'no_ktp', 'alamat', 'no_telepon',
    ];

    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class, 'kendaraan_id')->withTrashed();
    }

    public function getPeranLabelAttribute(): string
    {
        return match ($this->peran) {
            'pemilik' => 'Pemilik',
            'pengemudi' => 'Pengemudi',
            'pemilik_sekaligus_pengemudi' => 'Pemilik & Pengemudi',
            default => ! empty($this->peran) ? ucfirst((string) $this->peran) : 'Pengemudi',
        };
    }
}
