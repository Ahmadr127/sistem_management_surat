<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OrganizationType;
use App\Models\OrganizationUnit;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class KomiteSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Organization Type "Komite"
        $type = OrganizationType::firstOrCreate(
            ['name' => 'komite'],
            [
                'display_name' => 'Komite',
                'level' => 3, // Assuming level 3 is appropriate (below Directorate/Division)
                'description' => 'Unit organisasi berbentuk komite'
            ]
        );

        // 2. Create Organization Unit "Komite Etik"
        $unit = OrganizationUnit::firstOrCreate(
            ['code' => 'KOM-ETIK'],
            [
                'name' => 'Komite Etik',
                'type_id' => $type->id,
                'description' => 'Komite Etik Rumah Sakit',
                'is_active' => true
            ]
        );

        // 3. Create "Kepala Komite" (Role: Manager - 4)
        $kepala = User::firstOrCreate(
            ['email' => 'kepala.komite@rsazra.co.id'],
            [
                'name' => 'Dr. Kepala Komite',
                'username' => 'kepala.komite', // Added username
                'password' => Hash::make('password'),
                'role' => 4, // Manager
                'organization_unit_id' => $unit->id,
                'status_akun' => 'aktif'
            ]
        );

        // Update unit head
        $unit->update(['head_id' => $kepala->id]);

        // 4. Create "Anggota Komite" (Role: Staff - 0)
        $anggota = User::firstOrCreate(
            ['email' => 'anggota.komite@rsazra.co.id'],
            [
                'name' => 'Anggota Komite 1',
                'username' => 'anggota.komite', // Added username
                'password' => Hash::make('password'),
                'role' => 0, // Staff
                'organization_unit_id' => $unit->id,
                'status_akun' => 'aktif',
                'manager_id' => $kepala->id // Assign manager
            ]
        );
        
        $this->command->info('Komite Seeder executed successfully.');
    }
}
