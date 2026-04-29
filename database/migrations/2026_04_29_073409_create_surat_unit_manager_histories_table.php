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
        Schema::create('surat_unit_manager_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('surat_unit_manager_id');
            $table->unsignedBigInteger('user_id');
            $table->string('action');
            $table->text('keterangan')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
            
            $table->foreign('surat_unit_manager_id', 'sum_id_foreign')->references('id')->on('tbl_surat_unit_manager')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_unit_manager_histories');
    }
};
