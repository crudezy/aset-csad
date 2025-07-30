<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Aset;
use App\Models\Kategori;
use App\Models\StatusAset;
use App\Models\Vendor;

class AsetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $kategoris = Kategori::all();
        $statusTersedia = StatusAset::where('nama', 'Tersedia')->first();
        $vendors = Vendor::all();

        if ($kategoris->isEmpty() || !$statusTersedia || $vendors->isEmpty()) {
            $this->command->info('Tidak dapat menjalankan AsetSeeder. Pastikan data master (Kategori, StatusAset, Vendor) sudah terisi.');
            return;
        }

        // Membuat 50 data aset dummy
        $this->command->info('Membuat 50 data aset dummy...');
        
        foreach ($kategoris as $kategori) {
            // Membuat beberapa aset untuk setiap kategori agar datanya bervariasi
            for ($i = 0; $i < 5; $i++) {
                // Logika generate kode_tag yang benar
                $prefix = $kategori->prefix;
                $lastAsset = Aset::where('kode_tag', 'LIKE', $prefix . '%')->orderBy('kode_tag', 'desc')->first();
                $nextNumber = 1;
                if ($lastAsset) {
                    $lastNumber = (int) substr($lastAsset->kode_tag, strlen($prefix));
                    $nextNumber = $lastNumber + 1;
                }
                $newKodeTag = $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

                Aset::factory()->create([
                    'kode_tag' => $newKodeTag,
                    'kategori_id' => $kategori->id,
                    'status_id' => $statusTersedia->id,
                    'vendor_id' => $vendors->random()->id,
                ]);
            }
        }
        $this->command->info('50 data aset dummy berhasil dibuat.');
    }
}
