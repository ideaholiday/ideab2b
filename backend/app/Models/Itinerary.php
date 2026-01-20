<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Itinerary extends Model
{
    protected $fillable = [
        'agent_id', 'destination_id', 'city_id', 'client_name',
        'client_email', 'client_phone', 'start_date', 'end_date',
        'pax_count', 'total_price', 'status', 'operator_id'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'total_price' => 'decimal:2',
    ];

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function destination()
    {
        return $this->belongsTo(Destination::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function operator()
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    public function items()
    {
        return $this->hasMany(ItineraryItem::class)->orderBy('day_number')->orderBy('order');
    }
}
