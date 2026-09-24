<?php

namespace App\Models;

use Database\Factories\FloorFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'number', 'manager_id'])]
class Floor extends Model
{
    /** @use HasFactory<FloorFactory> */
    use HasFactory;

    protected static function booted(): void
    {
        static::creating(function (Floor $floor) {
            if (empty($floor->number)) {
                $floor->number = static::generateFloorNumber();
            }
        });
    }

    /**
     * Generate an auto-generated unique floor number of at least 4 digits.
     */
    public static function generateFloorNumber(): string
    {
        do {
            $number = (string) random_int(1000, 9999);
        } while (static::where('number', $number)->exists());

        return $number;
    }

    /**
     * Get the manager who created the floor.
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    /**
     * Get the rooms on this floor.
     */
    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }
}
