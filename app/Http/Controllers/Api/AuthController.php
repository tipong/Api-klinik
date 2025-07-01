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
            'role' => 'required|in:admin,hrd,front_office,kasir,dokter,beautician,pelanggan',
            'phone' => 'nullable|string|max:20',
            'no_telepon' => 'nullable|string|max:20', // Support both formats
            'alamat' => 'nullable|string',
            'address' => 'nullable|string', // Support both formats
            'jenis_kelamin' => 'nullable|in:male,female',
            'gender' => 'nullable|in:male,female', // Support both formats
        ], [
            'username.required' => 'Username harus diisi',
            'username.unique' => 'Username sudah digunakan',
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'nama.required' => 'Nama harus diisi',
            'password.required' => 'Password harus diisi',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
            'role.required' => 'Role harus dipilih',
            'role.in' => 'Role tidak valid',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // Handle dual format fields
            $phone = $request->phone ?? $request->no_telepon;
            $alamat = $request->alamat ?? $request->address;
            $gender = $request->jenis_kelamin ?? $request->gender;

            $user = User::create([
                'username' => $request->username,
                'email' => $request->email,
                'nama' => $request->nama,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'phone' => $phone,
                'alamat' => $alamat,
                'jenis_kelamin' => $gender,
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
            'email' => 'required|email|max:100',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Use email for authentication
        $credentials = [
            'email' => $request->email,
            'password' => $request->password
        ];

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid credentials',
            ], 401);
        }

        $user = Auth::user();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Login successful',
            'data' => [
                'user' => [
                    'id_user' => $user->id_user,
                    'nama_user' => $user->nama_user,
                    'email' => $user->email,
                    'no_telp' => $user->no_telp,
                    'role' => $user->role,
                    'tanggal_lahir' => $user->tanggal_lahir,
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

    /**
     * Get all users (Admin only)
     */
    public function getAllUsers(Request $request)
    {
        try {
            $user = $request->user();
            
            if ($user->role !== 'admin') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized. Only admin can view all users.',
                ], 403);
            }

            // Sample user data
            $users = [
                [
                    'id' => 1,
                    'username' => 'admin',
                    'email' => 'admin@klinik.com',
                    'nama' => 'Administrator',
                    'role' => 'admin',
                    'status' => 'aktif',
                    'phone' => '+6281234567890',
                    'created_at' => '2024-01-01 00:00:00'
                ],
                [
                    'id' => 2,
                    'username' => 'hrd_user',
                    'email' => 'hrd@klinik.com',
                    'nama' => 'HRD Manager',
                    'role' => 'hrd',
                    'status' => 'aktif',
                    'phone' => '+6281234567891',
                    'created_at' => '2024-01-15 10:00:00'
                ],
                [
                    'id' => 3,
                    'username' => 'frontoffice',
                    'email' => 'frontoffice@klinik.com',
                    'nama' => 'Front Office Staff',
                    'role' => 'front_office',
                    'status' => 'aktif',
                    'phone' => '+6281234567892',
                    'created_at' => '2024-02-01 09:00:00'
                ],
                [
                    'id' => 4,
                    'username' => 'kasir',
                    'email' => 'kasir@klinik.com',
                    'nama' => 'Kasir',
                    'role' => 'kasir',
                    'status' => 'aktif',
                    'phone' => '+6281234567893',
                    'created_at' => '2024-02-15 08:30:00'
                ],
                [
                    'id' => 5,
                    'username' => 'dokter',
                    'email' => 'dokter@klinik.com',
                    'nama' => 'Dr. Smith',
                    'role' => 'dokter',
                    'status' => 'aktif',
                    'phone' => '+6281234567894',
                    'created_at' => '2024-03-01 07:00:00'
                ],
                [
                    'id' => 6,
                    'username' => 'beautician',
                    'email' => 'beautician@klinik.com',
                    'nama' => 'Beauty Expert',
                    'role' => 'beautician',
                    'status' => 'aktif',
                    'phone' => '+6281234567895',
                    'created_at' => '2024-03-15 08:00:00'
                ],
                [
                    'id' => 7,
                    'username' => 'customer',
                    'email' => 'customer@klinik.com',
                    'nama' => 'John Customer',
                    'role' => 'pelanggan',
                    'status' => 'aktif',
                    'phone' => '+6281234567896',
                    'created_at' => '2024-04-01 10:30:00'
                ]
            ];

            return response()->json([
                'status' => 'success',
                'message' => 'Users retrieved successfully',
                'data' => $users,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve users',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update user status (Admin only)
     */
    public function updateUserStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:aktif,nonaktif',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $user = $request->user();
            
            if ($user->role !== 'admin') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized. Only admin can update user status.',
                ], 403);
            }

            // Don't allow admin to deactivate themselves
            if ($user->id == $id && $request->status === 'nonaktif') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You cannot deactivate your own account.',
                ], 422);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'User status updated successfully',
                'data' => [
                    'user_id' => $id,
                    'status' => $request->status,
                    'updated_at' => now(),
                    'updated_by' => $user->id,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update user status',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete user (Admin only)
     */
    public function deleteUser(Request $request, $id)
    {
        try {
            $user = $request->user();
            
            if ($user->role !== 'admin') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized. Only admin can delete users.',
                ], 403);
            }

            // Don't allow admin to delete themselves
            if ($user->id == $id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You cannot delete your own account.',
                ], 422);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'User deleted successfully',
                'data' => [
                    'deleted_user_id' => $id,
                    'deleted_at' => now(),
                    'deleted_by' => $user->id,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete user',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get dashboard statistics
     */
    public function getDashboardStats(Request $request)
    {
        try {
            $user = $request->user();
            
            $stats = [
                'general' => [
                    'total_users' => 156,
                    'active_users' => 142,
                    'total_appointments_today' => 12,
                    'total_revenue_today' => 2400000,
                ],
                'role_specific' => []
            ];

            // Role-specific dashboard data
            switch ($user->role) {
                case 'admin':
                case 'hrd':
                    $stats['role_specific'] = [
                        'total_employees' => 45,
                        'pending_applications' => 8,
                        'active_trainings' => 3,
                        'monthly_revenue' => 45600000,
                        'customer_growth' => 12.5,
                    ];
                    break;

                case 'front_office':
                    $stats['role_specific'] = [
                        'todays_appointments' => 15,
                        'pending_confirmations' => 5,
                        'new_customers_today' => 3,
                        'customer_calls_today' => 8,
                    ];
                    break;

                case 'kasir':
                    $stats['role_specific'] = [
                        'todays_transactions' => 18,
                        'total_revenue_today' => 2400000,
                        'pending_payments' => 4,
                        'payment_success_rate' => 95.5,
                    ];
                    break;

                case 'dokter':
                    $stats['role_specific'] = [
                        'todays_appointments' => 8,
                        'completed_consultations' => 5,
                        'upcoming_appointments' => 3,
                        'patient_satisfaction' => 4.8,
                    ];
                    break;

                case 'beautician':
                    $stats['role_specific'] = [
                        'todays_treatments' => 6,
                        'completed_treatments' => 4,
                        'upcoming_treatments' => 2,
                        'treatment_rating' => 4.9,
                    ];
                    break;

                case 'pelanggan':
                    $stats['role_specific'] = [
                        'total_appointments' => 12,
                        'completed_treatments' => 10,
                        'upcoming_appointments' => 1,
                        'loyalty_points' => 250,
                    ];
                    break;
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Dashboard statistics retrieved successfully',
                'data' => $stats,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve dashboard statistics',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
