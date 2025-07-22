<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    use ApiResponseTrait;
    /**
     * Register a new user
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_user' => 'required|string|max:255',
            'no_telp' => 'required|string|max:255|unique:tb_user,no_telp',
            'email' => 'required|string|email|max:255|unique:tb_user,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,hrd,front office,kasir,dokter,beautician,pelanggan',
            'tanggal_lahir' => 'nullable|date',
            'foto_profil' => 'nullable|string|max:255',
        ], [
            'nama_user.required' => 'Nama user harus diisi',
            'no_telp.required' => 'Nomor telepon harus diisi',
            'no_telp.unique' => 'Nomor telepon sudah digunakan',
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
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
            $user = User::create([
                'nama_user' => $request->nama_user,
                'no_telp' => $request->no_telp,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'tanggal_lahir' => $request->tanggal_lahir,
                'foto_profil' => $request->foto_profil,
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'status' => 'success',
                'message' => 'User registered successfully',
                'data' => [
                    'user' => [
                        'id_user' => $user->id_user,
                        'nama_user' => $user->nama_user,
                        'no_telp' => $user->no_telp,
                        'email' => $user->email,
                        'role' => $user->role,
                        'tanggal_lahir' => $user->tanggal_lahir,
                        'foto_profil' => $user->foto_profil,
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
                    'id_user' => $user->id_user,
                    'nama_user' => $user->nama_user,
                    'email' => $user->email,
                    'no_telp' => $user->no_telp,
                    'role' => $user->role,
                    'tanggal_lahir' => $user->tanggal_lahir,
                    'foto_profil' => $user->foto_profil,
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
            'nama_user' => 'sometimes|required|string|max:255',
            'no_telp' => 'sometimes|required|string|max:255|unique:tb_user,no_telp,' . $user->id_user . ',id_user',
            'tanggal_lahir' => 'nullable|date',
            'foto_profil' => 'nullable|string|max:255',
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
            if ($request->has('nama_user')) {
                $user->nama_user = $request->nama_user;
            }
            if ($request->has('no_telp')) {
                $user->no_telp = $request->no_telp;
            }
            if ($request->has('tanggal_lahir')) {
                $user->tanggal_lahir = $request->tanggal_lahir;
            }
            if ($request->has('foto_profil')) {
                $user->foto_profil = $request->foto_profil;
            }

            $user->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Profile updated successfully',
                'data' => [
                    'user' => [
                        'id_user' => $user->id_user,
                        'nama_user' => $user->nama_user,
                        'email' => $user->email,
                        'no_telp' => $user->no_telp,
                        'role' => $user->role,
                        'tanggal_lahir' => $user->tanggal_lahir,
                        'foto_profil' => $user->foto_profil,
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
     * Get all users (Admin/HRD only)
     */
    public function getAllUsers(Request $request)
    {
        try {
            $user = $request->user();
            
            // Check if user has admin privileges
            if (!$user->hasAdminPrivileges()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized. Only admin or HRD can view all users.',
                ], 403);
            }

            $query = User::query();

            // Add filters if provided
            if ($request->filled('role')) {
                $query->where('role', $request->role);
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('nama_user', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('no_telp', 'like', "%{$search}%");
                });
            }

            // Pagination
            $perPage = $request->get('per_page', 15);
            $users = $query->orderBy('created_at', 'desc')->paginate($perPage);

            return response()->json([
                'status' => 'success',
                'message' => 'Users retrieved successfully',
                'data' => [
                    'users' => $users->items(),
                    'pagination' => [
                        'current_page' => $users->currentPage(),
                        'total_pages' => $users->lastPage(),
                        'per_page' => $users->perPage(),
                        'total' => $users->total(),
                    ]
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve users: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get user by ID (Admin/HRD only)
     */
    public function getUserById(Request $request, $id)
    {
        try {
            $user = $request->user();
            
            // Check if user has admin privileges
            if (!$user->hasAdminPrivileges()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized. Only admin or HRD can view user details.',
                ], 403);
            }

            // Find user by ID
            $targetUser = User::find($id);

            if (!$targetUser) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not found',
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'User retrieved successfully',
                'data' => [
                    'user' => [
                        'id_user' => $targetUser->id_user,
                        'nama_user' => $targetUser->nama_user,
                        'email' => $targetUser->email,
                        'no_telp' => $targetUser->no_telp,
                        'role' => $targetUser->role,
                        'tanggal_lahir' => $targetUser->tanggal_lahir,
                        'foto_profil' => $targetUser->foto_profil,
                        'created_at' => $targetUser->created_at,
                        'updated_at' => $targetUser->updated_at,
                    ]
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve user: ' . $e->getMessage(),
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
            $currentUser = $request->user();
            
            if ($currentUser->role !== 'admin') {
                return $this->errorResponse('Unauthorized. Only admin can delete users.', 403, []);
            }

            // Don't allow admin to delete themselves
            if ($currentUser->id == $id) {
                return $this->errorResponse('You cannot delete your own account.', 422, []);
            }

            $userToDelete = User::find($id);
            
            if (!$userToDelete) {
                return $this->errorResponse('User tidak ditemukan', 404, []);
            }
            
            // Store data for response before deletion
            $responseData = [
                'id_user' => $userToDelete->id_user,
                'nama_user' => $userToDelete->nama_user,
                'email' => $userToDelete->email,
                'role' => $userToDelete->role,
                'deleted_at' => now(),
                'deleted_by' => $currentUser->id,
            ];
            
            $userToDelete->delete();

            return $this->successResponse(
                $responseData,
                'User berhasil dihapus'
            );

        } catch (\Exception $e) {
            return $this->errorResponse(
                'Gagal menghapus user',
                500,
                ['error' => $e->getMessage()]
            );
        }
    }

    /**
     * Public delete method for frontend integration (no auth required)
     */
    public function publicDeleteUser($id)
    {
        try {
            $userToDelete = User::find($id);
            
            if (!$userToDelete) {
                return $this->errorResponse('User tidak ditemukan', 404, []);
            }
            
            // Store data for response before deletion
            $responseData = [
                'id_user' => $userToDelete->id_user,
                'nama_user' => $userToDelete->nama_user,
                'email' => $userToDelete->email,
                'role' => $userToDelete->role
            ];
            
            $userToDelete->delete();

            return $this->successResponse(
                $responseData,
                'User berhasil dihapus'
            );

        } catch (\Exception $e) {
            return $this->errorResponse(
                'Gagal menghapus user',
                500,
                ['error' => $e->getMessage()]
            );
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
