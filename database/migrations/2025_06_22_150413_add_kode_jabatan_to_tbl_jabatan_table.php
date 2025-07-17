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
        Schema::table('tbl_jabatan', function (Blueprint $table) {
            $table->string('kode_jabatan')->unique()->nullable()->after('nama_jabatan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_jabatan', function (Blueprint $table) {
            $table->dropColumn('kode_jabatan');
        });
    }
};
