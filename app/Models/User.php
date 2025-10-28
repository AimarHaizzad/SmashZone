<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'position',
        'profile_picture',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

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
        ];
    }

    /**
     * Get the payments for the user.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get the bookings for the user.
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get the courts owned by the user (if owner).
     */
    public function courts()
    {
        return $this->hasMany(Court::class, 'owner_id');
    }

    /**
     * Get the web notifications for the user.
     */
    public function webNotifications()
    {
        return $this->hasMany(WebNotification::class);
    }

    /**
     * Get unread web notifications for the user.
     */
    public function unreadWebNotifications()
    {
        return $this->webNotifications()->unread();
    }

    /**
     * Get unread web notifications count for the user.
     */
    public function unreadWebNotificationsCount()
    {
        return $this->unreadWebNotifications()->count();
    }

    /**
     * Check if user is owner.
     */
    public function isOwner()
    {
        return $this->role === 'owner';
    }

    /**
     * Check if user is staff.
     */
    public function isStaff()
    {
        return $this->role === 'staff';
    }

    /**
     * Check if user is customer.
     */
    public function isCustomer()
    {
        return $this->role === 'customer';
    }
}
