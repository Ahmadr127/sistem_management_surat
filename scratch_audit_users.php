<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

$userIds = [66, 33, 10, 64, 71, 2, 72, 77, 73, 5, 65, 52, 55, 68, 62, 67, 51, 76, 69, 59, 74, 54, 53, 23, 70, 60, 75, 61, 63, 9, 7];
$hasDisposisi = User::whereIn('id', $userIds)->whereHas('disposisiDibuat')->pluck('id')->toArray();

echo "Users in list who HAVE disposisiDibuat: " . implode(', ', $hasDisposisi) . "\n";

$hasSuratKeluar = User::whereIn('id', $userIds)->whereHas('suratKeluarDibuat')->pluck('id')->toArray();
echo "Users in list who HAVE suratKeluarDibuat (non-trashed): " . implode(', ', $hasSuratKeluar) . "\n";

$hasSuratKeluarTrashed = User::whereIn('id', $userIds)->where(function($q) {
    $q->whereHas('suratKeluarDibuat', function($q2) {
        $q2->withTrashed();
    });
})->pluck('id')->toArray();
echo "Users in list who HAVE suratKeluarDibuat (WITH TRASHED): " . implode(', ', $hasSuratKeluarTrashed) . "\n";
