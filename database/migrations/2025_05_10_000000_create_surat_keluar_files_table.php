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
        Schema::create('surat_keluar_files', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('surat_keluar_id');
            $table->string('file_path');
            $table->string('file_type')->nullable();
            $table->string('original_name')->nullable();
            $table->timestamps();

            $table->foreign('surat_keluar_id')
                ->references('id')->on('tbl_surat_keluar')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_keluar_files');
    }
}; 