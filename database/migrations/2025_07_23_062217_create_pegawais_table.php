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

            $table->foreignId('department_id')->constrained('departemens');
            $table->foreignId('lokasi_id')->constrained('lokasis')->onUpdate('cascade')->onDelete('cascade');
            
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
