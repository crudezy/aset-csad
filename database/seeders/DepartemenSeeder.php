<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Kategori;
use App\Models\Lokasi;
use App\Models\Departemen;
use App\Models\StatusAset;

class DepartemenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Departemen::create(['nama' => 'IT']);
        Departemen::create(['nama' => 'Finance & Accounting']);
        Departemen::create(['nama' => 'Human Resources']);
        Departemen::create(['nama' => 'Marketing']);
        Departemen::create(['nama' => 'Operasional']);
    }
}
