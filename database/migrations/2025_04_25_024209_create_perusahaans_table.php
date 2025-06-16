<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('perusahaans', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 20)->unique();
            $table->string('nama_perusahaan', 100);
            $table->text('alamat')->nullable();
            $table->string('telepon', 20)->nullable();
            $table->string('email')->nullable();
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->timestamps();
        });

        // Insert default data
        DB::table('perusahaans')->insert([
            [
                'kode' => 'RSAZRA',
                'nama_perusahaan' => 'RS AZRA',
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'kode' => 'ASP',
                'nama_perusahaan' => 'ASP',
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'kode' => 'DIRUT',
                'nama_perusahaan' => 'DIRUT',
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perusahaans');
    }
};
