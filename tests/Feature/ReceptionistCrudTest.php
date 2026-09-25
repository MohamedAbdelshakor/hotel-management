<?php

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

test('only admin and manager can access receptionists index', function () {
    $admin = User::factory()->admin()->create();
    $manager = User::factory()->manager()->create();
    $receptionist = User::factory()->receptionist()->create();
    $client = User::factory()->client()->create();

    // Guest redirected
    $this->get(route('receptionists.index'))->assertRedirect(route('login'));

    // Client forbidden
    $this->actingAs($client)->get(route('receptionists.index'))->assertForbidden();

    // Receptionist forbidden
    $this->actingAs($receptionist)->get(route('receptionists.index'))->assertForbidden();

    // Manager allowed
    $this->actingAs($manager)->get(route('receptionists.index'))->assertOk();

    // Admin allowed
    $this->actingAs($admin)->get(route('receptionists.index'))->assertOk();
});

test('admin and manager can search receptionists and admin receives manager_name', function () {
    $admin = User::factory()->admin()->create();
    $manager = User::factory()->manager()->create(['name' => 'Manager Dave']);
    $rec1 = User::factory()->receptionist()->create([
        'name' => 'Emma Watson',
        'email' => 'emma@hotel.com',
        'created_by_id' => $manager->id,
    ]);
    $rec2 = User::factory()->receptionist()->create([
        'name' => 'John Bradley',
        'email' => 'john@hotel.com',
    ]);

    $response = $this->actingAs($admin)->get(route('receptionists.index', ['search' => 'Emma']));

    $response->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Receptionists/Index')
            ->has('receptionists.data', 1)
            ->where('receptionists.data.0.name', 'Emma Watson')
            ->where('receptionists.data.0.manager_name', 'Manager Dave')
        );
});

test('manager can create receptionist and created_by_id is set to manager', function () {
    $manager = User::factory()->manager()->create();
    $avatar = UploadedFile::fake()->create('avatar.jpg', 100, 'image/jpeg');

    $response = $this->actingAs($manager)->post(route('receptionists.store'), [
        'name' => 'New Receptionist',
        'email' => 'rec@hotel.com',
        'password' => 'secret123',
        'national_id' => '12345678901234',
        'avatar_image' => $avatar,
    ]);

    $response->assertRedirect(route('receptionists.index'))
        ->assertSessionHas('success');

    $created = User::where('email', 'rec@hotel.com')->first();
    expect($created)->not->toBeNull()
        ->and($created->hasRole('receptionist'))->toBeTrue()
        ->and($created->created_by_id)->toBe($manager->id)
        ->and($created->is_approved)->toBeTrue()
        ->and($created->avatar_image)->not->toBeNull();

    Storage::disk('public')->assertExists($created->avatar_image);
});

test('admin can create receptionist and created_by_id is set to admin', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->post(route('receptionists.store'), [
        'name' => 'Admin Created Receptionist',
        'email' => 'admin_rec@hotel.com',
        'password' => 'secret123',
        'national_id' => '99887766554433',
    ]);

    $response->assertRedirect(route('receptionists.index'))
        ->assertSessionHas('success');

    $created = User::where('email', 'admin_rec@hotel.com')->first();
    expect($created)->not->toBeNull()
        ->and($created->created_by_id)->toBe($admin->id);
});

test('receptionist creation requires valid and unique fields', function () {
    $manager = User::factory()->manager()->create();
    $existing = User::factory()->receptionist()->create([
        'email' => 'existing_rec@hotel.com',
        'national_id' => '11122233344455',
    ]);

    $response = $this->actingAs($manager)->post(route('receptionists.store'), [
        'name' => 'Duplicate',
        'email' => 'existing_rec@hotel.com',
        'national_id' => '11122233344455',
        'password' => '123', // min 6 required
    ]);

    $response->assertSessionHasErrors(['email', 'national_id', 'password']);
});

test('manager can update receptionist they created', function () {
    $manager = User::factory()->manager()->create();
    $receptionist = User::factory()->receptionist()->create([
        'name' => 'Old Name',
        'created_by_id' => $manager->id,
    ]);

    $response = $this->actingAs($manager)->put(route('receptionists.update', $receptionist->id), [
        'name' => 'Updated Name',
        'email' => $receptionist->email,
        'national_id' => $receptionist->national_id,
        'password' => '',
    ]);

    $response->assertRedirect(route('receptionists.index'))
        ->assertSessionHas('success');

    $receptionist->refresh();
    expect($receptionist->name)->toBe('Updated Name');
});

