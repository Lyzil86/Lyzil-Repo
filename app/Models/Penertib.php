<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Penertib extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'penertibs';

    protected $fillable = [
        'nomor_surat', 'tanggal_surat', 'kendaraan_id', 'lokasi_penertiban',
        'tanggal_penertiban', 'usia_teknis_operasional', 'petugas_penertiban',
        'status', 'dasar_hukum', 'keterangan', 'catatan_internal', 'created_by',
    ];

    protected $casts = [
        'tanggal_surat' => 'date',
        'tanggal_penertiban' => 'date',
    ];

    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class, 'kendaraan_id')->withTrashed();
    }

    public function barangBuktis()
    {
        return $this->hasMany(BarangBukti::class, 'penertib_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'Draft',
            'aktif' => 'Aktif (Disita)',
            'selesai' => 'Selesai',
            'dibatalkan' => 'Dibatalkan',
            default => ! empty($this->status) ? ucfirst((string) $this->status) : 'Draft',
        };
    }
}
