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
        Schema::create('asets', function (Blueprint $table) {
            // Primary Key kustom, bukan auto-increment
            $table->string('kode_tag')->primary(); 
            
            $table->string('serial_number')->unique()->nullable();
            $table->string('merk')->nullable();
            $table->string('type')->nullable();
            $table->text('spesifikasi')->nullable();
            $table->date('tanggal_pembelian')->nullable();
            $table->string('gambar')->nullable();
            $table->text('keterangan')->nullable();

            // Foreign Keys
            $table->foreignId('kategori_id')->constrained('kategoris')->onUpdate('cascade'); 
            $table->foreignId('status_id')->constrained('status_asets')->onUpdate('cascade');
            $table->foreignId('vendor_id')->nullable()->constrained('vendors')->onUpdate('cascade')->onDelete('set null');
            $table->unsignedBigInteger('department_id')->nullable();
            $table->foreign('department_id')->references('id')->on('departemens')->onDelete('set null');
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
        Schema::dropIfExists('asets');
    }
};
