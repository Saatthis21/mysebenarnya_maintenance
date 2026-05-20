<?php

require 'vendor/autoload.php';

use Illuminate\Support\Facades\Hash;

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing existing user authentication...\n\n";

// Common passwords to test
$commonPasswords = [
    'password',
    'Password123',
    '123456',
    'admin',
    'mcmc123',
    'public123'
];

$testUsers = [
    ['email' => 'john@example.com', 'type' => 'public', 'name' => 'John Doe'],
    ['email' => 'admin@mcmc.gov.my', 'type' => 'mcmc', 'name' => 'MCMC Admin'],
    ['email' => 'staff@mcmc.gov.my', 'type' => 'mcmc', 'name' => 'MCMC Staff'],
];

foreach ($testUsers as $testUser) {
    echo "Testing user: {$testUser['name']} ({$testUser['email']})\n";

    $user = App\Models\UserRecord::where('email', $testUser['email'])
        ->where('user_type', $testUser['type'])
        ->first();

    if (!$user) {
        echo "  ❌ User not found!\n\n";
        continue;
    }

    echo "  ✅ User found in database\n";
    echo "  Password hash: " . substr($user->password, 0, 20) . "...\n";

    $passwordFound = false;
    foreach ($commonPasswords as $password) {
        if (Hash::check($password, $user->password)) {
            echo "  ✅ Password is: '$password'\n";
            $passwordFound = true;
            break;
        }
    }

    if (!$passwordFound) {
        echo "  ❌ Password not found in common list\n";
        echo "  Let's reset to 'password' for testing...\n";

        $user->password = Hash::make('password');
        $user->save();
        echo "  ✅ Password reset to 'password'\n";
    }

    echo "\n";
}

echo "Password testing complete!\n";
echo "\nTry logging in with these credentials:\n";
echo "- John Doe: john@example.com / password\n";
echo "- MCMC Admin: admin@mcmc.gov.my / password\n";
echo "- MCMC Staff: staff@mcmc.gov.my / password\n";
