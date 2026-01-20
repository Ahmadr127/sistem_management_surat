<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class ManagerKeuanganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            // Ambil Unit Organisasi Keuangan
            $deptFIN = \App\Models\OrganizationUnit::where('code', 'FIN')->first();

            // Cek apakah ada General Manager
            $generalManager = User::where('role', 6)->first();

            // Buat user Manager Keuangan
            $mk = User::firstOrCreate(
                ['username' => 'manager_keuangan'],
                [
                    'name' => 'Manager Keuangan',
                    'email' => 'manager_keuangan@gmail.com',
                    'password' => Hash::make('123'),
                    'role' => 7, // Role Manager Keuangan
                    'organization_unit_id' => $deptFIN ? $deptFIN->id : null,
                    'general_manager_id' => $generalManager ? $generalManager->id : null, // Hubungkan dengan General Manager jika ada
                    'status_akun' => 'aktif',
                ]
            );

            $this->command->info('Manager Keuangan berhasil dibuat!');
            $this->command->info('Username: manager_keuangan');
            $this->command->info('Password: 123');
            if ($generalManager) {
                $this->command->info('Manager Keuangan terhubung dengan General Manager: ' . $generalManager->name);
            } else {
                $this->command->info('Manager Keuangan dibuat sebagai manager independen (tidak terhubung dengan General Manager)');
            }

        } catch (\Exception $e) {
            Log::error('Error in ManagerKeuanganSeeder: ' . $e->getMessage());
            throw $e;
        }
    }
}
