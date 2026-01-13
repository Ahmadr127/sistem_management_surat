<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'staff',
                'display_name' => 'Staff/Unit',
                'description' => 'Staff atau unit yang dapat membuat surat unit manager',
                'legacy_role_id' => 0
            ],
            [
                'name' => 'sekretaris',
                'display_name' => 'Sekretaris',
                'description' => 'Sekretaris yang mengelola surat masuk dan keluar',
                'legacy_role_id' => 1
            ],
            [
                'name' => 'direktur',
                'display_name' => 'Direktur',
                'description' => 'Direktur yang menyetujui disposisi',
                'legacy_role_id' => 2
            ],
            [
                'name' => 'super_admin',
                'display_name' => 'Super Admin',
                'description' => 'Administrator sistem dengan akses penuh',
                'legacy_role_id' => 3
            ],
            [
                'name' => 'manager',
                'display_name' => 'Manager',
                'description' => 'Manager yang menyetujui surat unit',
                'legacy_role_id' => 4
            ],
            [
                'name' => 'sekretaris_asp',
                'display_name' => 'Sekretaris ASP',
                'description' => 'Sekretaris khusus ASP',
                'legacy_role_id' => 5
            ],
            [
                'name' => 'general_manager',
                'display_name' => 'General Manager',
                'description' => 'General Manager yang mengawasi beberapa manager',
                'legacy_role_id' => 6
            ],
            [
                'name' => 'manager_keuangan',
                'display_name' => 'Manager Keuangan',
                'description' => 'Manager khusus keuangan',
                'legacy_role_id' => 7
            ],
            [
                'name' => 'direktur_asp',
                'display_name' => 'Direktur ASP',
                'description' => 'Direktur ASP',
                'legacy_role_id' => 8
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['name' => $role['name']],
                $role
            );
        }

        $this->command->info('Roles seeded successfully!');
    }
}
