<?php

use App\Models\Floor;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Database\Seeders\RolesAndAdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RolesAndAdminSeeder::class);
});

test('user model has required relationships and fallback avatar url', function () {
    $manager = User::factory()->manager()->create();
    $receptionist = User::factory()->receptionist()->create([
        'created_by_id' => $manager->id,
    ]);
    $client = User::factory()->client()->unapproved()->create();

    expect($receptionist->creator->id)->toBe($manager->id)
        ->and($manager->createdStaff)->toHaveCount(1)
        ->and($client->avatar_url)->toContain('ui-avatars.com');

    // Approve client
    $client->update([
        'is_approved' => true,
        'approved_by_id' => $receptionist->id,
        'approved_at' => now(),
    ]);

    expect($client->approver->id)->toBe($receptionist->id)
        ->and($receptionist->approvedClients)->toHaveCount(1);
});

test('floor model auto generates a unique 4 digit number and belongs to a manager', function () {
    $manager = User::factory()->manager()->create();

    $floor = Floor::create([
        'name' => 'Floor 1',
        'manager_id' => $manager->id,
    ]);

    expect($floor->number)->not->toBeEmpty()
        ->and(strlen($floor->number))->toBeGreaterThanOrEqual(4)
        ->and($floor->manager->id)->toBe($manager->id);
});

test('room model formats price in dollars and checks reservations', function () {
    $manager = User::factory()->manager()->create();
    $floor = Floor::factory()->create(['manager_id' => $manager->id]);

    $room = Room::factory()->create([
        'floor_id' => $floor->id,
        'manager_id' => $manager->id,
        'price' => 15000, // $150.00
    ]);

    expect($room->price_in_dollars)->toBe('150.00')
        ->and($room->floor->id)->toBe($floor->id)
        ->and($room->manager->id)->toBe($manager->id)
        ->and($room->isReserved())->toBeFalse();

    // Available scope contains room
    expect(Room::available()->where('id', $room->id)->exists())->toBeTrue();

    // Create reservation for room
    $reservation = Reservation::factory()->create([
        'room_id' => $room->id,
        'paid_price' => 15000,
        'status' => 'paid',
    ]);

    $room->refresh();
    expect($room->isReserved())->toBeTrue()
        ->and(Room::available()->where('id', $room->id)->exists())->toBeFalse()
        ->and($reservation->paid_price_in_dollars)->toBe('150.00')
        ->and($reservation->room->id)->toBe($room->id);
});
