<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $fillable = ['destination_id', 'name', 'description', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function destination()
    {
        return $this->belongsTo(Destination::class);
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
