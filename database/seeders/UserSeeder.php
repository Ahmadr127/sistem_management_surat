<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
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
            // Ambil Unit Organisasi
            $deptIT = \App\Models\OrganizationUnit::where('code', 'IT')->first();

            $users = [
                [
                    'name' => 'Admin',
                    'username' => 'admin',
                    'email' => 'admin@gmail.com',
                    'password' => Hash::make('123'),
                    'role' => 3,
                    'organization_unit_id' => $deptIT ? $deptIT->id : null,
                    'status_akun' => 'aktif',
                ],
                [
                    'name' => 'Direktur Utama',
                    'username' => 'dirut',
                    'email' => 'dirut@gmail.com',
                    'password' => Hash::make('123'),
                    'role' => 2,
                    'organization_unit_id' => null, // Dirut usually top level or holding
                    'status_akun' => 'aktif',
                ],
                [
                    'name' => 'Direktur ASP',
                    'username' => 'dirut_asp',
                    'email' => 'dirut_asp@gmail.com',
                    'password' => Hash::make('123'),
                    'role' => 8,
                    'organization_unit_id' => null,
                    'status_akun' => 'aktif',
                ],
                [
                    'name' => 'Sekretaris',
                    'username' => 'sekretaris',
                    'email' => 'sekretaris@gmail.com',
                    'password' => Hash::make('123'),
                    'role' => 1,
                    'organization_unit_id' => null,
                    'status_akun' => 'aktif',
                ],
                [
                    'name' => 'Staff Departemen IT',
                    'username' => 'staff_it',
                    'email' => 'staff_it@gmail.com',
                    'password' => Hash::make('123'),
                    'role' => 0,
                    'organization_unit_id' => $deptIT ? $deptIT->id : null, // Assign to IT
                    'status_akun' => 'aktif',
                ]
            ];

            foreach ($users as $userData) {
                User::updateOrCreate(
                    ['username' => $userData['username']],
                    $userData
                );
            }

        } catch (\Exception $e) {
            Log::error('Error in UserSeeder: ' . $e->getMessage());
            throw $e;
        }
    }
}
