<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

$user = User::where('username', 'komitemutu')->first();
if ($user) {
    echo "User: " . $user->username . "\n";
    echo "Dibuat: " . $user->disposisiDibuat()->count() . "\n";
    echo "Diterima: " . $user->disposisiDiterima()->count() . "\n";
} else {
    echo "User komitemutu not found\n";
}
