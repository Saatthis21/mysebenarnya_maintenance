<?php

require 'vendor/autoload.php';

use Illuminate\Support\Facades\Hash;

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$user = App\Models\UserRecord::where('email', 'umpsa@gov.my')->first();
$agency = App\Models\Agency::where('agency_Email', 'umpsa@gov.my')->first();

if (!$user) {
    echo "User not found!\n";
    exit;
}

echo "User found: " . $user->email . "\n";
echo "User type: " . $user->user_type . "\n";
echo "Agency ID: " . $user->agency_ID . "\n";

// Reset password to a known value for testing
$testPassword = 'TestPassword123';
$hashedPassword = Hash::make($testPassword);

echo "Updating password to: $testPassword\n";

// Update both records
$user->password = $hashedPassword;
$user->save();

$agency->agency_Password = $hashedPassword;
$agency->save();

echo "Password updated successfully!\n";
echo "Try logging in with email: umpsa@gov.my and password: $testPassword\n";
