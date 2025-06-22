<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Jabatan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            // Buat atau update jabatan terlebih dahulu
            $jabatanData = [
                'IT' => Jabatan::firstOrCreate(['nama_jabatan' => 'IT'], ['status' => 'aktif']),
                'STAFF' => Jabatan::firstOrCreate(['nama_jabatan' => 'STAFF'], ['status' => 'aktif']),
                'MANAGER' => Jabatan::firstOrCreate(['nama_jabatan' => 'MANAGER'], ['status' => 'aktif']),
                'DIRUT' => Jabatan::firstOrCreate(['nama_jabatan' => 'DIRUT'], ['status' => 'aktif']),
                'SEKERTARIS' => Jabatan::firstOrCreate(['nama_jabatan' => 'SEKERTARIS'], ['status' => 'aktif'])
            ];

            $users = [
                [
                    'name' => 'Admin',
                    'username' => 'admin',
                    'email' => 'admin@gmail.com',
                    'password' => Hash::make('123'),
                    'role' => 3,
                    'jabatan_id' => $jabatanData['IT']->id,
                    'status_akun' => 'aktif',
                ],
                [
                    'name' => 'Direktur Utama',
                    'username' => 'dirut',
                    'email' => 'dirut@gmail.com',
                    'password' => Hash::make('123'),
                    'role' => 2,
                    'jabatan_id' => $jabatanData['DIRUT']->id,
                    'status_akun' => 'aktif',
                ],
                [
                    'name' => 'Sekretaris',
                    'username' => 'sekretaris',
                    'email' => 'sekretaris@gmail.com',
                    'password' => Hash::make('123'),
                    'role' => 1,
                    'jabatan_id' => $jabatanData['SEKERTARIS']->id,
                    'status_akun' => 'aktif',
                ],
                [
                    'name' => 'Manager Unit A',
                    'username' => 'manager_a',
                    'email' => 'manager_a@gmail.com',
                    'password' => Hash::make('123'),
                    'role' => 4, // Role manager
                    'jabatan_id' => $jabatanData['MANAGER']->id,
                    'status_akun' => 'aktif',
                ],
                [
                    'name' => 'Manager Unit B',
                    'username' => 'manager_b',
                    'email' => 'manager_b@gmail.com',
                    'password' => Hash::make('123'),
                    'role' => 4, // Role manager
                    'jabatan_id' => $jabatanData['MANAGER']->id,
                    'status_akun' => 'aktif',
                ],
                [
                    'name' => 'Staff Unit A',
                    'username' => 'staff_a',
                    'email' => 'staff_a@gmail.com',
                    'password' => Hash::make('123'),
                    'role' => 0,
                    'jabatan_id' => $jabatanData['STAFF']->id,
                    'status_akun' => 'aktif',
                ],
                [
                    'name' => 'Staff Unit B',
                    'username' => 'staff_b',
                    'email' => 'staff_b@gmail.com',
                    'password' => Hash::make('123'),
                    'role' => 0,
                    'jabatan_id' => $jabatanData['STAFF']->id,
                    'status_akun' => 'aktif',
                ]
            ];

            foreach ($users as $userData) {
                User::updateOrCreate(
                    ['username' => $userData['username']],
                    $userData
                );
            }

            // Set manager_id untuk staff setelah semua user dibuat
            $managerA = User::where('username', 'manager_a')->first();
            $managerB = User::where('username', 'manager_b')->first();
            $staffA = User::where('username', 'staff_a')->first();
            $staffB = User::where('username', 'staff_b')->first();

            if ($managerA && $staffA) {
                $staffA->update(['manager_id' => $managerA->id]);
            }
            if ($managerB && $staffB) {
                $staffB->update(['manager_id' => $managerB->id]);
            }

        } catch (\Exception $e) {
            Log::error('Error in UserSeeder: ' . $e->getMessage());
            throw $e;
        }
    }
}
