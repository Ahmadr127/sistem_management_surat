<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = App\Models\User::where('name', 'like', '%MIFTAHUDIN%')
    ->with('roleModel.permissions')
    ->first();

if ($user) {
    echo "User: {$user->name}\n";
    echo "role_id: {$user->role_id}\n";
    echo "Role: {$user->roleModel->name}\n";
    echo "Permissions: " . $user->roleModel->permissions->pluck('name')->join(', ') . "\n\n";
    
    $perms = ['view_dashboard', 'approve_surat_unit', 'create_surat_unit', 'view_laporan', 'manage_surat_keluar'];
    foreach ($perms as $perm) {
        $has = $user->hasPermission($perm) ? 'YES' : 'NO';
        echo "  hasPermission({$perm}): {$has}\n";
    }
}
