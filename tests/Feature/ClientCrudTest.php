<?php

use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Database\Seeders\RolesAndAdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RolesAndAdminSeeder::class);
    Storage::fake('public');
});

test('only admin and manager can access clients index', function () {
    $admin = User::factory()->admin()->create();
    $manager = User::factory()->manager()->create();
    $receptionist = User::factory()->receptionist()->create();
    $client = User::factory()->client()->create();

    // Guest redirected
    $this->get(route('clients.index'))->assertRedirect(route('login'));

    // Client forbidden
    $this->actingAs($client)->get(route('clients.index'))->assertForbidden();

    // Receptionist forbidden
    $this->actingAs($receptionist)->get(route('clients.index'))->assertForbidden();

    // Manager allowed
    $this->actingAs($manager)->get(route('clients.index'))->assertOk();

    // Admin allowed
    $this->actingAs($admin)->get(route('clients.index'))->assertOk();
});

test('admin and manager can search clients', function () {
    $admin = User::factory()->admin()->create();
    User::factory()->client()->create([
        'name' => 'Michael Corleone',
        'email' => 'michael@example.com',
        'country' => 'Italy',
    ]);
    User::factory()->client()->create([
        'name' => 'Tom Hagen',
        'email' => 'tom@example.com',
        'country' => 'USA',
    ]);

    $response = $this->actingAs($admin)->get(route('clients.index', ['search' => 'Corleone']));

    $response->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Clients/Index')
            ->has('clients.data', 1)
            ->where('clients.data.0.name', 'Michael Corleone')
        );
});

test('manager can create a client with full profile and avatar', function () {
    $manager = User::factory()->manager()->create();
    $avatar = UploadedFile::fake()->create('client_avatar.jpg', 100, 'image/jpeg');

    $response = $this->actingAs($manager)->post(route('clients.store'), [
        'name' => 'Jane Client',
        'email' => 'jane@example.com',
        'password' => 'secret123',
        'mobile' => '+1234567890',
        'country' => 'Canada',
        'gender' => 'Female',
        'national_id' => '98765432101234',
        'avatar_image' => $avatar,
    ]);

    $response->assertRedirect(route('clients.index'))
        ->assertSessionHas('success');

    $created = User::where('email', 'jane@example.com')->first();
    expect($created)->not->toBeNull()
        ->and($created->hasRole('client'))->toBeTrue()
        ->and($created->is_approved)->toBeTrue()
        ->and($created->country)->toBe('Canada')
        ->and($created->gender)->toBe('Female')
        ->and($created->avatar_image)->not->toBeNull();

    Storage::disk('public')->assertExists($created->avatar_image);
});

test('client creation requires valid email and password', function () {
    $manager = User::factory()->manager()->create();
    $existing = User::factory()->client()->create([
        'email' => 'existing_client@example.com',
    ]);

    $response = $this->actingAs($manager)->post(route('clients.store'), [
        'name' => 'Jane',
        'email' => 'existing_client@example.com',
        'password' => '123', // min 6
    ]);

    $response->assertSessionHasErrors(['email', 'password']);
});

test('manager can update client details', function () {
    $manager = User::factory()->manager()->create();
    $client = User::factory()->client()->create([
        'name' => 'Original Name',
        'country' => 'France',
    ]);

    $response = $this->actingAs($manager)->put(route('clients.update', $client->id), [
        'name' => 'Updated Client Name',
        'email' => $client->email,
        'country' => 'Germany',
        'gender' => 'Male',
        'mobile' => '+4912345678',
        'national_id' => $client->national_id,
        'password' => '',
    ]);

    $response->assertRedirect(route('clients.index'))
        ->assertSessionHas('success');

    $client->refresh();
    expect($client->name)->toBe('Updated Client Name')
        ->and($client->country)->toBe('Germany');
});

test('manager can delete client via ajax', function () {
    $manager = User::factory()->manager()->create();
    $client = User::factory()->client()->create();

    $response = $this->actingAs($manager)->deleteJson(route('clients.destroy', $client->id));

    $response->assertOk()
        ->assertJson(['success' => true]);

    expect(User::find($client->id))->toBeNull();
});

test('cannot delete client with existing reservations', function () {
    $admin = User::factory()->admin()->create();
    $client = User::factory()->client()->create();
    $room = Room::factory()->create();

    Reservation::factory()->create([
        'user_id' => $client->id,
        'room_id' => $room->id,
    ]);

    $response = $this->actingAs($admin)->delete(route('clients.destroy', $client->id));

    $response->assertSessionHas('error');
    expect(User::find($client->id))->not->toBeNull();
});
