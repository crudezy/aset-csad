<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoriPemakaian extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terhubung dengan model.
     *
     * @var string
     */
    protected $table = 'histori_pemakaians';

    /**
     * Atribut yang dapat diisi secara massal (mass assignable).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'aset_kode_tag', // <-- INI YANG PALING PENTING
        'pegawai_id',
        'tanggal_serah',
        'tanggal_kembali',
        'keterangan',
    ];

    /**
     * Mendefinisikan relasi "belongsTo" ke model Aset.
     * Setiap histori pemakaian dimiliki oleh satu aset.
     */
    public function aset()
    {
        // Relasi ke model Aset menggunakan foreign key 'aset_kode_tag' 
        // yang merujuk ke primary key 'kode_tag' di tabel asets.
        return $this->belongsTo(Aset::class, 'aset_kode_tag', 'kode_tag');
    }

    /**
     * Mendefinisikan relasi "belongsTo" ke model Pegawai.
     * Setiap histori pemakaian dimiliki oleh satu pegawai.
     */
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id', 'id');
    }
}
