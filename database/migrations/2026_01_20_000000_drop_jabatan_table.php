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
        // 1. Hapus foreign key dan kolom jabatan_id di users
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'jabatan_id')) {
                // Drop foreign key first if exists (nama constraint biasanya users_jabatan_id_foreign)
                // Kita gunakan try-catch atau pengecekan array constraint jika memungkinkan, 
                // tapi cara aman di Laravel adalah dropForeign dengan array kolom
                try {
                    $table->dropForeign(['jabatan_id']);
                } catch (\Exception $e) {
                    // Ignore if constraint doesn't exist
                }
                $table->dropColumn('jabatan_id');
            }
        });

        // 2. Hapus tabel tbl_jabatan
        Schema::dropIfExists('tbl_jabatan');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Create table tbl_jabatan
        Schema::create('tbl_jabatan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_jabatan');
            $table->string('kode_jabatan')->nullable();
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->timestamps();
        });

        // Add column back to users
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('jabatan_id')->nullable()->after('role');
            $table->foreign('jabatan_id')->references('id')->on('tbl_jabatan');
        });
    }
};
