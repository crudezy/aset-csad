<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Kategori;
use App\Models\Lokasi;
use App\Models\Departemen;
use App\Models\StatusAset;

class LokasiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Lokasi::create(['nama' => 'Head Office']);
        Lokasi::create(['nama' => 'Sumatera']);
    }
}
