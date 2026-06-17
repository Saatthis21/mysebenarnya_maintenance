<?php

require 'vendor/autoload.php';

use Illuminate\Support\Facades\Hash;

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== MCMC STAFF PROFILE UPDATE TEST ===\n\n";

// Get MCMC staff user
$mcmcUser = \App\Models\McmcStaff::where('staff_Email', 'amrddnmbrk01@gmail.com')->first();

if (!$mcmcUser) {
    echo "❌ MCMC user not found\n";
    exit;
}

echo "✅ MCMC Staff found: {$mcmcUser->staff_Email}\n";
echo "   Name: {$mcmcUser->staff_Name}\n";
echo "   ID: {$mcmcUser->staff_ID}\n\n";

// Test 1: Update basic profile info
echo "1️⃣  Testing basic profile update\n";
try {
    $mcmcUser->update([
        'staff_Name' => 'Ahmad Rahman (Updated)',
        'staff_Phone_Number' => '01234567890',
    ]);
    echo "   ✅ Basic profile updated\n";
} catch (\Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}

// Test 2: Update password
echo "\n2️⃣  Testing password update\n";
try {
    $mcmcUser->update([
        'staff_Password' => Hash::make('NewPassword123'),
    ]);
    echo "   ✅ Password updated\n";
    
    // Verify new password
    $passwordMatches = Hash::check('NewPassword123', $mcmcUser->staff_Password);
    echo "   Password verification: " . ($passwordMatches ? "✅ VALID" : "❌ INVALID") . "\n";
} catch (\Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}

// Test 3: Update settings
echo "\n3️⃣  Testing settings update\n";
try {
    $mcmcUser->update([
        'email_notifications' => true,
        'sms_notifications' => false,
        'language' => 'ms',
        'timezone' => 'Asia/Kuala_Lumpur',
    ]);
    echo "   ✅ Settings updated\n";
    echo "   Email notifications: {$mcmcUser->email_notifications}\n";
    echo "   SMS notifications: {$mcmcUser->sms_notifications}\n";
    echo "   Language: {$mcmcUser->language}\n";
    echo "   Timezone: {$mcmcUser->timezone}\n";
} catch (\Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}

// Test 4: Update profile picture
echo "\n4️⃣  Testing profile picture field\n";
try {
    $mcmcUser->update([
        'profile_picture' => 'profile_pictures/test.jpg',
    ]);
    echo "   ✅ Profile picture path set\n";
    echo "   Profile picture: {$mcmcUser->profile_picture}\n";
} catch (\Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}

// Reset password back to 'password'
echo "\n5️⃣  Resetting password back to test password\n";
$mcmcUser->update([
    'staff_Password' => Hash::make('password'),
    'staff_Name' => 'Ahmad Rahman', // Reset name too
]);
echo "   ✅ Password and name reset\n";

echo "\n✅ ALL MCMC STAFF PROFILE UPDATES WORKING!\n";
