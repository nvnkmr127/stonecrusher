<?php

use App\Models\User;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$users = User::with('roles')->get();

foreach ($users as $user) {
    echo "User: " . $user->email . " | Roles: " . $user->roles->pluck('name')->implode(', ') . "\n";
}
