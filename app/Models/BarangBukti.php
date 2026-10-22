<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangBukti extends Model
{
    use HasFactory;

    protected $table = 'barang_buktis';

    protected $fillable = [
        'penertib_id', 'jenis', 'nomor_dokumen', 'tanggal_dokumen',
        'tanggal_kadaluarsa', 'kondisi', 'status', 'tanggal_pengembalian', 'keterangan',
        'foto',
    ];

    protected $casts = [
        'tanggal_dokumen' => 'date',
        'tanggal_kadaluarsa' => 'date',
        'tanggal_pengembalian' => 'date',
    ];

    public function penertib()
    {
        return $this->belongsTo(Penertib::class, 'penertib_id')->withTrashed();
    }

    public function getFotoUrlAttribute(): ?string
    {
        if (! $this->foto) {
            return null;
        }

        return asset('storage/'.$this->foto);
    }

    public function getJenisLabelAttribute(): string
    {
        return match ($this->jenis) {
            'kir' => 'Buku Uji Berkala (KIR)',
            'kps' => 'Kartu Pengawasan (KPS)',
            'stnk' => 'STNK',
            'kendaraan' => 'Kendaraan',
            'lainnya' => 'Lainnya',
            default => ! empty($this->jenis) ? (string) $this->jenis : 'Lainnya',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'disita' => 'Disita',
            'dikembalikan' => 'Dikembalikan',
            'dimusnahkan' => 'Dimusnahkan',
            default => ! empty($this->status) ? ucfirst((string) $this->status) : 'Disita',
        };
    }
}
