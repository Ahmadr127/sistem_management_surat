<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\OrganizationUnit;
use Illuminate\Support\Facades\Hash;

class ManagerITSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Cari Unit Departemen IT
        $deptIT = OrganizationUnit::where('code', 'IT')->first();

        if (!$deptIT) {
            $this->command->error('Departemen IT tidak ditemukan! Pastikan OrganizationUnitSeeder sudah dijalankan.');
            return;
        }

        // 2. Cari Role Manager
        $roleManager = Role::where('name', 'manager')->first();
        if (!$roleManager) {
            // Fallback jika role belum ada di DB (misal belum seed role)
            // Tapi idealnya RoleSeeder sudah jalan
            $roleManagerId = 4; // ID default manager
        } else {
            $roleManagerId = $roleManager->id;
        }

        // 3. Buat User Manager IT
        $managerIT = User::updateOrCreate(
            ['email' => 'manager.it@example.com'],
            [
                'name' => 'Budi Manager IT',
                'username' => 'manager_it',
                'password' => Hash::make('password'),
                'role' => 4, // Legacy role ID for Manager
                'role_id' => $roleManagerId,
                'organization_unit_id' => $deptIT->id,
                'status_akun' => 'aktif',
                'manager_id' => null, // Manager biasanya lapor ke GM atau Direktur (bisa diset nanti)
                'general_manager_id' => null,
            ]
        );

        // 4. Set User sebagai Head dari Unit IT
        $deptIT->update(['head_id' => $managerIT->id]);

        $this->command->info('Manager IT created and assigned to Departemen IT successfully!');
    }
}
