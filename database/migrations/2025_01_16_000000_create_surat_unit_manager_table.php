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
        Schema::create('tbl_surat_unit_manager', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_surat');
            $table->date('tanggal_surat');
            $table->string('perihal');
            $table->text('isi_surat');
            $table->enum('jenis_surat', ['internal', 'eksternal'])->default('internal');
            $table->enum('sifat_surat', ['urgent', 'normal'])->default('normal');
            $table->string('perusahaan')->default('RSAZRA');
            $table->string('file_path')->nullable();
            $table->text('keterangan_unit')->nullable();
            $table->text('keterangan_manager')->nullable();
            $table->text('keterangan_sekretaris')->nullable();
            $table->text('keterangan_dirut')->nullable();
            
            // Status persetujuan
            $table->enum('status_manager', ['pending', 'review', 'approved', 'rejected'])->default('pending');
            $table->enum('status_sekretaris', ['pending', 'review', 'approved', 'rejected'])->default('pending');
            $table->enum('status_dirut', ['pending', 'review', 'approved', 'rejected'])->default('pending');
            
            // Timestamp review
            $table->timestamp('waktu_review_manager')->nullable();
            $table->timestamp('waktu_review_sekretaris')->nullable();
            $table->timestamp('waktu_review_dirut')->nullable();
            
            // Foreign keys
            $table->foreignId('unit_id')->constrained('users')->comment('Staff yang membuat surat');
            $table->foreignId('manager_id')->constrained('users')->comment('Manager yang menyetujui');
            $table->foreignId('sekretaris_id')->nullable()->constrained('users')->comment('Sekretaris yang menyetujui');
            $table->foreignId('dirut_id')->nullable()->constrained('users')->comment('Direktur yang memberikan disposisi');
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['unit_id', 'status_manager']);
            $table->index(['manager_id', 'status_manager']);
            $table->index(['sekretaris_id', 'status_sekretaris']);
            $table->index(['dirut_id', 'status_dirut']);
            $table->index('nomor_surat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_surat_unit_manager');
    }
}; 