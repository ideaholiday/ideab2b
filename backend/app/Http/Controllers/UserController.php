<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * List all users
     * GET /api/users
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Filter by role if provided
        if ($request->has('role')) {
            $query->where('role', $request->role);
        }

        // Filter by status if provided
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        // Pagination
        $per_page = $request->input('per_page', 15);
        $users = $query->paginate($per_page);

        return response()->json([
            'success' => true,
            'data' => $users->items(),
            'pagination' => [
                'total' => $users->total(),
                'per_page' => $users->perPage(),
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage()
            ]
        ]);
    }

    /**
     * Create a new user
     * POST /api/users
     */
    public function store(Request $request)
    {
        // Check authorization
        if (!in_array($request->user()->role, ['admin', 'staff'])) {
            return response()->json([
                'success' => false,
                'error' => 'Unauthorized: Only admin and staff can create users'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,staff,agent,operator,hotel_partner',
            'phone' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'phone' => $request->phone,
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'data' => $user
        ], 201);
    }

    /**
     * Get user by ID
     * GET /api/users/{id}
     */
    public function show($id)
    {
        $user = User::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    /**
     * Update user
     * PUT /api/users/{id}
     */
    public function update(Request $request, $id)
    {
        // Check authorization
        if (!in_array($request->user()->role, ['admin', 'staff']) && $request->user()->id != $id) {
            return response()->json([
                'success' => false,
                'error' => 'Unauthorized'
            ], 403);
        }

        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $id,
            'phone' => 'sometimes|string',
            'role' => 'sometimes|in:admin,staff,agent,operator,hotel_partner',
            'is_active' => 'sometimes|boolean',
            'password' => 'sometimes|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        if ($request->has('name')) {
            $user->name = $request->name;
        }

        if ($request->has('email')) {
            $user->email = $request->email;
        }

        if ($request->has('phone')) {
            $user->phone = $request->phone;
        }

        if ($request->has('role')) {
            $user->role = $request->role;
        }

        if ($request->has('is_active')) {
            $user->is_active = $request->is_active;
        }

        if ($request->has('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    /**
     * Delete user
     * DELETE /api/users/{id}
     */
    public function destroy(Request $request, $id)
    {
        // Check authorization
        if ($request->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'error' => 'Unauthorized: Only admin can delete users'
            ], 403);
        }

        $user = User::findOrFail($id);
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully'
        ]);
    }

    /**
     * Get users by role
     * GET /api/users/role/{role}
     */
    public function getByRole($role)
    {
        $users = User::where('role', $role)->get();

        return response()->json([
            'success' => true,
            'data' => $users,
            'count' => count($users)
        ]);
    }

    /**
     * Activate/Deactivate user
     * PUT /api/users/{id}/status
     */
    public function updateStatus(Request $request, $id)
    {
        // Check authorization
        if ($request->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'error' => 'Unauthorized'
            ], 403);
        }

        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'is_active' => 'required|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user->is_active = $request->is_active;
        $user->save();

        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }
}
