<?php

namespace App\Http\Controllers;

use App\Models\Destination;
use Illuminate\Http\Request;

class DestinationController extends Controller
{
    public function index(Request $request)
    {
        $query = Destination::with('cities');

        // Agents only see active destinations
        if ($request->user()->role === 'agent') {
            $query->active();
        }

        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $destination = Destination::create($validated);

        return response()->json($destination, 201);
    }

    public function update(Request $request, Destination $destination)
    {
        $validated = $request->validate([
            'name' => 'string|max:255',
            'country' => 'string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $destination->update($validated);

        return response()->json($destination);
    }

    public function destroy(Destination $destination)
    {
        $destination->delete();

        return response()->json(['message' => 'Destination deleted']);
    }
}
