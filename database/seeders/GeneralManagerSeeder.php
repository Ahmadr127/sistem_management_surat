<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Jabatan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class GeneralManagerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            // Ambil jabatan yang sudah ada atau buat baru
            $jabatanGeneralManager = Jabatan::firstOrCreate(
                ['nama_jabatan' => 'General Manager'],
                [
                    'kode_jabatan' => 'GM',
                    'status' => 'aktif'
                ]
            );

            // Buat user General Manager
            $generalManager = User::firstOrCreate(
                ['username' => 'general_manager'],
                [
                    'name' => 'General Manager',
                    'email' => 'general_manager@gmail.com',
                    'password' => Hash::make('123'),
                    'role' => 6, // Role General Manager
                    'jabatan_id' => $jabatanGeneralManager->id,
                    'status_akun' => 'aktif',
                ]
            );

            // Update manager yang sudah ada untuk memiliki general manager
            $managers = User::whereIn('role', [4, 7])->get(); // Include both Manager and Manager Keuangan
            foreach ($managers as $manager) {
                // Hanya update jika manager belum memiliki general manager
                if (!$manager->general_manager_id) {
                    $manager->update(['general_manager_id' => $generalManager->id]);
                }
            }

            // Update staff yang sudah ada untuk memiliki general manager melalui manager mereka
            $staffWithManagers = User::where('role', 0)->whereNotNull('manager_id')->get();
            foreach ($staffWithManagers as $staff) {
                if ($staff->manager && $staff->manager->general_manager_id) {
                    $staff->update(['general_manager_id' => $staff->manager->general_manager_id]);
                }
            }

            $this->command->info('General Manager berhasil dibuat dan relasi berhasil diupdate!');

        } catch (\Exception $e) {
            Log::error('Error in GeneralManagerSeeder: ' . $e->getMessage());
            throw $e;
        }
    }
}
