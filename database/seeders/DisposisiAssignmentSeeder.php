<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Role;

class DisposisiAssignmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Truncate table
        DB::table('disposisi_assignments')->truncate();

        // 1. Define Rules by Role (Based on image and logical structure)
        $roleRules = [
            // Sekretaris (1) -> Direktur, Manager, Sekretaris ASP, Manager Keuangan
            1 => [2, 4, 5, 7],
            
            // Sekretaris ASP (5) -> Direktur, Manager, General Manager, Direktur ASP, Manager Keuangan, Sekretaris
            5 => [2, 4, 6, 8, 7, 1],
            
            // Manager (4) -> Direktur, Manager
            4 => [2, 4],
            
            
        ];

        foreach ($roleRules as $sourceRole => $targetRoles) {
            foreach ($targetRoles as $targetRole) {
                DB::table('disposisi_assignments')->insert([
                    'source_role' => $sourceRole,
                    'source_user_id' => null,
                    'target_role' => $targetRole,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // 2. Define Rules by User (Budi Manager IT from ManagerITSeeder)
        $user = User::where('email', 'manager.it@example.com')->first();
        
        if ($user) {
            // User Budi -> Direktur, Manager, General Manager, Manager Keuangan, Direktur ASP
            // Target IDs: 2, 4, 6, 7, 8
            $userTargets = [2, 4, 6, 7, 8];
            
            // Clear existing specific rules for this user to avoid duplicates if re-seeding
            DB::table('disposisi_assignments')->where('source_user_id', $user->id)->delete();

            foreach ($userTargets as $targetRole) {
                DB::table('disposisi_assignments')->insert([
                    'source_role' => null, // User specific
                    'source_user_id' => $user->id,
                    'target_role' => $targetRole,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            
            $this->command->info("✓ User-specific rules created for {$user->name} (ID: {$user->id})");
        } else {
             $this->command->warn("! User 'manager.it@example.com' not found. Please run 'php artisan db:seed --class=ManagerITSeeder' first.");
        }

        $this->command->info('✓ Disposisi Assignments seeded successfully!');
    }
}
