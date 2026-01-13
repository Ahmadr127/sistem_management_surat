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
            // Existing seeders
            JabatanSeeder::class,
            UserSeeder::class,
            
            // New permission system seeders
            PermissionSeeder::class,
            RoleSeeder::class,
            RolePermissionSeeder::class,
            
            // Organization structure seeders
            OrganizationTypeSeeder::class,
            OrganizationUnitSeeder::class,
            
            // Update existing users with new role system
            UserRoleUpdateSeeder::class,
        ]);
    }
}
