<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    /**
     * Relasi ke model Departemen.
     */
    public function departemen()
    {
        return $this->belongsTo(Departemen::class);
    }

    /**
     * Relasi ke model Lokasi.
     */
    public function lokasi()
    {
        return $this->belongsTo(Lokasi::class);
    }

    /**
     * Relasi ke Aset dimana pegawai ini menjadi PIC.
     */
    public function asets()
    {
        return $this->hasMany(Aset::class, 'pic_id');
    }

    /**
     * Relasi ke histori pemakaian.
     */
    public function historiPemakaians()
{
    return $this->hasMany(HistoriPemakaian::class);
}
}
