<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\City;
use App\Models\Destination;
use App\Models\Hotel;
use App\Models\Itinerary;
use App\Models\Sightseeing;
use App\Models\User;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Global search across all entities
     * GET /api/search?q=query
     */
    public function search(Request $request)
    {
        $query = $request->input('q', '');

        if (strlen($query) < 2) {
            return response()->json([
                'success' => false,
                'error' => 'Search query must be at least 2 characters'
            ], 400);
        }

        $destinations = Destination::where('name', 'like', "%$query%")
            ->orWhere('description', 'like', "%$query%")
            ->limit(10)
            ->get();

        $cities = City::where('name', 'like', "%$query%")
            ->orWhere('description', 'like', "%$query%")
            ->limit(10)
            ->get();

        $hotels = Hotel::where('name', 'like', "%$query%")
            ->orWhere('description', 'like', "%$query%")
            ->limit(10)
            ->get();

        $sightseeings = Sightseeing::where('name', 'like', "%$query%")
            ->orWhere('description', 'like', "%$query%")
            ->limit(10)
            ->get();

        $itineraries = Itinerary::where('name', 'like', "%$query%")
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'destinations' => $destinations,
                'cities' => $cities,
                'hotels' => $hotels,
                'sightseeings' => $sightseeings,
                'itineraries' => $itineraries,
            ],
            'total' => count($destinations) + count($cities) + count($hotels) + count($sightseeings) + count($itineraries)
        ]);
    }

    /**
     * Search destinations
     * GET /api/search/destinations?q=query
     */
    public function searchDestinations(Request $request)
    {
        $query = $request->input('q', '');

        $results = Destination::where('name', 'like', "%$query%")
            ->orWhere('description', 'like', "%$query%")
            ->orWhere('country', 'like', "%$query%")
            ->get();

        return response()->json([
            'success' => true,
            'data' => $results,
            'count' => count($results)
        ]);
    }

    /**
     * Search cities
     * GET /api/search/cities?q=query
     */
    public function searchCities(Request $request)
    {
        $query = $request->input('q', '');

        $results = City::where('name', 'like', "%$query%")
            ->orWhere('description', 'like', "%$query%")
            ->with('destination')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $results,
            'count' => count($results)
        ]);
    }

    /**
     * Search hotels
     * GET /api/search/hotels?q=query
     */
    public function searchHotels(Request $request)
    {
        $query = $request->input('q', '');

        $results = Hotel::where('name', 'like', "%$query%")
            ->orWhere('description', 'like', "%$query%")
            ->with('city')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $results,
            'count' => count($results)
        ]);
    }

    /**
     * Search sightseeings
     * GET /api/search/sightseeings?q=query
     */
    public function searchSightseeings(Request $request)
    {
        $query = $request->input('q', '');

        $results = Sightseeing::where('name', 'like', "%$query%")
            ->orWhere('description', 'like', "%$query%")
            ->with('city')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $results,
            'count' => count($results)
        ]);
    }
}
