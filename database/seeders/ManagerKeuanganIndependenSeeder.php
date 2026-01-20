<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
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
            // Buat staff yang dibawahi manager keuangan independen (tanpa manager)
            $staffKeuangan = User::firstOrCreate(
                ['username' => 'staff_keuangan'],
                [
                    'name' => 'Staff Keuangan',
                    'email' => 'staff_keuangan@gmail.com',
                    'password' => Hash::make('123'),
                    'role' => 0, // Role Staff
                    'manager_id' => null,
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
