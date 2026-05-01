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
        Schema::table('tbl_surat_unit_manager', function (Blueprint $table) {
            $table->dropColumn([
                'keterangan_sekretaris',
                'keterangan_dirut',
                'status_sekretaris',
                'status_dirut',
                'waktu_review_sekretaris',
                'waktu_review_dirut',
                'sekretaris_id',
                'dirut_id'
            ]);
            
            $table->unsignedBigInteger('surat_keluar_id')->nullable()->after('status_manager');
            
            // Assuming tbl_surat_keluar exists and id is the primary key. If you don't have constraints, you can skip the foreign key.
            // Let's add foreign key if possible, but just an index is fine if we're not sure about constraint strictness.
            $table->index('surat_keluar_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_surat_unit_manager', function (Blueprint $table) {
            $table->dropColumn('surat_keluar_id');
            
            $table->text('keterangan_sekretaris')->nullable();
            $table->text('keterangan_dirut')->nullable();
            $table->string('status_sekretaris')->default('pending');
            $table->string('status_dirut')->default('pending');
            $table->timestamp('waktu_review_sekretaris')->nullable();
            $table->timestamp('waktu_review_dirut')->nullable();
            $table->unsignedBigInteger('sekretaris_id')->nullable();
            $table->unsignedBigInteger('dirut_id')->nullable();
        });
    }
};
