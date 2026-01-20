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

class StatisticsController extends Controller
{
    /**
     * Get system statistics
     * GET /api/statistics
     */
    public function index(Request $request)
    {
        // Check authorization
        if (!in_array($request->user()->role, ['admin', 'staff'])) {
            return response()->json([
                'success' => false,
                'error' => 'Unauthorized'
            ], 403);
        }

        $stats = [
            'users' => User::count(),
            'destinations' => Destination::count(),
            'cities' => City::count(),
            'hotels' => Hotel::count(),
            'sightseeings' => Sightseeing::count(),
            'itineraries' => Itinerary::count(),
            'bookings' => Booking::count(),
            'total_revenue' => Booking::sum('total_price') ?? 0,
        ];

        // User role breakdown
        $stats['users_by_role'] = [
            'admin' => User::where('role', 'admin')->count(),
            'staff' => User::where('role', 'staff')->count(),
            'agent' => User::where('role', 'agent')->count(),
            'operator' => User::where('role', 'operator')->count(),
            'hotel_partner' => User::where('role', 'hotel_partner')->count(),
        ];

        // Booking status breakdown
        $stats['bookings_by_status'] = [
            'pending' => Booking::where('status', 'pending')->count(),
            'confirmed' => Booking::where('status', 'confirmed')->count(),
            'cancelled' => Booking::where('status', 'cancelled')->count(),
        ];

        // Top destinations
        $stats['top_destinations'] = Destination::withCount('cities')
            ->orderBy('cities_count', 'desc')
            ->limit(5)
            ->get();

        // Top hotels by rating
        $stats['top_hotels'] = Hotel::orderBy('rating', 'desc')
            ->limit(5)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Get dashboard summary
     * GET /api/statistics/summary
     */
    public function summary(Request $request)
    {
        // Check authorization
        if (!in_array($request->user()->role, ['admin', 'staff'])) {
            return response()->json([
                'success' => false,
                'error' => 'Unauthorized'
            ], 403);
        }

        $summary = [
            'total_users' => User::count(),
            'active_users' => User::where('is_active', true)->count(),
            'total_destinations' => Destination::count(),
            'total_itineraries' => Itinerary::count(),
            'total_bookings' => Booking::count(),
            'confirmed_bookings' => Booking::where('status', 'confirmed')->count(),
            'pending_bookings' => Booking::where('status', 'pending')->count(),
            'total_revenue' => Booking::sum('total_price') ?? 0,
            'average_booking_value' => Booking::avg('total_price') ?? 0,
        ];

        return response()->json([
            'success' => true,
            'data' => $summary
        ]);
    }

    /**
     * Get user statistics
     * GET /api/statistics/users
     */
    public function userStats(Request $request)
    {
        // Check authorization
        if (!in_array($request->user()->role, ['admin', 'staff'])) {
            return response()->json([
                'success' => false,
                'error' => 'Unauthorized'
            ], 403);
        }

        $stats = [
            'total' => User::count(),
            'active' => User::where('is_active', true)->count(),
            'inactive' => User::where('is_active', false)->count(),
            'by_role' => [
                'admin' => User::where('role', 'admin')->count(),
                'staff' => User::where('role', 'staff')->count(),
                'agent' => User::where('role', 'agent')->count(),
                'operator' => User::where('role', 'operator')->count(),
                'hotel_partner' => User::where('role', 'hotel_partner')->count(),
            ],
            'recent_users' => User::latest()->limit(10)->get(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Get booking statistics
     * GET /api/statistics/bookings
     */
    public function bookingStats(Request $request)
    {
        // Check authorization
        if (!in_array($request->user()->role, ['admin', 'staff'])) {
            return response()->json([
                'success' => false,
                'error' => 'Unauthorized'
            ], 403);
        }

        $stats = [
            'total' => Booking::count(),
            'by_status' => [
                'pending' => Booking::where('status', 'pending')->count(),
                'confirmed' => Booking::where('status', 'confirmed')->count(),
                'cancelled' => Booking::where('status', 'cancelled')->count(),
            ],
            'total_revenue' => Booking::sum('total_price') ?? 0,
            'average_value' => Booking::avg('total_price') ?? 0,
            'max_value' => Booking::max('total_price') ?? 0,
            'min_value' => Booking::min('total_price') ?? 0,
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Get destination statistics
     * GET /api/statistics/destinations
     */
    public function destinationStats(Request $request)
    {
        $stats = [
            'total' => Destination::count(),
            'with_cities' => Destination::has('cities')->count(),
            'destinations' => Destination::withCount('cities')
                ->withCount('hotels')
                ->with('cities.sightseeings')
                ->get(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}
