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
        Schema::table('tbl_disposisi_user', function (Blueprint $table) {
            $table->boolean('dibaca')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_disposisi_user', function (Blueprint $table) {
            $table->dropColumn('dibaca');
        });
    }
};
