<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Roles;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Register a new user
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => Roles::Customer, // Default role is Customer
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'User registered successfully',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role->label(),
            ],
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 201);
    }

    /**
     * Login user
     */
    public function login(LoginRequest $request): JsonResponse
    {
        if (! Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Invalid credentials',
            ], 401);
        }

        $user = User::where('email', $request->email)->firstOrFail();

        // Revoke all existing tokens
        $user->tokens()->delete();

        // Create new token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => new UserResource($user),
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * Logout user (Revoke the token)
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Successfully logged out',
        ]);
    }

    /**
     * Get the authenticated User
     */
    public function profile(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'user' => new UserResource($user),
        ]);
    }

    /**
     * Update user profile
     */
    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        $user = $request->user();

        $updateData = [];

        if ($request->has('name')) {
            $updateData['name'] = $request->name;
        }

        if ($request->has('email')) {
            $updateData['email'] = $request->email;
        }

        if ($request->has('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);
        $user->refresh();

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => new UserResource($user),
        ]);
    }

    /**
     * Logout from all devices (Revoke all tokens)
     */
    public function logoutAll(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Successfully logged out from all devices',
        ]);
    }
}
