<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * API: POST /api/v1/auth/login
     * Login menggunakan Sanctum guard
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Gunakan guard 'sanctum' untuk API authentication
        if (!Auth::guard('sanctum')->attempt($request->only('email', 'password'))) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah',
            ], 401);
        }

        $user = User::where('email', $request->email)->first();
        
        if (!$user || !$user->is_active) {
            Auth::guard('sanctum')->logout();
            return response()->json([
                'success' => false,
                'message' => 'Akun tidak aktif',
            ], 403);
        }

        // Buat token API
        $token = $user->createToken('pos-token')->plainTextToken;
        $user->update(['last_login_at' => now()]);

        // Load relasi
        $user->load(['roles', 'permissions', 'outlets', 'primaryOutlet', 'business']);

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'photo' => $user->photo,
                    'business' => $user->business,
                    'outlets' => $user->outlets,
                    'primary_outlet' => $user->primaryOutlet,
                    'roles' => $user->roles->pluck('name'),
                    'permissions' => $user->permissions->pluck('name'),
                    'last_login_at' => $user->last_login_at,
                ],
                'token' => $token,
                'token_type' => 'Bearer',
            ],
        ]);
    }

    /**
     * API: POST /api/v1/auth/register (disabled)
     */
    public function register(Request $request)
    {
        return response()->json([
            'success' => false,
            'message' => 'Pendaftaran tidak diizinkan. Gunakan seeder.',
        ], 403);
    }

    /**
     * API: GET /api/v1/auth/me
     * Get current authenticated user
     */
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
                'phone' => $user->phone,
                'photo' => $user->photo,
                'business' => $user->business,
                'outlets' => $user->outlets,
                'primary_outlet' => $user->primaryOutlet,
                'roles' => $user->roles->pluck('name'),
                'permissions' => $user->permissions->pluck('name'),
                'last_login_at' => $user->last_login_at,
            ],
        ]);
    }

    /**
     * API: POST /api/v1/auth/logout
     * Logout dan hapus token
     */
    public function logout(Request $request)
    {
        // Hapus token yang sedang digunakan
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil',
        ]);
    }
}
