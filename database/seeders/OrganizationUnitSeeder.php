<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OrganizationUnit;
use App\Models\OrganizationType;

class OrganizationUnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get organization types
        $holding = OrganizationType::where('name', 'holding')->first();
        $directorate = OrganizationType::where('name', 'directorate')->first();
        $department = OrganizationType::where('name', 'department')->first();

        // Create sample organization structure
        // Level 1: Holding
        $holdingUnit = OrganizationUnit::updateOrCreate(
            ['code' => 'HOLDING'],
            [
                'name' => 'PT Holding Company',
                'type_id' => $holding->id,
                'parent_id' => null,
                'head_id' => null,
                'description' => 'Perusahaan Holding',
                'is_active' => true
            ]
        );

        // Level 3: Directorate
        $direktorat = OrganizationUnit::updateOrCreate(
            ['code' => 'DIR01'],
            [
                'name' => 'Direktorat Operasional',
                'type_id' => $directorate->id,
                'parent_id' => $holdingUnit->id,
                'head_id' => null,
                'description' => 'Direktorat Operasional',
                'is_active' => true
            ]
        );

        // Level 4: Departments
        $deptIT = OrganizationUnit::updateOrCreate(
            ['code' => 'IT'],
            [
                'name' => 'Departemen IT',
                'type_id' => $department->id,
                'parent_id' => $direktorat->id,
                'head_id' => null,
                'description' => 'Departemen Teknologi Informasi',
                'is_active' => true
            ]
        );

        $deptFIN = OrganizationUnit::updateOrCreate(
            ['code' => 'FIN'],
            [
                'name' => 'Departemen Keuangan',
                'type_id' => $department->id,
                'parent_id' => $direktorat->id,
                'head_id' => null,
                'description' => 'Departemen Keuangan',
                'is_active' => true
            ]
        );

        $this->command->info('Organization units seeded successfully!');
    }
}
