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
        Schema::create('tbl_surat_keluar', function (Blueprint $table) {
            $table->id();
            $table->string('perusahaan');
            $table->string('nomor_surat');
            $table->enum('jenis_surat', ['internal', 'eksternal'])->default('internal');
            $table->enum('sifat_surat', ['urgent', 'normal'])->default('normal');
            $table->date('tanggal_surat');
            $table->foreignId('tujuan')->constrained('users')->onDelete('cascade');
            $table->string('perihal');
            $table->text('keterangan_pengirim')->nullable();
            $table->text('keterangan_sekretaris')->nullable();
            $table->text('keterangan_dirut')->nullable();
            $table->string('file_path')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void    
    {
        Schema::dropIfExists('tbl_surat_keluar');
    }
};
