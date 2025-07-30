<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Kategori;
use App\Models\Lokasi;
use App\Models\Departemen;
use App\Models\StatusAset;

class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Menggunakan metode create dari model untuk otomatis mengisi timestamps
        Kategori::create(['nama' => 'Laptop', 'prefix' => 'CSADLP']);
        Kategori::create(['nama' => 'Printer', 'prefix' => 'CSADPT']);
        Kategori::create(['nama' => 'Projector', 'prefix' => 'CSADPJ']);
        Kategori::create(['nama' => 'Router', 'prefix' => 'CSADRT']);
        Kategori::create(['nama' => 'Switch', 'prefix' => 'CSADSW']);
        Kategori::create(['nama' => 'PC Desktop', 'prefix' => 'CSADPC']);
        Kategori::create(['nama' => 'Access Point', 'prefix' => 'CSADAC']);
        Kategori::create(['nama' => 'Monitor', 'prefix' => 'CSADMN']);
        Kategori::create(['nama' => 'Finger Print', 'prefix' => 'CSADFP']);
    }
}
