<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Vendor;

class VendorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Vendor::create([
            'nama_vendor' => 'PT Sinar Jaya Abadi',
            'kontak' => '081234567890'
        ]);

        Vendor::create([
            'nama_vendor' => 'CV Mitra Komputindo',
            'kontak' => '021-555-1234'
        ]);

        Vendor::create([
            'nama_vendor' => 'Toko Elektronik Makmur',
            'kontak' => 'sales@makmur.com'
        ]);
    }
}
