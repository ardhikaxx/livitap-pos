<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    // API: POST /api/v1/auth/login
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
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
                    'roles' => $user->roles->pluck('name'),
                    'permissions' => $user->permissions->pluck('name'),
                ],
                'token' => $token,
            ],
        ]);
    }

    // API: POST /api/v1/auth/register (disabled)
    public function register(Request $request)
    {
        return response()->json([
            'success' => false,
            'message' => 'Register tidak diizinkan. Gunakan seeder.',
        ], 403);
    }

    // API: GET /api/v1/auth/me
    public function me(Request $request)
    {
        $user = $request->user();
        $user->load(['roles', 'permissions', 'outlets', 'primaryOutlet', 'business']);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'business' => $user->business,
                'outlets' => $user->outlets,
                'primary_outlet' => $user->primaryOutlet,
                'roles' => $user->roles->pluck('name'),
                'permissions' => $user->permissions->pluck('name'),
                'last_login_at' => $user->last_login_at,
            ],
        ]);
    }

    // API: POST /api/v1/auth/logout
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        Auth::logout();

        return response()->json([
            'success' => true,
            'message' => 'Logout successful',
        ]);
    }
}

    public function logout(Request $request)
    {
        Auth::guard('sanctum')->logout();
        $request->session()->invalidate();
        $request->session()->destroyToken();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ]);
    }
}
