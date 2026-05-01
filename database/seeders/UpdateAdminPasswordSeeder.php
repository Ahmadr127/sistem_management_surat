<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UpdateAdminPasswordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where('username', 'admin')->first();
        if ($user) {
            $user->update([
                'password' => Hash::make('rsazra')
            ]);
            $this->command->info('Password for user "admin" has been updated to "rsazra".');
        } else {
            $this->command->error('User with username "admin" not found.');
        }
    }
}
