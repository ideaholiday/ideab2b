<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Destination extends Model
{
    protected $fillable = ['name', 'country', 'description', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function cities()
    {
        return $this->hasMany(City::class);
    }

    public function sightseeings()
    {
        return $this->hasMany(Sightseeing::class);
    }

    public function hotels()
    {
        return $this->hasMany(Hotel::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
