<?php

namespace App\Models;

use Database\Factories\RoomFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['number', 'capacity', 'price', 'floor_id', 'manager_id'])]
class Room extends Model
{
    /** @use HasFactory<RoomFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'capacity' => 'integer',
            'price' => 'integer', // in cents
        ];
    }

    /**
     * Price accessor in dollars.
     */
    protected function priceInDollars(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => number_format(($attributes['price'] ?? 0) / 100, 2, '.', '')
        );
    }

    /**
     * Get the floor this room belongs to.
     */
    public function floor(): BelongsTo
    {
        return $this->belongsTo(Floor::class);
    }

    /**
     * Get the manager who created this room.
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    /**
     * Get reservations for this room.
     */
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    /**
     * Check if room is currently reserved.
     */
    public function isReserved(): bool
    {
        return $this->reservations()->whereIn('status', ['paid', 'confirmed', 'active'])->exists();
    }

    /**
     * Scope query to only available (unreserved) rooms.
     */
    #[Scope]
    protected function available(Builder $query): Builder
    {
        return $query->whereDoesntHave('reservations', function (Builder $q) {
            $q->whereIn('status', ['paid', 'confirmed', 'active']);
        });
    }
}
