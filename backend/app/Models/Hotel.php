<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    protected $fillable = [
        'destination_id', 'city_id', 'partner_id', 'name', 
        'category', 'room_type', 'notes', 'is_active'
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function destination()
    {
        return $this->belongsTo(Destination::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function partner()
    {
        return $this->belongsTo(User::class, 'partner_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
