<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatService extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'aset_kode_tag',
        'deskripsi_kerusakan',
        'tanggal_masuk_service',
        'perkiraan_selesai',        // Ini adalah kolom yang kita tambahkan
        'tanggal_selesai_service',
        'tindakan_perbaikan',
        'biaya_service',
        'vendor_id',               // Relasi ke vendor
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'tanggal_masuk_service'   => 'datetime',
        'perkiraan_selesai'       => 'datetime',
        'tanggal_selesai_service' => 'datetime',
    ];

    // oke
    
    /**
     * Mendefinisikan relasi ke model Aset.
     */
    public function aset()
    {
        // Relasi ini menghubungkan 'aset_kode_tag' di tabel ini
        // dengan 'kode_tag' di tabel 'asets'.
        return $this->belongsTo(Aset::class, 'aset_kode_tag', 'kode_tag');
    }
    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }
}