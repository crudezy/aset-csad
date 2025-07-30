<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pegawais', function (Blueprint $table) {
            $table->id(); // Primary Key
            $table->string('nama');
            $table->string('email')->unique()->nullable(); // Diubah menjadi bisa null
            $table->string('no_telp')->nullable(); // Kolom baru, bisa null
            
            // Foreign Key ke tabel departemen (onDelete('restrict') dihapus)
            $table->foreignId('departemen_id')->constrained('departemens')->onUpdate('cascade');
            
            // Foreign Key ke tabel lokasi (onDelete('restrict') dihapus)
            $table->foreignId('lokasi_id')->constrained('lokasis')->onUpdate('cascade');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pegawais');
    }
};
