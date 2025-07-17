<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Jabatan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class ManagerKeuanganIndependenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            // Ambil jabatan yang sudah ada atau buat baru
            $jabatanMK = Jabatan::firstOrCreate(
                ['nama_jabatan' => 'Manager Keuangan Independen'],
                [
                    'kode_jabatan' => 'MKI',
                    'status' => 'aktif'
                ]
            );

            // Buat user Manager Keuangan Independen
            $mkIndependen = User::firstOrCreate(
                ['username' => 'manager_keuangan_independen'],
                [
                    'name' => 'Manager Keuangan Independen',
                    'email' => 'manager_keuangan_independen@gmail.com',
                    'password' => Hash::make('123'),
                    'role' => 7, // Role Manager Keuangan
                    'jabatan_id' => $jabatanMK->id,
                    'general_manager_id' => null, // Tidak terhubung dengan general manager
                    'status_akun' => 'aktif',
                ]
            );

            // Buat staff yang dibawahi manager keuangan independen
            $staffKeuangan = User::firstOrCreate(
                ['username' => 'staff_keuangan'],
                [
                    'name' => 'Staff Keuangan',
                    'email' => 'staff_keuangan@gmail.com',
                    'password' => Hash::make('123'),
                    'role' => 0, // Role Staff
                    'jabatan_id' => $jabatanMK->id,
                    'manager_id' => $mkIndependen->id,
                    'general_manager_id' => null, // Staff juga tidak terhubung dengan general manager
                    'status_akun' => 'aktif',
                ]
            );

            $this->command->info('Manager Keuangan Independen dan Staff Keuangan berhasil dibuat!');
            $this->command->info('Username Manager: manager_keuangan_independen');
            $this->command->info('Username Staff: staff_keuangan');
            $this->command->info('Password: 123');

        } catch (\Exception $e) {
            Log::error('Error in ManagerKeuanganIndependenSeeder: ' . $e->getMessage());
            throw $e;
        }
    }
}
