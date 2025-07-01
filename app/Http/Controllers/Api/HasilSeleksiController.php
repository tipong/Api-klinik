<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HasilSeleksi;
use App\Models\User;
use App\Models\LowonganPekerjaan;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HasilSeleksiController extends Controller
{
    use ApiResponseTrait;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $query = HasilSeleksi::with(['user', 'lowonganPekerjaan.posisi']);
        
        // Filter by user for non-admin roles
        if (!$user->isAdmin() && !$user->isHrd()) {
            $query->where('id_user', $user->id_user);
        }
        
        // Filter by lowongan
        if ($request->filled('id_lowongan_pekerjaan')) {
            $query->where('id_lowongan_pekerjaan', $request->id_lowongan_pekerjaan);
        }
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter by user
        if (($user->isAdmin() || $user->isHrd()) && $request->filled('id_user')) {
            $query->where('id_user', $request->id_user);
        }
        
        $hasilSeleksi = $query->orderBy('created_at', 'desc')->paginate(15);
        
        return $this->successResponse($hasilSeleksi, 'Data hasil seleksi berhasil diambil');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id_user' => 'required|exists:tb_user,id_user',
                'id_lowongan_pekerjaan' => 'required|exists:tb_lowongan_pekerjaan,id_lowongan_pekerjaan',
                'status' => 'required|in:pending,diterima,ditolak',
                'catatan' => 'nullable|string|max:500',
            ]);
            
            if ($validator->fails()) {
                return $this->validationErrorResponse($validator->errors());
            }
            
            // Check if hasil seleksi for this user and lowongan already exists
            $existingHasil = HasilSeleksi::where('id_user', $request->id_user)
                                      ->where('id_lowongan_pekerjaan', $request->id_lowongan_pekerjaan)
                                      ->first();
                                      
            if ($existingHasil) {
                return $this->errorResponse('Hasil seleksi untuk user dan lowongan ini sudah ada', 400);
            }
            
            $hasilSeleksi = HasilSeleksi::create($request->all());
            
            return $this->successResponse(
                $hasilSeleksi->load(['user', 'lowonganPekerjaan.posisi']),
                'Hasil seleksi berhasil ditambahkan',
                201
            );
            
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Gagal menambahkan hasil seleksi: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $hasilSeleksi = HasilSeleksi::with(['user', 'lowonganPekerjaan.posisi'])->find($id);
        
        if (!$hasilSeleksi) {
            return response()->json([
                'status' => 'error',
                'message' => 'Hasil seleksi tidak ditemukan'
            ], 404);
        }
        
        $user = request()->user();
        
        // Check if user is allowed to view this result
        if (!$user->isAdmin() && !$user->isHrd() && $user->id_user !== $hasilSeleksi->id_user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda tidak memiliki akses untuk melihat hasil seleksi ini'
            ], 403);
        }
        
        return response()->json([
            'status' => 'success',
            'data' => $hasilSeleksi
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $hasilSeleksi = HasilSeleksi::find($id);
        
        if (!$hasilSeleksi) {
            return response()->json([
                'status' => 'error',
                'message' => 'Hasil seleksi tidak ditemukan'
            ], 404);
        }
        
        $user = $request->user();
        
        // Only admin or HRD can update results
        if (!$user->isAdmin() && !$user->isHrd()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda tidak memiliki akses untuk memperbarui hasil seleksi ini'
            ], 403);
        }
        
        $validator = Validator::make($request->all(), [
            'status' => 'sometimes|required|in:diterima,ditolak,pending',
            'catatan' => 'nullable|string',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $hasilSeleksi->update($request->only([
            'status',
            'catatan',
        ]));
        
        return response()->json([
            'status' => 'success',
            'message' => 'Hasil seleksi berhasil diperbarui',
            'data' => $hasilSeleksi->load(['user', 'lowonganPekerjaan.posisi'])
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $hasilSeleksi = HasilSeleksi::find($id);
        
        if (!$hasilSeleksi) {
            return response()->json([
                'status' => 'error',
                'message' => 'Hasil seleksi tidak ditemukan'
            ], 404);
        }
        
        $user = request()->user();
        
        // Only admin or HRD can delete results
        if (!$user->isAdmin() && !$user->isHrd()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda tidak memiliki akses untuk menghapus hasil seleksi ini'
            ], 403);
        }
        
        $hasilSeleksi->delete();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Hasil seleksi berhasil dihapus'
        ]);
    }
}
