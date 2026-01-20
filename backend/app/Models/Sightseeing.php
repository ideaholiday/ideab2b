<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sightseeing extends Model
{
    protected $fillable = [
        'destination_id', 'city_id', 'type', 'name', 
        'description', 'duration', 'internal_cost', 
        'agent_price', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'internal_cost' => 'decimal:2',
        'agent_price' => 'decimal:2',
    ];

    public function destination()
    {
        return $this->belongsTo(Destination::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }
}
