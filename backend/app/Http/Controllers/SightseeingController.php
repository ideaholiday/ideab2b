<?php

namespace App\Http\Controllers;

use App\Models\Sightseeing;
use Illuminate\Http\Request;

class SightseeingController extends Controller
{
    public function index(Request $request)
    {
        $query = Sightseeing::with(['destination', 'city']);

        if ($request->user()->role === 'agent') {
            $query->active();
        }

        return response()->json($query->get());
    }

    public function byCity($cityId)
    {
        return response()->json(
            Sightseeing::where('city_id', $cityId)->active()->get()
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'destination_id' => 'required|exists:destinations,id',
            'city_id' => 'required|exists:cities,id',
            'type' => 'required|in:sightseeing,transfer',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration' => 'nullable|string',
            'internal_cost' => 'required|numeric',
            'agent_price' => 'nullable|numeric',
            'is_active' => 'boolean',
        ]);

        $sightseeing = Sightseeing::create($validated);

        return response()->json($sightseeing, 201);
    }

    public function update(Request $request, Sightseeing $sightseeing)
    {
        $validated = $request->validate([
            'name' => 'string|max:255',
            'description' => 'nullable|string',
            'duration' => 'nullable|string',
            'internal_cost' => 'numeric',
            'agent_price' => 'nullable|numeric',
            'is_active' => 'boolean',
        ]);

        $sightseeing->update($validated);

        return response()->json($sightseeing);
    }

    public function destroy(Sightseeing $sightseeing)
    {
        $sightseeing->delete();

        return response()->json(['message' => 'Sightseeing deleted']);
    }
}
