<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Jabatan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class IndependentManagerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            // Ambil jabatan yang sudah ada atau buat baru
            $jabatanManager = Jabatan::firstOrCreate(
                ['nama_jabatan' => 'Manager Independen'],
                [
                    'kode_jabatan' => 'MI',
                    'status' => 'aktif'
                ]
            );

            // Buat user Manager Independen
            $independentManager = User::firstOrCreate(
                ['username' => 'manager_independen'],
                [
                    'name' => 'Manager Independen',
                    'email' => 'manager_independen@gmail.com',
                    'password' => Hash::make('123'),
                    'role' => 4, // Role Manager
                    'jabatan_id' => $jabatanManager->id,
                    'general_manager_id' => null, // Tidak terhubung dengan general manager
                    'status_akun' => 'aktif',
                ]
            );

            // Buat staff yang dibawahi manager independen
            $staffIndependen = User::firstOrCreate(
                ['username' => 'staff_independen'],
                [
                    'name' => 'Staff Independen',
                    'email' => 'staff_independen@gmail.com',
                    'password' => Hash::make('123'),
                    'role' => 0, // Role Staff
                    'jabatan_id' => $jabatanManager->id,
                    'manager_id' => $independentManager->id,
                    'general_manager_id' => null, // Staff juga tidak terhubung dengan general manager
                    'status_akun' => 'aktif',
                ]
            );

            $this->command->info('Manager Independen dan Staff berhasil dibuat!');

        } catch (\Exception $e) {
            Log::error('Error in IndependentManagerSeeder: ' . $e->getMessage());
            throw $e;
        }
    }
}
