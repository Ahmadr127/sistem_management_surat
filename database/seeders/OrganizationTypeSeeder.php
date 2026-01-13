<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OrganizationType;

class OrganizationTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            [
                'name' => 'holding',
                'display_name' => 'Holding Company',
                'level' => 1,
                'description' => 'Perusahaan induk atau holding'
            ],
            [
                'name' => 'company',
                'display_name' => 'Perusahaan',
                'level' => 2,
                'description' => 'Perusahaan atau anak perusahaan'
            ],
            [
                'name' => 'directorate',
                'display_name' => 'Direktorat',
                'level' => 3,
                'description' => 'Direktorat dalam organisasi'
            ],
            [
                'name' => 'department',
                'display_name' => 'Departemen',
                'level' => 4,
                'description' => 'Departemen atau divisi'
            ],
            [
                'name' => 'unit',
                'display_name' => 'Unit',
                'level' => 5,
                'description' => 'Unit kerja terkecil'
            ],
        ];

        foreach ($types as $type) {
            OrganizationType::updateOrCreate(
                ['name' => $type['name']],
                $type
            );
        }

        $this->command->info('Organization types seeded successfully!');
    }
}
