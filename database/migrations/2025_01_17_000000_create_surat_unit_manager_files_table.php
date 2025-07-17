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
        Schema::create('surat_unit_manager_files', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('surat_unit_manager_id');
            $table->string('file_path');
            $table->string('original_name');
            $table->string('file_type')->nullable();
            $table->integer('file_size')->nullable(); // dalam bytes
            $table->timestamps();

            $table->foreign('surat_unit_manager_id')
                ->references('id')->on('tbl_surat_unit_manager')
                ->onDelete('cascade');
                
            // Index untuk performa
            $table->index('surat_unit_manager_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_unit_manager_files');
    }
}; 