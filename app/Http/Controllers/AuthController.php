<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{   
    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $request->user()->id,
            'birthdate' => 'required|date',
        ]);

        try {
            $user = $request->user();
            $userData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'birthdate' => $validated['birthdate'],
            ];

            // Handle profile photo upload
            if ($request->hasFile('profile_photo')) {
                // Delete the old profile photo if it exists
                if ($user->profile_photo) {
                    Storage::disk('public')->delete($user->profile_photo);
                }

                // Store the new profile photo
                $path = $request->file('profile_photo')->store('profile_photos', 'public');
                $userData['profile_photo'] = $path;
            }

            $user->update($userData);

            return response()->json([
                'message' => 'Profile updated successfully',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'birthdate' => $user->birthdate,
                    'role' => $user->role,
                    'profile_photo' => $user->profile_photo ? asset('storage/' . $user->profile_photo) : null,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Profile update failed: ' . $e->getMessage());
            return response()->json([
                'message' => 'Profile update failed. Please try again.'
            ], 500);
        }
    }
    
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
    
        $user = User::where('email', $request->email)->first();
    
        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Create token with role-based abilities
        $token = $user->createToken('auth_token', [$user->role])->plainTextToken;
    
        return response()->json([
            'token' => $token,  
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'birthdate' => $user->birthdate,
                'role' => $user->role,
                'profile_photo' => $user->profile_photo ? asset('storage/' . $user->profile_photo) : null,
            ]
        ]);
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'birthdate' => 'required|date|',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);
    
        try {
            $userData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'birthdate' => $validated['birthdate'],
                'role' => 'user',
            ];

            // Handle profile photo upload
            if ($request->hasFile('profile_photo')) {
                $path = $request->file('profile_photo')->store('profile_photos', 'public');
                $userData['profile_photo'] = $path;
            }

            $user = User::create($userData);
    
            $token = $user->createToken('auth_token', ['user'])->plainTextToken;
    
            return response()->json([
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'birthdate' => $user->birthdate,
                    'role' => $user->role,
                    'profile_photo' => $user->profile_photo ? asset('storage/' . $user->profile_photo) : null,
                ]
            ], 201);
    
        } catch (\Exception $e) {
            Log::error('Registration failed: ' . $e->getMessage());
            return response()->json([
                'message' => 'Registration failed. Please try again.'
            ], 500);
        }   
    }

    public function changePassword(Request $request)
    {
        try {
            $request->validate([
                'current_password' => 'required',
                'new_password' => 'required|min:8',
                'confirm_password' => 'required|same:new_password'
            ]);

            $user = $request->user();

            // Check if current password matches
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'message' => 'Current password is incorrect.',
                    'errors' => [
                        'current_password' => ['Current password is incorrect.']
                    ]
                ], 422); // Using 422 for validation error
            }

            // Update password
            $user->password = Hash::make($request->new_password);
            $user->save();

            return response()->json([
                'message' => 'Password changed successfully'
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Password change failed: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to change password. Please try again.'
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();
            return response()->json([
                'message' => 'Logged out successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Logout error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Logout failed'
            ], 500);
        }
    }

    public function user(Request $request)
    {
        $user = $request->user();
        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'birthdate' => $user->birthdate,
            'role' => $user->role,
            'profile_photo' => $user->profile_photo ? asset('storage/' . $user->profile_photo) : null,
        ]);
    }
}
