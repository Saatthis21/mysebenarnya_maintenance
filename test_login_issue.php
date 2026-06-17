<?php

require 'vendor/autoload.php';

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== LOGIN DIAGNOSIS ===\n\n";

// Test 1: MCMC Staff Login
echo "1️⃣  Testing MCMC Staff Login\n";
echo "---\n";

$mcmcEmail = 'ahmad.rahman@mcmc.gov.my';
$testPassword = 'password';

$mcmcUser = \App\Models\McmcStaff::where('staff_Email', $mcmcEmail)->first();

if (!$mcmcUser) {
    echo "❌ MCMC user not found\n\n";
} else {
    echo "✅ MCMC User found: {$mcmcUser->staff_Email}\n";
    
    // Test password
    $passwordMatches = Hash::check($testPassword, $mcmcUser->staff_Password);
    echo "   Password check ('$testPassword'): " . ($passwordMatches ? "✅ MATCHES" : "❌ DOES NOT MATCH") . "\n";
    
    // Test auth attempt
    $result = Auth::guard('mcmc')->attempt([
        'staff_Email' => $mcmcEmail,
        'password' => $testPassword
    ]);
    echo "   Auth::guard('mcmc')->attempt(): " . ($result ? "✅ SUCCESS" : "❌ FAILED") . "\n";
    
    if ($result) {
        Auth::guard('mcmc')->logout();
    }
}

echo "\n\n";

// Test 2: Public User Login
echo "2️⃣  Testing Public User Login\n";
echo "---\n";

$publicEmail = 'john@example.com';
$publicUser = \App\Models\UserRecord::where('email', $publicEmail)
    ->where('user_type', 'public')
    ->first();

if (!$publicUser) {
    echo "❌ Public user not found\n\n";
} else {
    echo "✅ Public User found: {$publicUser->email}\n";
    
    // Test password
    $passwordMatches = Hash::check($testPassword, $publicUser->password);
    echo "   Password check ('$testPassword'): " . ($passwordMatches ? "✅ MATCHES" : "❌ DOES NOT MATCH") . "\n";
    
    // Test auth attempt
    $result = Auth::guard('public')->attempt([
        'email' => $publicEmail,
        'password' => $testPassword
    ]);
    echo "   Auth::guard('public')->attempt(): " . ($result ? "✅ SUCCESS" : "❌ FAILED") . "\n";
    
    if ($result) {
        Auth::guard('public')->logout();
    }
}

echo "\n\n";

// Test 3: Agency User Login
echo "3️⃣  Testing Agency User Login\n";
echo "---\n";

$agencyUsers = \App\Models\UserRecord::where('user_type', 'agency')->get();

if ($agencyUsers->isEmpty()) {
    echo "❌ No agency users found in database\n\n";
} else {
    $agencyUser = $agencyUsers->first();
    echo "✅ Agency User found: {$agencyUser->email}\n";
    
    // Test password
    $passwordMatches = Hash::check($testPassword, $agencyUser->password);
    echo "   Password check ('$testPassword'): " . ($passwordMatches ? "✅ MATCHES" : "❌ DOES NOT MATCH") . "\n";
    
    // Test auth attempt
    $result = Auth::guard('agency')->attempt([
        'email' => $agencyUser->email,
        'password' => $testPassword
    ]);
    echo "   Auth::guard('agency')->attempt(): " . ($result ? "✅ SUCCESS" : "❌ FAILED") . "\n";
    
    if ($result) {
        Auth::guard('agency')->logout();
    }
}

echo "\n\n=== SUMMARY ===\n";
echo "If all tests show ❌ for 'DOES NOT MATCH', the issue is that:\n";
echo "Passwords are not hashed with 'password' value.\n\n";
echo "To reset all test accounts to password='password', run:\n";
echo "php reset_passwords.php\n";
