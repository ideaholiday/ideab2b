<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use Illuminate\Http\Request;

class HotelController extends Controller
{
    public function index(Request $request)
    {
        $query = Hotel::with(['destination', 'city', 'partner']);

        if ($request->user()->role === 'agent') {
            $query->active();
        }

        return response()->json($query->get());
    }

    public function byCity($cityId)
    {
        return response()->json(
            Hotel::where('city_id', $cityId)->active()->with(['partner'])->get()
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'destination_id' => 'required|exists:destinations,id',
            'city_id' => 'required|exists:cities,id',
            'partner_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'category' => 'required|in:3star,4star,5star',
            'room_type' => 'nullable|string',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $hotel = Hotel::create($validated);

        return response()->json($hotel, 201);
    }

    public function update(Request $request, Hotel $hotel)
    {
        $validated = $request->validate([
            'name' => 'string|max:255',
            'category' => 'in:3star,4star,5star',
            'room_type' => 'nullable|string',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $hotel->update($validated);

        return response()->json($hotel);
    }

    public function destroy(Hotel $hotel)
    {
        $hotel->delete();

        return response()->json(['message' => 'Hotel deleted']);
    }
}
