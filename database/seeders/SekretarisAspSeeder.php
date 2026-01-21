<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class SekretarisAspSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            User::updateOrCreate(
                ['username' => 'sekretaris_asp'],
                [
                    'name' => 'Sekretaris ASP',
                    'email' => 'sekretaris_asp@gmail.com',
                    'password' => Hash::make('123'),
                    'role' => 5,
                    'organization_unit_id' => null,
                    'status_akun' => 'aktif',
                ]
            );
            
            Log::info('Sekretaris ASP user seeded successfully.');
            
        } catch (\Exception $e) {
            Log::error('Error in SekretarisAspSeeder: ' . $e->getMessage());
            throw $e;
        }
    }
}
