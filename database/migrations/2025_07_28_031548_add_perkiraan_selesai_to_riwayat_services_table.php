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
        // Hanya tambahkan kolom jika belum ada
        if (!Schema::hasColumn('riwayat_services', 'perkiraan_selesai')) {
            Schema::table('riwayat_services', function (Blueprint $table) {
                $table->date('perkiraan_selesai')->nullable()->after('tanggal_masuk_service');
            });
        }
    }

    public function down(): void
    {
        // Hanya hapus kolom jika ada
        if (Schema::hasColumn('riwayat_services', 'perkiraan_selesai')) {
            Schema::table('riwayat_services', function (Blueprint $table) {
                $table->dropColumn('perkiraan_selesai');
            });
        }
    }
};
