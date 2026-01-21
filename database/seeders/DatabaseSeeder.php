<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // Organization structure seeders (MUST RUN FIRST)
            OrganizationTypeSeeder::class,
            OrganizationUnitSeeder::class,

            // Existing seeders
            // JabatanSeeder::class, // DEPRECATED: Replaced by Role + Organization Logic
            UserSeeder::class,
            GeneralManagerSeeder::class,
            ManagerKeuanganSeeder::class,
            IndependentManagerSeeder::class,
            ManagerKeuanganIndependenSeeder::class,
            ManagerITSeeder::class,
            KomiteSeeder::class,
            
            // New permission system seeders
            PermissionSeeder::class,
            RoleSeeder::class,
            RolePermissionSeeder::class,
            
            // Update existing users with new role system
            UserRoleUpdateSeeder::class,
        ]);
    }
}
