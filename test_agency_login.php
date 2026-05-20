<?php

require 'vendor/autoload.php';

use Illuminate\Support\Facades\Hash;

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$email = 'umpsa@gov.my';
$password = 'TestPassword123';
$type = 'agency';

echo "Testing agency login process...\n";
echo "Email: $email\n";
echo "Password: $password\n";
echo "Type: $type\n\n";

// Step 1: Find user by email and type
$user = App\Models\UserRecord::where('email', $email)
    ->where('user_type', $type)
    ->first();

if (!$user) {
    echo "❌ User not found with email '$email' and type '$type'\n";

    // Let's check if user exists with different type
    $anyUser = App\Models\UserRecord::where('email', $email)->first();
    if ($anyUser) {
        echo "⚠️  User found but with type: " . $anyUser->user_type . "\n";
    } else {
        echo "❌ No user found with this email at all\n";
    }
    exit;
}

echo "✅ User found: " . $user->name . " (ID: " . $user->id . ")\n";
echo "   User type: " . $user->user_type . "\n";
echo "   Agency ID: " . $user->agency_ID . "\n";

// Step 2: Check password
$passwordMatches = Hash::check($password, $user->password);
echo "Password check: " . ($passwordMatches ? "✅ MATCHES" : "❌ DOES NOT MATCH") . "\n";

if (!$passwordMatches) {
    echo "❌ Authentication would fail at password check\n";
    exit;
}

echo "✅ Authentication should succeed!\n";
echo "\nTry logging in via the web interface now.\n";
