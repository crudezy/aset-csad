<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Kategori;
use App\Models\Lokasi;
use App\Models\Departemen;
use App\Models\StatusAset;

class StatusAsetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        StatusAset::create(['nama' => 'Tersedia']);
        StatusAset::create(['nama' => 'Digunakan']);
        StatusAset::create(['nama' => 'Rusak']);
        StatusAset::create(['nama' => 'Dalam Perbaikan']);
        StatusAset::create(['nama' => 'Proses Penghapusan']);
    }
}
