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
        Schema::create('histori_pemakaians', function (Blueprint $table) {
            $table->id();
            // Relasi ke tabel Aset (menggunakan kode_tag sebagai foreign key)
            $table->string('aset_kode_tag');
            $table->foreign('aset_kode_tag')->references('kode_tag')->on('asets')->onDelete('cascade');
    
            // Relasi ke tabel Pegawai
            $table->foreignId('pegawai_id')->constrained('pegawais')->onDelete('cascade');
    
            $table->date('tanggal_serah');
            $table->date('tanggal_kembali')->nullable(); // Dibuat nullable karena akan diisi saat pengembalian
            $table->text('keterangan')->nullable();
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
        Schema::dropIfExists('histori_pemakaians');
    }
};
