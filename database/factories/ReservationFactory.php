<?php

namespace Database\Factories;

use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Reservation>
 */
class ReservationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->client(),
            'room_id' => Room::factory(),
            'accompany_number' => fake()->numberBetween(0, 3),
            'paid_price' => fn (array $attributes) => Room::find($attributes['room_id'])?->price ?? 15000,
            'stripe_payment_id' => 'ch_'.fake()->regexify('[A-Za-z0-9]{24}'),
            'status' => 'paid',
        ];
    }
}
