<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$search = strtolower('icu');

$count1 = App\Models\OrganizationUnit::where('name', 'like', "%{$search}%")->orWhere('code', 'like', "%{$search}%")->count();
$count2 = App\Models\OrganizationUnit::whereRaw('LOWER(name) LIKE ?', ["%{$search}%"])->orWhereRaw('LOWER(code) LIKE ?', ["%{$search}%"])->count();

echo "Count 1 (original): $count1\n";
echo "Count 2 (lower): $count2\n";
