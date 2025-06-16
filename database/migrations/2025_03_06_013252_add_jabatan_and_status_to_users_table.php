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
        Schema::table('users', function (Blueprint $table) {
            // Hapus kolom jabatan_id yang lama jika ada
            if (Schema::hasColumn('users', 'jabatan_id')) {
                $table->dropColumn('jabatan_id');
            }
            
            // Tambahkan kolom jabatan_id dengan foreign key
            $table->unsignedBigInteger('jabatan_id')->after('role');
            $table->enum('status_akun', ['aktif', 'nonaktif'])->default('aktif')->after('jabatan_id');
            
            // Tambahkan foreign key constraint
            $table->foreign('jabatan_id')
                  ->references('id')
                  ->on('tbl_jabatan')
                  ->onDelete('restrict')
                  ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Hapus foreign key constraint terlebih dahulu
            $table->dropForeign(['jabatan_id']);
            
            // Kemudian hapus kolom
            $table->dropColumn(['jabatan_id', 'status_akun']);
        });
    }
};
