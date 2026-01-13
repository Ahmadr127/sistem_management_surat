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
        $company = OrganizationType::where('name', 'company')->first();
        $directorate = OrganizationType::where('name', 'directorate')->first();
        $department = OrganizationType::where('name', 'department')->first();
        $unit = OrganizationType::where('name', 'unit')->first();

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

        // Level 2: Company
        $companyUnit = OrganizationUnit::updateOrCreate(
            ['code' => 'COMP01'],
            [
                'name' => 'PT Anak Perusahaan',
                'type_id' => $company->id,
                'parent_id' => $holdingUnit->id,
                'head_id' => null,
                'description' => 'Anak Perusahaan',
                'is_active' => true
            ]
        );

        // Level 3: Directorate
        $direktorat = OrganizationUnit::updateOrCreate(
            ['code' => 'DIR01'],
            [
                'name' => 'Direktorat Operasional',
                'type_id' => $directorate->id,
                'parent_id' => $companyUnit->id,
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

        $deptHR = OrganizationUnit::updateOrCreate(
            ['code' => 'HR'],
            [
                'name' => 'Departemen HR',
                'type_id' => $department->id,
                'parent_id' => $direktorat->id,
                'head_id' => null,
                'description' => 'Departemen Human Resources',
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

        // Level 5: Units
        OrganizationUnit::updateOrCreate(
            ['code' => 'IT-DEV'],
            [
                'name' => 'Unit Development',
                'type_id' => $unit->id,
                'parent_id' => $deptIT->id,
                'head_id' => null,
                'description' => 'Unit Pengembangan Software',
                'is_active' => true
            ]
        );

        OrganizationUnit::updateOrCreate(
            ['code' => 'IT-SUP'],
            [
                'name' => 'Unit Support',
                'type_id' => $unit->id,
                'parent_id' => $deptIT->id,
                'head_id' => null,
                'description' => 'Unit IT Support',
                'is_active' => true
            ]
        );

        $this->command->info('Organization units seeded successfully!');
    }
}
