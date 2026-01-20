<?php

namespace App\Http\Controllers;

use App\Models\Itinerary;
use App\Models\ItineraryItem;
use Illuminate\Http\Request;

class ItineraryController extends Controller
{
    public function index(Request $request)
    {
        $query = Itinerary::with(['destination', 'city', 'agent', 'operator']);

        // Filter based on user role
        if ($request->user()->role === 'agent') {
            $query->where('agent_id', $request->user()->id);
        } elseif ($request->user()->role === 'operator') {
            $query->where('operator_id', $request->user()->id);
        }

        return response()->json($query->latest()->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'destination_id' => 'required|exists:destinations,id',
            'city_id' => 'required|exists:cities,id',
            'client_name' => 'required|string|max:255',
            'client_email' => 'nullable|email',
            'client_phone' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'pax_count' => 'required|integer|min:1',
        ]);

        $validated['agent_id'] = $request->user()->id;

        $itinerary = Itinerary::create($validated);

        return response()->json($itinerary->load(['destination', 'city']), 201);
    }

    public function show(Itinerary $itinerary)
    {
        return response()->json(
            $itinerary->load([
                'destination', 
                'city', 
                'agent', 
                'operator',
                'items.hotel',
                'items.sightseeing'
            ])
        );
    }

    public function update(Request $request, Itinerary $itinerary)
    {
        $validated = $request->validate([
            'client_name' => 'string|max:255',
            'client_email' => 'nullable|email',
            'client_phone' => 'nullable|string',
            'start_date' => 'date',
            'end_date' => 'date|after:start_date',
            'pax_count' => 'integer|min:1',
            'status' => 'in:draft,quoted,booked,completed,cancelled',
        ]);

        $itinerary->update($validated);

        return response()->json($itinerary);
    }

    public function addItem(Request $request, Itinerary $itinerary)
    {
        $validated = $request->validate([
            'day_number' => 'required|integer|min:1',
            'item_type' => 'required|in:hotel,sightseeing,transfer',
            'hotel_id' => 'nullable|exists:hotels,id',
            'sightseeing_id' => 'nullable|exists:sightseeings,id',
            'notes' => 'nullable|string',
            'order' => 'integer|min:0',
        ]);

        $validated['itinerary_id'] = $itinerary->id;

        $item = ItineraryItem::create($validated);

        return response()->json($item->load(['hotel', 'sightseeing']), 201);
    }

    public function removeItem(Itinerary $itinerary, ItineraryItem $item)
    {
        if ($item->itinerary_id !== $itinerary->id) {
            return response()->json(['message' => 'Item not found'], 404);
        }

        $item->delete();

        return response()->json(['message' => 'Item removed']);
    }

    public function assignOperator(Request $request, Itinerary $itinerary)
    {
        $validated = $request->validate([
            'operator_id' => 'required|exists:users,id',
        ]);

        $itinerary->update($validated);

        return response()->json($itinerary->load('operator'));
    }
}
