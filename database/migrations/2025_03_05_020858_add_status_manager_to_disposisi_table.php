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
        Schema::table('tbl_disposisi', function (Blueprint $table) {
            $table->enum('status_manager', ['pending', 'review', 'approved', 'rejected'])->default('pending')->after('status_sekretaris');
            $table->timestamp('waktu_review_manager')->nullable()->comment('Waktu terakhir review oleh manager')->after('waktu_review_sekretaris');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_disposisi', function (Blueprint $table) {
            $table->dropColumn(['status_manager', 'waktu_review_manager']);
        });
    }
}; 