<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add new role_id (foreign key to roles table)
            $table->foreignId('role_id')->nullable()->after('role')->constrained('roles')->onDelete('set null');
            
            // Add organization_unit_id
            $table->foreignId('organization_unit_id')->nullable()->after('role_id')->constrained('organization_units')->onDelete('set null');
            
            // Add indexes
            $table->index('role_id');
            $table->index('organization_unit_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropForeign(['organization_unit_id']);
            $table->dropColumn(['role_id', 'organization_unit_id']);
        });
    }
};
