<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Trayek extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'kode_trayek', 'nama_trayek', 'asal', 'tujuan', 'deskripsi', 'status',
    ];

    public function kendaraans()
    {
        return $this->hasMany(Kendaraan::class, 'trayek_id');
    }
}
