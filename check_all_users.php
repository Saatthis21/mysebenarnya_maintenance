<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Checking all users in database...\n\n";

$users = App\Models\UserRecord::all();

if ($users->isEmpty()) {
    echo "No users found in database!\n";
    exit;
}

echo "Found " . $users->count() . " users:\n";
echo str_repeat("-", 80) . "\n";
printf("%-5s %-25s %-25s %-10s %-10s\n", "ID", "Name", "Email", "Type", "Agency ID");
echo str_repeat("-", 80) . "\n";

foreach ($users as $user) {
    printf("%-5s %-25s %-25s %-10s %-10s\n",
        $user->id,
        substr($user->name, 0, 24),
        substr($user->email, 0, 24),
        $user->user_type,
        $user->agency_ID ?? 'NULL'
    );
}

echo str_repeat("-", 80) . "\n";
echo "\nUser type distribution:\n";
$typeCount = $users->groupBy('user_type');
foreach ($typeCount as $type => $typeUsers) {
    echo "- $type: " . $typeUsers->count() . " users\n";
}