test('manager cannot update receptionist created by another manager', function () {
    $manager1 = User::factory()->manager()->create();
    $manager2 = User::factory()->manager()->create();
    $receptionist = User::factory()->receptionist()->create([
        'created_by_id' => $manager1->id,
    ]);

    $response = $this->actingAs($manager2)->put(route('receptionists.update', $receptionist->id), [
        'name' => 'Hacked Name',
        'email' => $receptionist->email,
        'national_id' => $receptionist->national_id,
    ]);

    $response->assertForbidden();
});

test('admin can update any receptionist', function () {
    $admin = User::factory()->admin()->create();
    $manager = User::factory()->manager()->create();
    $receptionist = User::factory()->receptionist()->create([
        'name' => 'Old Name',
        'created_by_id' => $manager->id,
    ]);

    $response = $this->actingAs($admin)->put(route('receptionists.update', $receptionist->id), [
        'name' => 'Admin Overridden Name',
        'email' => $receptionist->email,
        'national_id' => $receptionist->national_id,
    ]);

    $response->assertRedirect(route('receptionists.index'))
        ->assertSessionHas('success');

    $receptionist->refresh();
    expect($receptionist->name)->toBe('Admin Overridden Name');
});

test('manager can delete receptionist they created', function () {
    $manager = User::factory()->manager()->create();
    $receptionist = User::factory()->receptionist()->create([
        'created_by_id' => $manager->id,
    ]);

    $response = $this->actingAs($manager)->deleteJson(route('receptionists.destroy', $receptionist->id));

    $response->assertOk()
        ->assertJson(['success' => true]);

    expect(User::find($receptionist->id))->toBeNull();
});

test('manager cannot delete receptionist created by another manager', function () {
    $manager1 = User::factory()->manager()->create();
    $manager2 = User::factory()->manager()->create();
    $receptionist = User::factory()->receptionist()->create([
        'created_by_id' => $manager1->id,
    ]);

    $response = $this->actingAs($manager2)->delete(route('receptionists.destroy', $receptionist->id));

    $response->assertForbidden();
    expect(User::find($receptionist->id))->not->toBeNull();
});

test('cannot delete receptionist who has approved clients', function () {
    $admin = User::factory()->admin()->create();
    $receptionist = User::factory()->receptionist()->create();
    $client = User::factory()->client()->create([
        'approved_by_id' => $receptionist->id,
    ]);

    $response = $this->actingAs($admin)->delete(route('receptionists.destroy', $receptionist->id));

    $response->assertSessionHas('error');
    expect(User::find($receptionist->id))->not->toBeNull();
});

test('manager can ban and unban receptionist they created', function () {
    $manager = User::factory()->manager()->create();
    $receptionist = User::factory()->receptionist()->create([
        'created_by_id' => $manager->id,
    ]);

    expect($receptionist->isBanned())->toBeFalse();

    // Ban
    $banResponse = $this->actingAs($manager)->post(route('receptionists.ban', $receptionist->id));
    $banResponse->assertRedirect()->assertSessionHas('success');
    expect($receptionist->fresh()->isBanned())->toBeTrue();

    // Unban
    $unbanResponse = $this->actingAs($manager)->post(route('receptionists.unban', $receptionist->id));
    $unbanResponse->assertRedirect()->assertSessionHas('success');
    expect($receptionist->fresh()->isBanned())->toBeFalse();
});

test('manager cannot ban receptionist created by another manager', function () {
    $manager1 = User::factory()->manager()->create();
    $manager2 = User::factory()->manager()->create();
    $receptionist = User::factory()->receptionist()->create([
        'created_by_id' => $manager1->id,
    ]);

    $response = $this->actingAs($manager2)->post(route('receptionists.ban', $receptionist->id));

    $response->assertForbidden();
    expect($receptionist->fresh()->isBanned())->toBeFalse();
});

test('admin can ban and unban any receptionist', function () {
    $admin = User::factory()->admin()->create();
    $manager = User::factory()->manager()->create();
    $receptionist = User::factory()->receptionist()->create([
        'created_by_id' => $manager->id,
    ]);

    $this->actingAs($admin)->post(route('receptionists.ban', $receptionist->id))
        ->assertRedirect()
        ->assertSessionHas('success');

    expect($receptionist->fresh()->isBanned())->toBeTrue();

    $this->actingAs($admin)->post(route('receptionists.unban', $receptionist->id))
        ->assertRedirect()
        ->assertSessionHas('success');

    expect($receptionist->fresh()->isBanned())->toBeFalse();
});
