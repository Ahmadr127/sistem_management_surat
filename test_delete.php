<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$unit = App\Models\OrganizationUnit::first();
if ($unit) {
    echo "ID: {$unit->id}\n";
    echo "Children: " . $unit->children()->count() . "\n";
    echo "Members: " . $unit->members()->count() . "\n";
    
    // Try deleting an organization unit
    try {
        $testUnit = App\Models\OrganizationUnit::create([
            'name' => 'Test Delete Unit',
            'code' => 'TESTDEL',
            'type_id' => 1,
            'is_active' => true
        ]);
        
        echo "Created test unit ID: {$testUnit->id}\n";
        $testUnit->delete();
        echo "Deleted test unit successfully.\n";
        
    } catch (\Exception $e) {
        echo "Exception during delete: " . $e->getMessage() . "\n";
    }
} else {
    echo "No units found.\n";
}
