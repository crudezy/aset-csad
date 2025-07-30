<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('riwayat_services', function (Blueprint $table) {
            // PERBAIKAN: Mengubah onDelete('set null') menjadi onDelete('no action') untuk SQL Server
            $table->foreignId('vendor_id')->nullable()->after('aset_kode_tag')->constrained('vendors')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('riwayat_services', function (Blueprint $table) {
            // Hapus foreign key dan kolomnya jika migration di-rollback
            $table->dropForeign(['vendor_id']);
            $table->dropColumn('vendor_id');
        });
    }
};
