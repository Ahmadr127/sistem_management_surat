<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add username column as nullable first
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable()->unique()->after('id');
        });

        // Update existing users to have usernames based on their emails
        $users = User::all();
        foreach ($users as $user) {
            $username = explode('@', $user->email)[0];
            $user->username = $username;
            $user->save();
        }

        // Now make username required (not nullable)
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('username');
        });
    }
};
