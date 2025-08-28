<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aset extends Model
{
    use HasFactory;

    // Karena kita menggunakan string sebagai primary key, kita perlu mendefinisikannya
    protected $primaryKey = 'kode_tag';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = []; // Izinkan mass assignment untuk semua field

        /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'tanggal_pembelian' => 'datetime',
    ];

    // Relasi ke Kategori
    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }
    

    // Relasi ke Vendor
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
    
    // Relasi ke Riwayat Service
    public function riwayatServices()
    {
        return $this->hasMany(RiwayatService::class, 'aset_kode_tag', 'kode_tag');
    }

    // Relasi ke Histori Pemakaian
    public function historiPemakaians()
    {
        return $this->hasMany(HistoriPemakaian::class, 'aset_kode_tag', 'kode_tag');
    }
    
    public function statusAset()
    {
        return $this->belongsTo(StatusAset::class, 'status_id');
    }
    public function pemegangTerakhir()
    {
        return $this->hasOne(HistoriPemakaian::class, 'aset_kode_tag', 'kode_tag')->latest('tanggal_serah');
    }
}
