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
        Schema::create('organization_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->comment('Type key (e.g., department, division)');
            $table->string('display_name')->comment('Human readable name');
            $table->integer('level')->comment('Hierarchy level (1=highest, 5=lowest)');
            $table->text('description')->nullable()->comment('Type description');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organization_types');
    }
};
