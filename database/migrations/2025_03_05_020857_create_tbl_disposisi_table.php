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
        Schema::create('tbl_disposisi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('surat_keluar_id')->constrained('tbl_surat_keluar')->onDelete('cascade');
            $table->text('keterangan_pengirim')->nullable();
            $table->text('keterangan_sekretaris')->nullable();
            $table->text('keterangan_dirut')->nullable();
            $table->enum('status_sekretaris', ['pending', 'review', 'approved', 'rejected'])->default('pending');
            $table->enum('status_dirut', ['pending', 'review', 'approved', 'rejected'])->default('pending');
            $table->timestamp('waktu_review_sekretaris')->nullable()->comment('Waktu terakhir review oleh sekretaris');
            $table->timestamp('waktu_review_dirut')->nullable()->comment('Waktu terakhir review oleh dirut');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });

        // Buat tabel pivot untuk relasi many-to-many antara disposisi dan user (tujuan)
        Schema::create('tbl_disposisi_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('disposisi_id')->constrained('tbl_disposisi')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            
            // Memastikan kombinasi disposisi_id dan user_id unik
            $table->unique(['disposisi_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_disposisi_user');
        Schema::dropIfExists('tbl_disposisi');
    }
}; 