<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItineraryItem extends Model
{
    protected $fillable = [
        'itinerary_id', 'day_number', 'item_type',
        'hotel_id', 'sightseeing_id', 'notes', 'order'
    ];

    public function itinerary()
    {
        return $this->belongsTo(Itinerary::class);
    }

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function sightseeing()
    {
        return $this->belongsTo(Sightseeing::class);
    }
}
