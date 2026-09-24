<?php

namespace App\Models;

use Database\Factories\ReservationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'room_id', 'accompany_number', 'paid_price', 'stripe_payment_id', 'status'])]
class Reservation extends Model
{
    /** @use HasFactory<ReservationFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'accompany_number' => 'integer',
            'paid_price' => 'integer', // in cents
        ];
    }

    /**
     * Paid price accessor in dollars.
     */
    protected function paidPriceInDollars(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => number_format(($attributes['paid_price'] ?? 0) / 100, 2, '.', '')
        );
    }


    /**
     * Get the client (user) who made this reservation.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the room reserved.
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }
}
