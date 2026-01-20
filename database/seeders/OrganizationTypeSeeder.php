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
