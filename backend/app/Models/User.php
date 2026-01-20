<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'is_active', 
        'company_name', 'phone'
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'is_active' => 'boolean',
        'email_verified_at' => 'datetime',
    ];

    public function hotels()
    {
        return $this->hasMany(Hotel::class, 'partner_id');
    }

    public function itineraries()
    {
        return $this->hasMany(Itinerary::class, 'agent_id');
    }

    public function assignedBookings()
    {
        return $this->hasMany(Itinerary::class, 'operator_id');
    }
}
