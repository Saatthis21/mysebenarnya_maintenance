<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$user = \App\Models\McmcStaff::find(1);
echo "Database Verification:\n";
echo "Name: " . $user->staff_Name . "\n";
echo "Email: " . $user->staff_Email . "\n";
echo "Phone: " . $user->staff_Phone_Number . "\n";
echo "Email Notifications: " . ($user->email_notifications ? 'Yes' : 'No') . "\n";
echo "Language: " . $user->language . "\n";
echo "Profile Picture: " . ($user->profile_picture ?? 'Not set') . "\n";
