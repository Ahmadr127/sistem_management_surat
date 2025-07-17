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
            // Ambil data jabatan yang sudah ada dari database
            // Pastikan JabatanSeeder sudah dijalankan sebelumnya
            $jabatanDirut = Jabatan::where('kode_jabatan', 'DIRUT')->firstOrFail();
            $jabatanSekre = Jabatan::where('kode_jabatan', 'SEKRE')->firstOrFail();
            $jabatanMngr = Jabatan::where('kode_jabatan', 'MNGR')->firstOrFail();
            $jabatanStaff = Jabatan::where('kode_jabatan', 'STAFF')->firstOrFail();
            $jabatanIt = Jabatan::where('kode_jabatan', 'IT')->firstOrFail();

            $users = [
                [
                    'name' => 'Admin',
                    'username' => 'admin',
                    'email' => 'admin@gmail.com',
                    'password' => Hash::make('123'),
                    'role' => 3,
                    'jabatan_id' => $jabatanIt->id,
                    'status_akun' => 'aktif',
                ],
                [
                    'name' => 'Direktur Utama',
                    'username' => 'dirut',
                    'email' => 'dirut@gmail.com',
                    'password' => Hash::make('123'),
                    'role' => 2,
                    'jabatan_id' => $jabatanDirut->id,
                    'status_akun' => 'aktif',
                ],
                [
                    'name' => 'Direktur ASP',
                    'username' => 'dirut_asp',
                    'email' => 'dirut_asp@gmail.com',
                    'password' => Hash::make('123'),
                    'role' => 8,
                    'jabatan_id' => $jabatanDirut->id,
                    'status_akun' => 'aktif',
                ],
                [
                    'name' => 'Sekretaris',
                    'username' => 'sekretaris',
                    'email' => 'sekretaris@gmail.com',
                    'password' => Hash::make('123'),
                    'role' => 1,
                    'jabatan_id' => $jabatanSekre->id,
                    'status_akun' => 'aktif',
                ],
                [
                    'name' => 'Manager Unit A',
                    'username' => 'manager_a',
                    'email' => 'manager_a@gmail.com',
                    'password' => Hash::make('123'),
                    'role' => 4, // Role manager
                    'jabatan_id' => $jabatanMngr->id,
                    'status_akun' => 'aktif',
                ],
                [
                    'name' => 'Manager Unit B',
                    'username' => 'manager_b',
                    'email' => 'manager_b@gmail.com',
                    'password' => Hash::make('123'),
                    'role' => 4, // Role manager
                    'jabatan_id' => $jabatanMngr->id,
                    'status_akun' => 'aktif',
                ],
                [
                    'name' => 'Staff Unit A',
                    'username' => 'staff_a',
                    'email' => 'staff_a@gmail.com',
                    'password' => Hash::make('123'),
                    'role' => 0,
                    'jabatan_id' => $jabatanStaff->id,
                    'status_akun' => 'aktif',
                ],
                [
                    'name' => 'Staff Unit B',
                    'username' => 'staff_b',
                    'email' => 'staff_b@gmail.com',
                    'password' => Hash::make('123'),
                    'role' => 0,
                    'jabatan_id' => $jabatanStaff->id,
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
