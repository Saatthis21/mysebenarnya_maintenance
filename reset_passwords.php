<?php

require 'vendor/autoload.php';

use Illuminate\Support\Facades\Hash;

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== RESETTING ALL PASSWORDS ===\n\n";

$testPassword = 'password';
$hashedPassword = Hash::make($testPassword);

// Reset MCMC Staff passwords
echo "1️⃣  MCMC Staff Accounts\n";
$mcmcStaff = \App\Models\McmcStaff::all();

foreach ($mcmcStaff as $staff) {
    $staff->staff_Password = $hashedPassword;
    $staff->save();
    echo "   ✅ {$staff->staff_Email}\n";
}

echo "\n";

// Reset Public User passwords
echo "2️⃣  Public User Accounts\n";
$publicUsers = \App\Models\UserRecord::where('user_type', 'public')->get();

foreach ($publicUsers as $user) {
    $user->password = $hashedPassword;
    $user->save();
    echo "   ✅ {$user->email}\n";
}

echo "\n";

// Reset Agency User passwords
echo "3️⃣  Agency User Accounts\n";
$agencyUsers = \App\Models\UserRecord::where('user_type', 'agency')->get();

foreach ($agencyUsers as $user) {
    $user->password = $hashedPassword;
    $user->save();
    echo "   ✅ {$user->email}\n";
}

echo "\n";

// Reset Admin (web) User passwords
echo "4️⃣  Admin (Web) Accounts\n";
$adminUsers = \App\Models\UserRecord::where('user_type', 'admin')->get();

if ($adminUsers->isEmpty()) {
    echo "   ⚠️  No admin users found\n";
} else {
    foreach ($adminUsers as $user) {
        $user->password = $hashedPassword;
        $user->save();
        echo "   ✅ {$user->email}\n";
    }
}

echo "\n\n";
echo "✅ ALL PASSWORDS RESET TO: '$testPassword'\n\n";
echo "You can now log in with:\n";
echo "   - MCMC Staff: ahmad.rahman@mcmc.gov.my / password\n";
echo "   - Public User: john@example.com / password\n";
echo "   - Agency User: admin@moh.gov.my / password\n";
