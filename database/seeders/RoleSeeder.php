<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define all roles matching existing structure
        $roles = [
            [
                'name' => 'staff',
                'display_name' => 'Staff',
                'description' => 'Staff biasa yang dapat membuat dan mengelola surat sendiri',
                'legacy_role_id' => 0
            ],
            [
                'name' => 'sekretaris',
                'display_name' => 'Sekretaris',
                'description' => 'Sekretaris yang dapat mereview dan menyetujui surat',
                'legacy_role_id' => 1
            ],
            [
                'name' => 'direktur',
                'display_name' => 'Direktur',
                'description' => 'Direktur yang dapat menyetujui surat final',
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
                'description' => 'Manager yang dapat menyetujui surat unit',
                'legacy_role_id' => 4
            ],
            [
                'name' => 'sekretaris_asp',
                'display_name' => 'Sekretaris ASP',
                'description' => 'Sekretaris ASP yang mengelola surat ASP',
                'legacy_role_id' => 5
            ],
            [
                'name' => 'general_manager',
                'display_name' => 'General Manager',
                'description' => 'General Manager dengan akses luas',
                'legacy_role_id' => 6
            ],
            [
                'name' => 'manager_keuangan',
                'display_name' => 'Manager Keuangan',
                'description' => 'Manager Keuangan yang menyetujui surat keuangan',
                'legacy_role_id' => 7
            ],
            [
                'name' => 'direktur_asp',
                'display_name' => 'Direktur ASP',
                'description' => 'Direktur ASP dengan akses penuh ASP',
                'legacy_role_id' => 8
            ]
        ];

        // Insert or update roles
        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(
                ['name' => $role['name']],
                array_merge($role, [
                    'created_at' => now(),
                    'updated_at' => now()
                ])
            );
        }

        $this->command->info('✓ Roles seeded successfully!');
        $this->command->info('  - Staff (legacy_role_id: 0)');
        $this->command->info('  - Sekretaris (legacy_role_id: 1)');
        $this->command->info('  - Direktur (legacy_role_id: 2)');
        $this->command->info('  - Super Admin (legacy_role_id: 3)');
        $this->command->info('  - Manager (legacy_role_id: 4)');
        $this->command->info('  - Sekretaris ASP (legacy_role_id: 5)');
        $this->command->info('  - General Manager (legacy_role_id: 6)');
        $this->command->info('  - Manager Keuangan (legacy_role_id: 7)');
        $this->command->info('  - Direktur ASP (legacy_role_id: 8)');
    }
}

