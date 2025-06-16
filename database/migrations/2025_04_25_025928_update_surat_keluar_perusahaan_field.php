<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Update the perusahaan field in tbl_surat_keluar to work with the Perusahaan model.
     */
    public function up(): void
    {
        Schema::table('tbl_surat_keluar', function (Blueprint $table) {
            // Add a comment to the perusahaan field to indicate it references the kode field in perusahaans table
            // We're not changing the field type to maintain compatibility with existing data
            $table->string('perusahaan')->comment('References kode field in perusahaans table')->change();
            
            // Add an index to improve query performance
            $table->index('perusahaan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_surat_keluar', function (Blueprint $table) {
            // Remove the index
            $table->dropIndex(['perusahaan']);
            
            // Remove the comment
            $table->string('perusahaan')->comment('')->change();
        });
    }
};
