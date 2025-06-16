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
        // Terlebih dahulu hapus foreign key constraint
        Schema::table('tbl_surat_keluar', function (Blueprint $table) {
            $table->dropForeign('tbl_surat_keluar_tujuan_foreign');
        });
        
        // Kemudian baru kita hapus kolom yang tidak diperlukan
        Schema::table('tbl_surat_keluar', function (Blueprint $table) {
            $table->dropColumn([
                'keterangan_pengirim',
                'keterangan_sekretaris',
                'keterangan_dirut',
                'tujuan'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_surat_keluar', function (Blueprint $table) {
            // Kembalikan kolom-kolom yang dihapus
            $table->text('keterangan_pengirim')->nullable();
            $table->text('keterangan_sekretaris')->nullable();
            $table->text('keterangan_dirut')->nullable();
            $table->foreignId('tujuan')->nullable();
            
            // Kemudian kembalikan juga foreign key constraint
            $table->foreign('tujuan')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
