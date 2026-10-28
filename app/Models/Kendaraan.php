<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kendaraan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'kode_data', 'nomor_kendaraan', 'jenis_kendaraan', 'trayek_id', 'nama_trayek',
        'tahun_kendaraan', 'usia_operasional', 'jenis_pelanggaran', 'warna', 'merek', 'model_kendaraan',
        'no_mesin', 'no_rangka', 'lokasi_pelanggaran', 'foto_bukti', 'latitude', 'longitude', 'tanggal_pendataan',
        'status_verifikasi', 'status_penanganan', 'catatan',
        'created_by', 'verified_by', 'verified_at',
    ];

    protected $casts = [
        'tanggal_pendataan' => 'date',
        'verified_at' => 'datetime',
        'foto_bukti' => 'array',
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    public function trayek()
    {
        return $this->belongsTo(Trayek::class, 'trayek_id');
    }

    public function pemilikPengemudis()
    {
        return $this->hasMany(PemilikPengemudi::class, 'kendaraan_id');
    }

    public function penertibs()
    {
        return $this->hasMany(Penertib::class, 'kendaraan_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    protected static function booted()
    {
        static::saving(function ($kendaraan) {
            if ($kendaraan->tahun_kendaraan) {
                $kendaraan->usia_operasional = date('Y') - (int) $kendaraan->tahun_kendaraan;
            }
        });
    }

    public function getUsiaOperasionalAttribute()
    {
        return $this->tahun_kendaraan
            ? ((int) date('Y') - (int) $this->tahun_kendaraan)
            : ($this->attributes['usia_operasional'] ?? null);
    }

    public function getIsMelanggarBatasUsiaAttribute(): bool
    {
        return (int) $this->usia_operasional >= 20;
    }

    public function getTrayekTeksAttribute(): string
    {
        if (! empty($this->nama_trayek)) {
            return $this->nama_trayek;
        }

        if ($this->trayek) {
            return 'Trayek '.$this->trayek->kode_trayek.' - '.$this->trayek->nama_trayek;
        }

        return '-';
    }

    public function getJenisPelanggaranLabelAttribute(): string
    {
        return match ($this->jenis_pelanggaran) {
            'pelanggaran_teknis' => 'Pelanggaran Teknis (Batas Usia > 20 Th / Kelaikan)',
            'pelanggaran_administrasi' => 'Pelanggaran Administrasi (Izin Trayek / KIR / KPS)',
            default => ! empty($this->jenis_pelanggaran) ? ucfirst(str_replace('_', ' ', (string) $this->jenis_pelanggaran)) : '-',
        };
    }

    /**
     * Mengembalikan daftar URL publik untuk gambar bukti laporan.
     */
    public function getFotoBuktiUrlsAttribute(): array
    {
        if (empty($this->foto_bukti) || ! is_array($this->foto_bukti)) {
            return [];
        }

        return array_values(array_filter(array_map(function ($path) {
            if (empty($path)) {
                return null;
            }
            if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
                return $path;
            }

            return asset('storage/'.ltrim($path, '/'));
        }, $this->foto_bukti)));
    }

    public function getHasFotoBuktiAttribute(): bool
    {
        return ! empty($this->foto_bukti) && is_array($this->foto_bukti) && count($this->foto_bukti) > 0;
    }

    public function getHasKoordinatAttribute(): bool
    {
        return ! is_null($this->latitude) && ! is_null($this->longitude);
    }

    public function getKoordinatTeksAttribute(): ?string
    {
        if (! $this->has_koordinat) {
            return null;
        }

        return "{$this->latitude}, {$this->longitude}";
    }

    public function getGoogleMapsUrlAttribute(): ?string
    {
        if (! $this->has_koordinat) {
            return null;
        }

        return "https://www.google.com/maps?q={$this->latitude},{$this->longitude}";
    }

    public static function generateKodeData(): string
    {
        $tahun = date('Y');
        // Cari nomor urut tertinggi termasuk data yang di-soft delete agar tidak terjadi duplikasi kode
        $last = static::withTrashed()
            ->where('kode_data', 'like', "KND-{$tahun}-%")
            ->orderBy('id', 'desc')
            ->first();

        $seq = 1;
        if ($last && preg_match('/KND-\d{4}-(\d+)/', (string) $last->kode_data, $matches)) {
            $seq = ((int) $matches[1]) + 1;
        }

        // Loop verifikasi untuk memastikan nomor kode unik di database
        while (static::withTrashed()->where('kode_data', 'KND-'.$tahun.'-'.str_pad($seq, 4, '0', STR_PAD_LEFT))->exists()) {
            $seq++;
        }

        return 'KND-'.$tahun.'-'.str_pad($seq, 4, '0', STR_PAD_LEFT);
    }
}
