<?php

use App\Models\User;
use Database\Seeders\RolesAndAdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

test('roles and default admin are seeded correctly', function () {
    $this->seed(RolesAndAdminSeeder::class);

    expect(Role::where('name', 'admin')->exists())->toBeTrue()
        ->and(Role::where('name', 'manager')->exists())->toBeTrue()
        ->and(Role::where('name', 'receptionist')->exists())->toBeTrue()
        ->and(Role::where('name', 'client')->exists())->toBeTrue();

    $admin = User::where('email', 'admin@admin.com')->first();
    expect($admin)->not->toBeNull()
        ->and($admin->hasRole('admin'))->toBeTrue();
});

test('create:admin command creates a new admin user with admin role', function () {
    $this->seed(RolesAndAdminSeeder::class);

    $this->artisan('create:admin', [
        '--name' => 'admin2@admin.com',
        '--password' => '123456',
    ])->assertSuccessful();

    $admin2 = User::where('email', 'admin2@admin.com')->first();
    expect($admin2)->not->toBeNull()
        ->and($admin2->hasRole('admin'))->toBeTrue();
});

test('create:admin command fails when email is invalid or password too short', function () {
    $this->seed(RolesAndAdminSeeder::class);

    $this->artisan('create:admin', [
        '--name' => 'John',
        '--email' => 'not-an-email',
        '--password' => '123',
    ])->assertFailed();
});
