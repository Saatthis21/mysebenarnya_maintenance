<?php

use App\Models\UserRecord;

test('user can view login page', function () {
    $response = $this->get('/login');
    $response->assertStatus(200);
});

test('user can login with valid credentials', function () {
    $user = UserRecord::factory()->create([
        'user_type' => 'public',
        'password' => bcrypt('password')
    ]);

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
        'type' => 'public'
    ]);

    $response->assertRedirect('/public/dashboard');
    $this->assertAuthenticatedAs($user, 'public');
});

test('user cannot login with invalid credentials', function () {
    $response = $this->post('/login', [
        'email' => 'test@example.com',
        'password' => 'wrongpassword',
        'type' => 'public'
    ]);

    $response->assertSessionHasErrors(['email']);
    $this->assertGuest();
});

test('user can register', function () {
    $response = $this->post('/register', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'contact_number' => '+60123456789',
        'password' => 'password123',
        'password_confirmation' => 'password123'
    ]);

    $response->assertRedirect('/email/verify');
    $this->assertDatabaseHas('users', [
        'email' => 'john@example.com',
        'user_type' => 'public'
    ]);
});

test('mcmc staff can access dashboard', function () {
    $user = UserRecord::factory()->create([
        'user_type' => 'mcmc'
    ]);

    $response = $this->actingAs($user, 'mcmc')->get('/mcmc/dashboard');
    $response->assertStatus(200);
});

test('agency user needs password reset on first login', function () {
    $user = UserRecord::factory()->create([
        'user_type' => 'agency',
        'force_password_reset' => true,
        'temporary_password' => bcrypt('temp123')
    ]);

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'temp123',
        'type' => 'agency'
    ]);

    $response->assertRedirect('/agency/password/reset');
});
