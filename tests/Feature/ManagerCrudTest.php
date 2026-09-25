<?php

use App\Models\Floor;
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

test('only admin can access manage managers page', function () {
    $admin = User::factory()->admin()->create();
    $manager = User::factory()->manager()->create();
    $client = User::factory()->client()->create();

    // Guest redirected to login
    $this->get(route('admin.managers.index'))->assertRedirect(route('login'));

    // Client forbidden
    $this->actingAs($client)->get(route('admin.managers.index'))->assertForbidden();

    // Manager forbidden
    $this->actingAs($manager)->get(route('admin.managers.index'))->assertForbidden();

    // Admin allowed
    $this->actingAs($admin)->get(route('admin.managers.index'))->assertOk();
});

test('admin can view managers with search and pagination', function () {
    $admin = User::factory()->admin()->create();
    $manager1 = User::factory()->manager()->create(['name' => 'Alice Smith', 'email' => 'alice@hotel.com']);
    $manager2 = User::factory()->manager()->create(['name' => 'Bob Jones', 'email' => 'bob@hotel.com']);

    $response = $this->actingAs($admin)->get(route('admin.managers.index', ['search' => 'Alice']));

    $response->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Managers/Index')
            ->has('managers.data', 1)
            ->where('managers.data.0.name', 'Alice Smith')
        );
});

test('admin can create a manager with avatar', function () {
    $admin = User::factory()->admin()->create();
    $avatar = UploadedFile::fake()->create('avatar.jpg', 100, 'image/jpeg');

    $response = $this->actingAs($admin)->post(route('admin.managers.store'), [
        'name' => 'New Manager',
        'email' => 'newmanager@hotel.com',
        'password' => 'secret123',
        'national_id' => '12345678901234',
        'avatar_image' => $avatar,
    ]);

    $response->assertRedirect(route('admin.managers.index'))
        ->assertSessionHas('success');

    $created = User::where('email', 'newmanager@hotel.com')->first();
    expect($created)->not->toBeNull()
        ->and($created->hasRole('manager'))->toBeTrue()
        ->and($created->is_approved)->toBeTrue()
        ->and($created->avatar_image)->not->toBeNull();

    Storage::disk('public')->assertExists($created->avatar_image);
});

test('manager creation requires unique email, national id, and minimum 6 char password', function () {
    $admin = User::factory()->admin()->create();
    $existing = User::factory()->manager()->create([
        'email' => 'existing@hotel.com',
        'national_id' => '11112222333344',
    ]);

    $response = $this->actingAs($admin)->post(route('admin.managers.store'), [
        'name' => 'Duplicate',
        'email' => 'existing@hotel.com',
        'national_id' => '11112222333344',
        'password' => '123',
    ]);

    $response->assertSessionHasErrors(['email', 'national_id', 'password']);
});

test('admin can update manager details', function () {
    $admin = User::factory()->admin()->create();
    $manager = User::factory()->manager()->create([
        'name' => 'Old Name',
        'email' => 'old@hotel.com',
        'national_id' => '99998888777766',
    ]);

    $response = $this->actingAs($admin)->put(route('admin.managers.update', $manager->id), [
        'name' => 'Updated Name',
        'email' => 'updated@hotel.com',
        'national_id' => '99998888777766',
        'password' => '', // leave blank
    ]);

    $response->assertRedirect(route('admin.managers.index'))
        ->assertSessionHas('success');

    $manager->refresh();
    expect($manager->name)->toBe('Updated Name')
        ->and($manager->email)->toBe('updated@hotel.com');
});

test('admin can delete manager via ajax', function () {
    $admin = User::factory()->admin()->create();
    $manager = User::factory()->manager()->create();

    $response = $this->actingAs($admin)->deleteJson(route('admin.managers.destroy', $manager->id));

    $response->assertOk()
        ->assertJson(['success' => true]);

    expect(User::find($manager->id))->toBeNull();
});

test('cannot delete manager with associated floors', function () {
    $admin = User::factory()->admin()->create();
    $manager = User::factory()->manager()->create();
    Floor::factory()->create(['manager_id' => $manager->id]);

    $response = $this->actingAs($admin)->delete(route('admin.managers.destroy', $manager->id));

    $response->assertSessionHas('error');
    expect(User::find($manager->id))->not->toBeNull();
});
