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
        Schema::table('disposisi_assignments', function (Blueprint $table) {
            $table->unsignedBigInteger('source_user_id')->nullable()->after('source_role');
            $table->integer('source_role')->nullable()->change();
            
            // Add foreign key if users table uses bigIncrements or similar
            // Assuming users table uses bigIncrements 'id'
            // $table->foreign('source_user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('disposisi_assignments', function (Blueprint $table) {
            $table->dropColumn('source_user_id');
            $table->integer('source_role')->nullable(false)->change();
        });
    }
};
