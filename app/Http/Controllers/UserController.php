<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Auth;

class UserController extends Controller
{
    /**
     * Get the authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show()
    {
        try {
            // Get the authenticated user
            $user = Auth::user();

            // Return user details in JSON response
            return response()->json(['user' => $user], 200);
        } catch (\Exception $e) {
            // ❌ Error response for exceptions
            return response()->json(['message' => 'Failed to fetch user details.'], 500);
        }
    }

    /**
     * Update the authenticated user's profile.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        // Validate the request
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        try {
            // Get the authenticated user
            $user = Auth::user();

            // Update name
            $user->name = $validatedData['name'];

            // Update password if provided
            if (isset($validatedData['password'])) {
                $user->password = bcrypt($validatedData['password']);
            }

            $user->save();

            // ✅ Success response
            return response()->json([
                'message' => 'Profile has been updated successfully.'
            ], 200);
        } catch (\Exception $e) {
            report($e->getMessage());
            // ❌ Error response for exceptions
            return response()->json(['message' => 'Failed to update profile.'], 500);
        }
    }

    /**
     * Logout the authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        try {
            // Get the authenticated user
            $user = Auth::user();

            // Clear the API token
            $user->api_token = null;
            $user->save();

            // ✅ Success response
            return response()->json([
                'message' => 'Logged out successfully.'
            ], 200);
        } catch (\Exception $e) {
            // ❌ Error response for exceptions
            return response()->json(['message' => 'Failed to log out.'], 500);
        }
    }
}