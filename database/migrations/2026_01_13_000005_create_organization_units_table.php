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
        Schema::create('organization_units', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Unit name');
            $table->string('code')->unique()->comment('Unit code (e.g., IT, HR, FIN)');
            $table->foreignId('type_id')->constrained('organization_types')->onDelete('restrict');
            $table->foreignId('parent_id')->nullable()->constrained('organization_units')->onDelete('restrict');
            $table->foreignId('head_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Indexes for better performance
            $table->index('type_id');
            $table->index('parent_id');
            $table->index('head_id');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organization_units');
    }
};
