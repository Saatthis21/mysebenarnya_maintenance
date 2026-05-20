<?php

require 'vendor/autoload.php';

use Illuminate\Support\Facades\Auth;

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$credentials = [
    'email' => 'umpsa@gov.my',
    'password' => 'TestPassword123'
];

echo "Testing Auth::attempt for agency guard...\n";
echo "Credentials: " . json_encode($credentials) . "\n\n";

// Test with agency guard
$result = Auth::guard('agency')->attempt($credentials);
echo "Auth::guard('agency')->attempt(): " . ($result ? "✅ SUCCESS" : "❌ FAILED") . "\n";

if ($result) {
    $user = Auth::guard('agency')->user();
    echo "Authenticated user: " . $user->name . " (" . $user->user_type . ")\n";
} else {
    echo "Authentication failed. Let's check if credentials work manually...\n";

    // Check the user manually
    $user = App\Models\UserRecord::where('email', $credentials['email'])->first();
    if ($user) {
        echo "User found: " . $user->name . "\n";
        echo "User type: " . $user->user_type . "\n";
        $passwordCheck = password_verify($credentials['password'], $user->password);
        echo "Password check: " . ($passwordCheck ? "✅ MATCHES" : "❌ DOES NOT MATCH") . "\n";
    } else {
        echo "User not found in database\n";
    }
}

// Also test with default guard
echo "\nTesting with default guard...\n";
$defaultResult = Auth::attempt($credentials);
echo "Auth::attempt() (default guard): " . ($defaultResult ? "✅ SUCCESS" : "❌ FAILED") . "\n";
