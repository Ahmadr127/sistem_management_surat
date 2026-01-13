<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;

class UserRoleUpdateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Updates existing users to use new role system
     */
    public function run(): void
    {
        // Get all roles
        $roles = Role::all()->keyBy('legacy_role_id');

        // Update all existing users
        $users = User::all();
        
        foreach ($users as $user) {
            if (isset($roles[$user->role])) {
                $user->role_id = $roles[$user->role]->id;
                $user->save();
                
                $this->command->info("Updated user: {$user->name} -> {$roles[$user->role]->display_name}");
            }
        }

        $this->command->info('User roles updated successfully!');
    }
}
