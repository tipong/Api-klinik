<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use App\Models\User;
use App\Models\Posisi;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class PegawaiController extends Controller
{
    use ApiResponseTrait;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Pegawai::with(['user', 'posisi']);
        
        // Filter by status
        if ($request->has('status')) {
            if ($request->status === 'active') {
                $query->whereNull('tanggal_keluar');
            } elseif ($request->status === 'inactive') {
                $query->whereNotNull('tanggal_keluar');
            }
        }
        
        // Filter by posisi
        if ($request->filled('id_posisi')) {
            $query->where('id_posisi', $request->id_posisi);
        }
        
        // Search by name
        if ($request->filled('search')) {
            $query->where('nama_lengkap', 'like', '%' . $request->search . '%');
        }
        
        $pegawai = $query->orderBy('nama_lengkap')->paginate(15);
        
        return response()->json([
            'status' => 'success',
            'data' => $pegawai
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_lengkap' => 'required|string|max:100',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable|in:Laki-laki,Perempuan',
            'alamat' => 'nullable|string',
            'telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'NIP' => 'nullable|string|max:20',
            'NIK' => 'nullable|string|max:16|unique:tb_pegawai,NIK',
            'id_posisi' => 'required|exists:tb_posisi,id_posisi',
            'agama' => 'nullable|string|max:20',
            'tanggal_masuk' => 'required|date',
            'id_user' => 'nullable|exists:tb_user,id_user', // Allow linking to existing user
            'create_user' => 'boolean',
            'password' => 'required_if:create_user,true|string|min:8|confirmed',
            'role' => 'required_if:create_user,true|in:admin,front office,kasir,dokter,beautician,pegawai',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Start transaction
        \DB::beginTransaction();
        
        try {
            $userId = $request->id_user; // Use existing user ID if provided
            
            // Create user if requested and no existing user provided
            if ($request->create_user && !$userId) {
                $user = User::create([
                    'nama_user' => $request->nama_lengkap,
                    'email' => $request->email,
                    'no_telp' => $request->telepon,
                    'password' => Hash::make($request->password),
                    'role' => $request->role,
                ]);
                
                $userId = $user->id_user;
            }
            
            // Create pegawai
            $pegawai = Pegawai::create([
                'id_user' => $userId,
                'nama_lengkap' => $request->nama_lengkap,
                'tanggal_lahir' => $request->tanggal_lahir,
                'jenis_kelamin' => $request->jenis_kelamin,
                'alamat' => $request->alamat,
                'telepon' => $request->telepon,
                'email' => $request->email,
                'NIP' => $request->NIP,
                'NIK' => $request->NIK,
                'id_posisi' => $request->id_posisi,
                'agama' => $request->agama,
                'tanggal_masuk' => $request->tanggal_masuk,
            ]);
            
            \DB::commit();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Pegawai berhasil ditambahkan',
                'data' => $pegawai->load(['user', 'posisi'])
            ], 201);
            
        } catch (\Exception $e) {
            \DB::rollback();
            
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menambahkan pegawai',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $pegawai = Pegawai::with(['user', 'posisi', 'absensi', 'gaji'])->find($id);
        
        if (!$pegawai) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pegawai tidak ditemukan'
            ], 404);
        }
        
        return response()->json([
            'status' => 'success',
            'data' => $pegawai
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $pegawai = Pegawai::find($id);
        
        if (!$pegawai) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pegawai tidak ditemukan'
            ], 404);
        }
        
        $validator = Validator::make($request->all(), [
            'nama_lengkap' => 'sometimes|required|string|max:100',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable|in:Laki-laki,Perempuan',
            'alamat' => 'nullable|string',
            'telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'NIP' => 'nullable|string|max:20',
            'NIK' => 'nullable|string|max:16|unique:tb_pegawai,NIK,' . $id . ',id_pegawai',
            'id_posisi' => 'sometimes|required|exists:tb_posisi,id_posisi',
            'agama' => 'nullable|string|max:20',
            'tanggal_masuk' => 'sometimes|required|date',
            'tanggal_keluar' => 'nullable|date',
            'update_user' => 'boolean',
            'role' => 'required_if:update_user,true|in:admin,front office,kasir,dokter,beautician',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Start transaction
        \DB::beginTransaction();
        
        try {
            // Update user if requested and if pegawai has a user
            if ($request->update_user && $pegawai->id_user) {
                $user = User::find($pegawai->id_user);
                
                if ($user) {
                    $userData = [];
                    
                    if ($request->has('nama_lengkap')) {
                        $userData['nama_user'] = $request->nama_lengkap;
                    }
                    
                    if ($request->has('email')) {
                        $userData['email'] = $request->email;
                    }
                    
                    if ($request->has('telepon')) {
                        $userData['no_telp'] = $request->telepon;
                    }
                    
                    if ($request->has('role')) {
                        $userData['role'] = $request->role;
                    }
                    
                    if (!empty($userData)) {
                        $user->update($userData);
                    }
                }
            }
            
            // Update pegawai
            $pegawai->update($request->only([
                'nama_lengkap',
                'tanggal_lahir',
                'jenis_kelamin',
                'alamat',
                'telepon',
                'email',
                'NIP',
                'NIK',
                'id_posisi',
                'agama',
                'tanggal_masuk',
                'tanggal_keluar',
            ]));
            
            \DB::commit();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Pegawai berhasil diperbarui',
                'data' => $pegawai->load(['user', 'posisi'])
            ]);
            
        } catch (\Exception $e) {
            \DB::rollback();
            
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat memperbarui pegawai',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $pegawai = Pegawai::find($id);
            
            if (!$pegawai) {
                return $this->errorResponse('Data pegawai tidak ditemukan', 404, []);
            }
            
            // Check if pegawai has related data
            if ($pegawai->absensi()->count() > 0 || $pegawai->gaji()->count() > 0) {
                return $this->errorResponse(
                    'Pegawai tidak dapat dihapus karena memiliki data terkait (absensi, gaji, dll)',
                    400,
                    []
                );
            }
            
            // Store data for response before deletion
            $responseData = [
                'id_pegawai' => $pegawai->id_pegawai,
                'nama_lengkap' => $pegawai->nama_lengkap,
                'nip' => $pegawai->NIP,
                'posisi' => $pegawai->posisi->nama_posisi ?? 'Unknown'
            ];
            
            // Start transaction
            \DB::beginTransaction();
            
            // Delete user if pegawai has a user
            if ($pegawai->id_user) {
                $user = User::find($pegawai->id_user);
                
                if ($user) {
                    $user->delete();
                }
            }
            
            // Delete pegawai
            $pegawai->delete();
            
            \DB::commit();
            
            return $this->successResponse(
                $responseData,
                'Data pegawai berhasil dihapus'
            );
            
        } catch (\Exception $e) {
            \DB::rollback();
            
            return $this->errorResponse(
                'Gagal menghapus data pegawai',
                500,
                ['error' => $e->getMessage()]
            );
        }
    }

    /**
     * Get pegawai data by user_id (for current logged in user)
     */
    public function getMyPegawaiData(Request $request)
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User tidak ditemukan atau tidak terautentikasi'
                ], 401);
            }
            
            // Find pegawai data where user_id matches current user
            $pegawai = Pegawai::with(['user', 'posisi'])
                ->where('id_user', $user->id_user)
                ->first();
            
            if (!$pegawai) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data pegawai tidak ditemukan untuk user ini',
                    'user_id' => $user->id_user,
                    'debug' => [
                        'user_data' => $user,
                        'search_field' => 'id_user',
                        'search_value' => $user->id_user
                    ]
                ], 404);
            }
            
            return response()->json([
                'status' => 'success',
                'message' => 'Data pegawai berhasil ditemukan',
                'data' => $pegawai
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengambil data pegawai',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
