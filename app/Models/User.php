<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Cog\Contracts\Ban\Bannable as BannableContract;
use Cog\Laravel\Ban\Traits\Bannable;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Traits\HasRoles;

#[Fillable([
    'name',
    'email',
    'password',
    'national_id',
    'avatar_image',
    'mobile',
    'country',
    'gender',
    'is_approved',
    'approved_by_id',
    'approved_at',
    'created_by_id',
    'last_login_at',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements BannableContract
{
    /** @use HasFactory<UserFactory> */
    use Bannable, HasFactory, HasRoles, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_approved' => 'boolean',
            'approved_at' => 'datetime',
            'last_login_at' => 'datetime',
        ];
    }

    /**
     * Get the floors managed by the user.
     */
    public function floors(): HasMany
    {
        return $this->hasMany(Floor::class, 'manager_id');
    }

    /**
     * Get the rooms managed by the user.
     */
    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class, 'manager_id');
    }

    /**
     * Get the reservations made by the user.
     */
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class, 'user_id');
    }

    /**
     * Get the user who created this staff member.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    /**
     * Get the staff created by this manager.
     */
    public function createdStaff(): HasMany
    {
        return $this->hasMany(User::class, 'created_by_id');
    }

    /**
     * Get the user who approved this client.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_id');
    }

    /**
     * Get the clients approved by this user.
     */
    public function approvedClients(): HasMany
    {
        return $this->hasMany(User::class, 'approved_by_id');
    }

    /**
     * Accessor for avatar URL with fallback default.
     */
    protected function avatarUrl(): Attribute
    {
        return Attribute::make(
            get: function (?string $value, array $attributes): string {
                $image = $attributes['avatar_image'] ?? null;
                if ($image && Storage::disk('public')->exists($image)) {
                    return Storage::disk('public')->url($image);
                }

                $name = urlencode($attributes['name'] ?? 'User');

                return "https://ui-avatars.com/api/?name={$name}&background=6366f1&color=fff";
            }
        );
    }
}
