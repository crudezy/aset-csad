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
    public function up(): void
    {
        Schema::create('riwayat_services', function (Blueprint $table) {
            $table->id();
            
            // Foreign key ke tabel asets (berdasarkan kode_tag)
            $table->string('aset_kode_tag');
            $table->foreign('aset_kode_tag')->references('kode_tag')->on('asets')->onUpdate('cascade')->onDelete('cascade');

            $table->text('deskripsi_kerusakan');
            $table->text('tindakan_perbaikan')->nullable();
            $table->decimal('biaya_service', 15, 2)->nullable(); // Menggunakan decimal untuk biaya
            $table->dateTime('tanggal_masuk_service');
            $table->dateTime('tanggal_selesai_service')->nullable();
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
        Schema::dropIfExists('riwayat_services');
    }
};
