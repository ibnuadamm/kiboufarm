<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "--- Seeding Admin User ---\n";

$email = 'admin@gmail.com';
$password = 'admin123';

$user = User::where('email', $email)->first();

if ($user) {
    echo "User $email exists. Updating password...\n";
    $user->password = Hash::make($password);
    $user->name = 'Administrator';
    $user->save();
} else {
    echo "Creating user $email...\n";
    User::create([
        'name' => 'Administrator',
        'email' => $email,
        'password' => Hash::make($password),
    ]);
}

echo "Done.\n";
