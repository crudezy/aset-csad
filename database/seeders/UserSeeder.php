<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Departemen; // Panggil model Departemen
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('id_ID');

        // Ambil semua ID dari tabel departemen
        $departemenIds = Departemen::all()->pluck('id')->toArray();

        // 1. Buat user admin IT spesifik
        User::create([
            'name' => 'Panji IT',
            'email' => 'panji.it@csad.com',
            'password' => Hash::make('panjiitcsad123'),
            'department_id' => Departemen::where('nama', 'IT')->first()->id,
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ]);

        // 2. Buat user admin Finance spesifik
        User::create([
            'name' => 'Yeyep IT',
            'email' => 'yeyep.it@csad.com',
            'password' => Hash::make('yeyepitcsad123'),
            'department_id' => Departemen::where('nama', 'IT')->first()->id,
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ]);
    }
}