<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoriPemakaian extends Model
{
    use HasFactory;

    protected $fillable = [
        'aset_kode_tag',
        'pegawai_id',
        'tanggal_serah',
        'tanggal_kembali',
        'keterangan',
    ];

    public function aset()
    {
        return $this->belongsTo(Aset::class, 'aset_kode_tag', 'kode_tag');
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }
}