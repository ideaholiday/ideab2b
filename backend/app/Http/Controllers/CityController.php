<?php

namespace App\Http\Controllers;

use App\Models\City;
use Illuminate\Http\Request;

class CityController extends Controller
{
    public function index(Request $request)
    {
        $query = City::with('destination');

        if ($request->user()->role === 'agent') {
            $query->active();
        }

        return response()->json($query->get());
    }

    public function byDestination($destinationId)
    {
        return response()->json(
            City::where('destination_id', $destinationId)->active()->get()
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'destination_id' => 'required|exists:destinations,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $city = City::create($validated);

        return response()->json($city, 201);
    }

    public function update(Request $request, City $city)
    {
        $validated = $request->validate([
            'name' => 'string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $city->update($validated);

        return response()->json($city);
    }

    public function destroy(City $city)
    {
        $city->delete();

        return response()->json(['message' => 'City deleted']);
    }
}
