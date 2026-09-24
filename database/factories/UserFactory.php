<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'national_id' => fake()->unique()->numerify('##############'),
            'avatar_image' => null,
            'mobile' => fake()->phoneNumber(),
            'country' => fake()->country(),
            'gender' => fake()->randomElement(['Male', 'Female']),
            'is_approved' => true,
            'approved_at' => now(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Indicate that the client is not yet approved.
     */
    public function unapproved(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_approved' => false,
            'approved_at' => null,
            'approved_by_id' => null,
        ]);
    }

    /**
     * Assign the admin role.
     */
    public function admin(): static
    {
        return $this->afterCreating(fn (User $user) => $user->assignRole('admin'));
    }

    /**
     * Assign the manager role.
     */
    public function manager(): static
    {
        return $this->afterCreating(fn (User $user) => $user->assignRole('manager'));
    }

    /**
     * Assign the receptionist role.
     */
    public function receptionist(): static
    {
        return $this->afterCreating(fn (User $user) => $user->assignRole('receptionist'));
    }

    /**
     * Assign the client role.
     */
    public function client(): static
    {
        return $this->afterCreating(fn (User $user) => $user->assignRole('client'));
    }
}
