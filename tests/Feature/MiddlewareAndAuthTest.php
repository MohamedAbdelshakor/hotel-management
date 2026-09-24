<?php

use App\Models\User;
use Database\Seeders\RolesAndAdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RolesAndAdminSeeder::class);
});

test('banned user is logged out and redirected to login', function () {
    $user = User::factory()->create();
    $user->ban();
    $user->refresh();

    $response = $this->actingAs($user)->get('/dashboard');

    $response->assertRedirect('/login');
    $this->assertGuest();
});

test('unbanned user can access dashboard', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/dashboard');

    $response->assertOk();
});

test('role middleware restricts unauthorized users', function () {
    Route::get('/test-admin-only', fn () => 'admin content')
        ->middleware(['web', 'auth', 'role:admin']);

    $client = User::factory()->client()->create();
    $admin = User::factory()->admin()->create();

    // Client should be forbidden (403)
    $this->actingAs($client)->get('/test-admin-only')->assertForbidden();

    // Admin should succeed (200)
    $this->actingAs($admin)->get('/test-admin-only')->assertOk();
});

test('newly registered user gets client role and is pending approval', function () {
    $response = $this->post('/register', [
        'name' => 'John Client',
        'email' => 'client@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertRedirect('/dashboard');

    $user = User::where('email', 'client@example.com')->first();
    expect($user)->not->toBeNull()
        ->and($user->hasRole('client'))->toBeTrue()
        ->and($user->is_approved)->toBeFalse();
});

test('ensure client is approved middleware blocks unapproved client', function () {
    Route::get('/test-approved-only', fn () => 'approved content')
        ->middleware(['web', 'auth', 'approved']);

    $unapprovedClient = User::factory()->client()->unapproved()->create();
    $approvedClient = User::factory()->client()->create(['is_approved' => true]);

    $this->actingAs($unapprovedClient)->get('/test-approved-only')
        ->assertRedirect('/dashboard');

    $this->actingAs($approvedClient)->get('/test-approved-only')
        ->assertOk();
});
