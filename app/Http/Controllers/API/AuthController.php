<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Http\Resources\UserResource;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah',
            ], 401);
        }

        $user = User::where('email', $request->email)->first();
        
        if (!$user->is_active) {
            Auth::logout();
            return response()->json([
                'success' => false,
                'message' => 'Akun tidak aktif',
            ], 403);
        }

        // Create sanctum token
        $token = $user->createToken('pos-token')->plainTextToken;

        $user->update(['last_login_at' => now()]);

        $user->load(['roles', 'permissions', 'outlets', 'primaryOutlet', 'business']);

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'business' => $user->business,
                    'outlets' => $user->outlets,
                    'primary_outlet' => $user->primaryOutlet,
                    'roles' => $user->roles,
                ],
                'token' => $token,
            ],
        ]);
    }

    public function register(Request $request)
    {
        // Typically disabled for POS system - use seeder instead
        return response()->json([
            'success' => false,
            'message' => 'Register tidak diizinkan',
        ], 403);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout successful',
        ]);
    }

    public function user(Request $request)
    {
        $user = $request->user();
        $user->load(['roles', 'permissions', 'outlets', 'primaryOutlet', 'business']);

        return response()->json([
            'success' => true,
            'data' => $user,
        ]);
    }
}
