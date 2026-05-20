<?php

use App\Models\UserRecord;

test('user login model can be created', function () {
    $user = UserRecord::factory()->create([
        'user_type' => 'public'
    ]);

    expect($user)->toBeInstanceOf(UserRecord::class);
    expect($user->user_type)->toBe('public');
});

test('get guard name returns correct guard', function () {
    $publicUser = UserRecord::factory()->create(['user_type' => 'public']);
    $mcmcUser = UserRecord::factory()->create(['user_type' => 'mcmc']);
    $agencyUser = UserRecord::factory()->create(['user_type' => 'agency']);

    expect($publicUser->getGuardName())->toBe('public');
    expect($mcmcUser->getGuardName())->toBe('mcmc');
    expect($agencyUser->getGuardName())->toBe('agency');
});

test('user type checks work correctly', function () {
    $publicUser = UserRecord::factory()->create(['user_type' => 'public']);
    $mcmcUser = UserRecord::factory()->create(['user_type' => 'mcmc']);
    $agencyUser = UserRecord::factory()->create(['user_type' => 'agency']);

    expect($publicUser->isPublicUser())->toBeTrue();
    expect($publicUser->isMcmcStaff())->toBeFalse();
    expect($publicUser->isAgency())->toBeFalse();

    expect($mcmcUser->isMcmcStaff())->toBeTrue();
    expect($mcmcUser->isPublicUser())->toBeFalse();
    expect($mcmcUser->isAgency())->toBeFalse();

    expect($agencyUser->isAgency())->toBeTrue();
    expect($agencyUser->isPublicUser())->toBeFalse();
    expect($agencyUser->isMcmcStaff())->toBeFalse();
});

test('login redirect routes work correctly', function () {
    $publicUser = UserRecord::factory()->create(['user_type' => 'public']);
    $mcmcUser = UserRecord::factory()->create(['user_type' => 'mcmc']);
    $agencyUser = UserRecord::factory()->create(['user_type' => 'agency']);

    expect($publicUser->getLoginRedirectRoute())->toBe('public.dashboard');
    expect($mcmcUser->getLoginRedirectRoute())->toBe('mcmc.dashboard');
    expect($agencyUser->getLoginRedirectRoute())->toBe('agency.dashboard');
});
