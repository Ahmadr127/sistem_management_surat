<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Jabatan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class DirekturAdmKeuanganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            // Ambil jabatan yang sudah ada atau buat baru
            $jabatanDAK = Jabatan::firstOrCreate(
                ['nama_jabatan' => 'Direktur Administrasi Keuangan'],
                [
                    'kode_jabatan' => 'DAK',
                    'status' => 'aktif'
                ]
            );

            // Buat user Direktur Administrasi Keuangan
            $dak = User::firstOrCreate(
                ['username' => 'dak'],
                [
                    'name' => 'Direktur Administrasi Keuangan',
                    'email' => 'dak@gmail.com',
                    'password' => Hash::make('123'),
                    'role' => 7, // Role Direktur Adm Keuangan
                    'jabatan_id' => $jabatanDAK->id,
                    'status_akun' => 'aktif',
                ]
            );

            $this->command->info('Direktur Administrasi Keuangan berhasil dibuat!');

        } catch (\Exception $e) {
            Log::error('Error in DirekturAdmKeuanganSeeder: ' . $e->getMessage());
            throw $e;
        }
    }
}
