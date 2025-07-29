<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Get first user with SKPD
$user = \App\Models\User::whereHas('skpd')->with('skpd')->first();

if ($user) {
    echo "User ID: {$user->id}\n";
    echo "User Name: {$user->name}\n";
    echo "Parsial URL: http://localhost/manajemenanggaran/{$user->id}/parsial\n";
} else {
    echo "No user found with SKPD!\n";
}
