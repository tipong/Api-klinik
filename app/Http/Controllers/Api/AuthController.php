<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Register a new user
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:50|unique:tb_user,username',
            'email' => 'required|string|email|max:100|unique:tb_user,email',
            'nama' => 'required|string|max:100',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'nullable|in:admin,hrd,pegawai,pelamar,kasir,front office,dokter,beautician,pelanggan',
            'phone' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $user = User::create([
                'username' => $request->username,
                'email' => $request->email,
                'nama' => $request->nama,
                'password' => Hash::make($request->password),
                'role' => $request->role ?? 'pelamar',
                'phone' => $request->phone,
                'alamat' => $request->alamat,
                'status' => 'aktif',
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'status' => 'success',
                'message' => 'User registered successfully',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'username' => $user->username,
                        'email' => $user->email,
                        'nama' => $user->nama,
                        'role' => $user->role,
                        'phone' => $user->phone,
                        'alamat' => $user->alamat,
                        'status' => $user->status,
                    ],
                    'token' => $token,
                ],
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Registration failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Login user
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required_without:email|string|max:50',
            'email' => 'required_without:username|email|max:100',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Check if login is with username or email
        $credentials = $request->has('username') 
            ? ['username' => $request->username, 'password' => $request->password]
            : ['email' => $request->email, 'password' => $request->password];

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid credentials',
            ], 401);
        }

        $user = Auth::user();

        // Check if user is active
        if ($user->status !== 'aktif') {
            Auth::logout();
            return response()->json([
                'status' => 'error',
                'message' => 'Account is not active. Please contact administrator.',
            ], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Login successful',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'email' => $user->email,
                    'nama' => $user->nama,
                    'role' => $user->role,
                    'phone' => $user->phone,
                    'alamat' => $user->alamat,
                    'status' => $user->status,
                ],
                'token' => $token,
            ],
        ]);
    }

    /**
     * Get user profile
     */
    public function profile(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'status' => 'success',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'email' => $user->email,
                    'nama' => $user->nama,
                    'role' => $user->role,
                    'phone' => $user->phone,
                    'alamat' => $user->alamat,
                    'status' => $user->status,
                    'photo' => $user->photo,
                    'created_at' => $user->created_at,
                ],
            ],
        ]);
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'nama' => 'sometimes|required|string|max:100',
            'phone' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'current_password' => 'required_with:password|string',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // Check current password if new password is provided
            if ($request->password) {
                if (!Hash::check($request->current_password, $user->password)) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Current password is incorrect',
                    ], 422);
                }
                $user->password = Hash::make($request->password);
            }

            // Update other fields
            if ($request->has('nama')) {
                $user->nama = $request->nama;
            }
            if ($request->has('phone')) {
                $user->phone = $request->phone;
            }
            if ($request->has('alamat')) {
                $user->alamat = $request->alamat;
            }

            $user->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Profile updated successfully',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'username' => $user->username,
                        'email' => $user->email,
                        'nama' => $user->nama,
                        'role' => $user->role,
                        'phone' => $user->phone,
                        'alamat' => $user->alamat,
                        'status' => $user->status,
                    ],
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Profile update failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Logout successful',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Logout failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Logout from all devices
     */
    public function logoutAll(Request $request)
    {
        try {
            $request->user()->tokens()->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Logged out from all devices successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Logout failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
