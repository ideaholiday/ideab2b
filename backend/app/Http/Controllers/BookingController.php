<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Itinerary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{
    /**
     * List all bookings
     * GET /api/bookings
     */
    public function index(Request $request)
    {
        $query = Booking::with(['itinerary', 'operator']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by operator (for operator users)
        if ($request->user()->role === 'operator') {
            $query->where('operator_id', $request->user()->id);
        }

        // Filter by itinerary
        if ($request->has('itinerary_id')) {
            $query->where('itinerary_id', $request->itinerary_id);
        }

        // Pagination
        $per_page = $request->input('per_page', 15);
        $bookings = $query->paginate($per_page);

        return response()->json([
            'success' => true,
            'data' => $bookings->items(),
            'pagination' => [
                'total' => $bookings->total(),
                'per_page' => $bookings->perPage(),
                'current_page' => $bookings->currentPage(),
                'last_page' => $bookings->lastPage()
            ]
        ]);
    }

    /**
     * Create a new booking
     * POST /api/bookings
     */
    public function store(Request $request)
    {
        // Check authorization
        if (!in_array($request->user()->role, ['admin', 'staff', 'agent', 'operator'])) {
            return response()->json([
                'success' => false,
                'error' => 'Unauthorized'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'itinerary_id' => 'required|exists:itineraries,id',
            'operator_id' => 'sometimes|exists:users,id',
            'status' => 'sometimes|in:pending,confirmed,cancelled',
            'notes' => 'sometimes|string',
            'total_price' => 'sometimes|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $booking = new Booking();
        $booking->itinerary_id = $request->itinerary_id;
        $booking->operator_id = $request->operator_id ?? $request->user()->id;
        $booking->status = $request->status ?? 'pending';
        $booking->notes = $request->notes ?? '';
        $booking->total_price = $request->total_price ?? 0;
        $booking->save();

        return response()->json([
            'success' => true,
            'data' => $booking->load(['itinerary', 'operator'])
        ], 201);
    }

    /**
     * Get booking by ID
     * GET /api/bookings/{id}
     */
    public function show($id)
    {
        $booking = Booking::with(['itinerary', 'operator'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $booking
        ]);
    }

    /**
     * Update booking
     * PUT /api/bookings/{id}
     */
    public function update(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        // Check authorization
        if ($request->user()->role === 'operator' && $booking->operator_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'error' => 'Unauthorized'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'sometimes|in:pending,confirmed,cancelled',
            'notes' => 'sometimes|string',
            'total_price' => 'sometimes|numeric|min:0',
            'operator_id' => 'sometimes|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        if ($request->has('status')) {
            $booking->status = $request->status;
        }

        if ($request->has('notes')) {
            $booking->notes = $request->notes;
        }

        if ($request->has('total_price')) {
            $booking->total_price = $request->total_price;
        }

        if ($request->has('operator_id')) {
            $booking->operator_id = $request->operator_id;
        }

        $booking->save();

        return response()->json([
            'success' => true,
            'data' => $booking->load(['itinerary', 'operator'])
        ]);
    }

    /**
     * Delete booking
     * DELETE /api/bookings/{id}
     */
    public function destroy(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        // Check authorization
        if (!in_array($request->user()->role, ['admin', 'staff'])) {
            return response()->json([
                'success' => false,
                'error' => 'Unauthorized'
            ], 403);
        }

        $booking->delete();

        return response()->json([
            'success' => true,
            'message' => 'Booking deleted successfully'
        ]);
    }

    /**
     * Get bookings by status
     * GET /api/bookings/status/{status}
     */
    public function getByStatus($status)
    {
        $bookings = Booking::where('status', $status)->with(['itinerary', 'operator'])->get();

        return response()->json([
            'success' => true,
            'data' => $bookings,
            'count' => count($bookings)
        ]);
    }

    /**
     * Confirm booking
     * PUT /api/bookings/{id}/confirm
     */
    public function confirm(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        // Check authorization
        if (!in_array($request->user()->role, ['admin', 'staff', 'operator'])) {
            return response()->json([
                'success' => false,
                'error' => 'Unauthorized'
            ], 403);
        }

        $booking->status = 'confirmed';
        $booking->save();

        return response()->json([
            'success' => true,
            'data' => $booking->load(['itinerary', 'operator'])
        ]);
    }

    /**
     * Cancel booking
     * PUT /api/bookings/{id}/cancel
     */
    public function cancel(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        // Check authorization
        if (!in_array($request->user()->role, ['admin', 'staff', 'operator'])) {
            return response()->json([
                'success' => false,
                'error' => 'Unauthorized'
            ], 403);
        }

        $booking->status = 'cancelled';
        $booking->save();

        return response()->json([
            'success' => true,
            'data' => $booking->load(['itinerary', 'operator'])
        ]);
    }

    /**
     * Get statistics
     * GET /api/bookings/statistics
     */
    public function statistics(Request $request)
    {
        // Check authorization
        if (!in_array($request->user()->role, ['admin', 'staff'])) {
            return response()->json([
                'success' => false,
                'error' => 'Unauthorized'
            ], 403);
        }

        $total_bookings = Booking::count();
        $confirmed_bookings = Booking::where('status', 'confirmed')->count();
        $pending_bookings = Booking::where('status', 'pending')->count();
        $cancelled_bookings = Booking::where('status', 'cancelled')->count();
        $total_revenue = Booking::sum('total_price');

        return response()->json([
            'success' => true,
            'data' => [
                'total_bookings' => $total_bookings,
                'confirmed' => $confirmed_bookings,
                'pending' => $pending_bookings,
                'cancelled' => $cancelled_bookings,
                'total_revenue' => $total_revenue,
            ]
        ]);
    }
}
