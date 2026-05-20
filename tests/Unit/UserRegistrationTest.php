<?php

use App\Models\UserRecord;
use Illuminate\Support\Facades\Hash;

test('can create public user', function () {
    $userData = [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => Hash::make('password'),
        'contact_number' => '+60123456789',
    ];

    $user = UserRecord::create(array_merge($userData, ['user_type' => 'public']));

    expect($user)->toBeInstanceOf(UserRecord::class);
    expect($user->user_type)->toBe('public');
    expect($user->name)->toBe('John Doe');
});

test('can create agency user with temporary password', function () {
    $userData = [
        'name' => 'Ministry of Health',
        'email' => 'admin@moh.gov.my',
        'contact_number' => '+60323456789',
    ];

    $user = UserRecord::create(array_merge($userData, [
        'user_type' => 'agency',
        'temporary_password' => Hash::make('temp123'),
        'force_password_reset' => true
    ]));

    expect($user)->toBeInstanceOf(UserRecord::class);
    expect($user->user_type)->toBe('agency');
    expect($user->force_password_reset)->toBeTrue();
    expect($user->temporary_password)->not->toBeNull();
});

test('can create mcmc user', function () {
    $userData = [
        'name' => 'MCMC Admin',
        'email' => 'admin@mcmc.gov.my',
        'password' => Hash::make('password'),
        'contact_number' => '+60312345678',
    ];

    $user = UserRecord::create(array_merge($userData, ['user_type' => 'mcmc']));

    expect($user)->toBeInstanceOf(UserRecord::class);
    expect($user->user_type)->toBe('mcmc');
});

test('query scopes work correctly', function () {
    UserRecord::factory()->create(['user_type' => 'public']);
    UserRecord::factory()->create(['user_type' => 'mcmc']);
    UserRecord::factory()->create(['user_type' => 'agency']);

    expect(UserRecord::where('user_type', 'public')->count())->toBe(1);
    expect(UserRecord::where('user_type', 'mcmc')->count())->toBe(1);
    expect(UserRecord::where('user_type', 'agency')->count())->toBe(1);
});
