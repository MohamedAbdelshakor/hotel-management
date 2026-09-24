<?php

namespace Database\Factories;

use App\Models\Floor;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Room>
 */
class RoomFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'number' => fake()->unique()->numerify('####'),
            'capacity' => fake()->numberBetween(1, 5),
            'price' => fake()->numberBetween(5000, 50000), // in cents ($50 - $500)
            'floor_id' => Floor::factory(),
            'manager_id' => fn (array $attributes) => Floor::find($attributes['floor_id'])?->manager_id ?? User::factory()->manager(),
        ];
    }
}
