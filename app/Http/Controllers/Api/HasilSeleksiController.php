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
        $query = HasilSeleksi::with(['user', 'lamaranPekerjaan.lowonganPekerjaan.posisi']);
        
        // Filter by specific lamaran ID if provided
        if ($request->filled('id_hasil_seleksi')) {
            $query->where('id_hasil_seleksi', $request->id_hasil_seleksi);
        }
        
        // If user is authenticated, apply user-based filtering
        if ($user) {
            // Filter by user for non-admin roles
            if (!$user->isAdmin() && !$user->isHrd()) {
                $query->where('id_user', $user->id_user);
            }
            
            // Filter by user (admin/HRD can filter by specific user)
            if (($user->isAdmin() || $user->isHrd()) && $request->filled('id_user')) {
                $query->where('id_user', $request->id_user);
            }
        }
        // If no user is authenticated (public access), return all data
        
        // Filter by lamaran pekerjaan
        if ($request->filled('id_lamaran_pekerjaan')) {
            $query->where('id_lamaran_pekerjaan', $request->id_lamaran_pekerjaan);
        }
        
        // Filter by lowongan pekerjaan (through lamaran_pekerjaan relationship)
        if ($request->filled('id_lowongan_pekerjaan')) {
            $query->whereHas('lamaranPekerjaan', function ($q) use ($request) {
                $q->where('id_lowongan_pekerjaan', $request->id_lowongan_pekerjaan);
            });
        }
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $hasilSeleksi = $query->orderBy('created_at', 'desc')->paginate(15);
        
        return $this->successResponse($hasilSeleksi, 'Data hasil seleksi berhasil diambil');
    }


    public function getByUser(Request $request, $id_user)
    {
        $currentUser = $request->user();

        // If user is authenticated, check access permissions
        if ($currentUser) {
            if (! $currentUser->isAdmin() 
                && ! $currentUser->isHrd() 
                && $currentUser->id_user != $id_user) {
                return $this->errorResponse('Anda tidak memiliki akses untuk melihat data ini', 403);
            }
        }
        // If no user is authenticated (public access), allow access to all data

        $query = HasilSeleksi::with(['user', 'lamaranPekerjaan.lowonganPekerjaan.posisi'])
            ->where('id_user', $id_user)
            ->orderBy('created_at', 'desc');

        $perPage = $request->get('per_page', 15);
        $hasilSeleksi = $query->paginate($perPage);

        return $this->successResponse(
            $hasilSeleksi,
            "Data hasil seleksi untuk user dengan id {$id_user} berhasil diambil"
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            \Log::info('HasilSeleksi store request received', [
                'request_data' => $request->all(),
                'headers' => $request->headers->all()
            ]);
            
            $validator = Validator::make($request->all(), [
                'id_user' => 'required|exists:tb_user,id_user',
                'id_lamaran_pekerjaan' => 'required|exists:tb_lamaran_pekerjaan,id_lamaran_pekerjaan',
                'status' => 'required|in:pending,diterima,ditolak',
                'catatan' => 'nullable|string|max:500',
            ]);
            
            if ($validator->fails()) {
                \Log::warning('HasilSeleksi store validation failed', [
                    'errors' => $validator->errors()->toArray()
                ]);
                return $this->validationErrorResponse($validator->errors());
            }
            
            // Check if hasil seleksi for this user and lamaran already exists
            $existingHasil = HasilSeleksi::where('id_user', $request->id_user)
                                      ->where('id_lamaran_pekerjaan', $request->id_lamaran_pekerjaan)
                                      ->first();
                                      
            if ($existingHasil) {
                \Log::warning('HasilSeleksi already exists, attempting to update', [
                    'existing_id' => $existingHasil->id_hasil_seleksi,
                    'existing_status' => $existingHasil->status,
                    'new_status' => $request->status
                ]);
                
                // Update existing record instead of throwing error
                $existingHasil->update([
                    'status' => $request->status,
                    'catatan' => $request->catatan,
                ]);
                
                \Log::info('HasilSeleksi updated successfully', [
                    'id' => $existingHasil->id_hasil_seleksi,
                    'new_status' => $existingHasil->status
                ]);
                
                return $this->successResponse(
                    $existingHasil->load(['user', 'lamaranPekerjaan.lowonganPekerjaan.posisi']),
                    'Hasil seleksi berhasil diperbarui',
                    200
                );
            }
            
            $hasilSeleksi = HasilSeleksi::create($request->all());
            
            \Log::info('HasilSeleksi created successfully', [
                'id' => $hasilSeleksi->id_hasil_seleksi,
                'status' => $hasilSeleksi->status
            ]);
            
            return $this->successResponse(
                $hasilSeleksi->load(['user', 'lamaranPekerjaan.lowonganPekerjaan.posisi']),
                'Hasil seleksi berhasil ditambahkan',
                201
            );
            
        } catch (\Exception $e) {
            \Log::error('HasilSeleksi store failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $this->serverErrorResponse('Gagal menambahkan hasil seleksi: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $hasilSeleksi = HasilSeleksi::with(['user', 'lamaranPekerjaan.lowonganPekerjaan.posisi'])->find($id);
        
        if (!$hasilSeleksi) {
            return response()->json([
                'status' => 'error',
                'message' => 'Hasil seleksi tidak ditemukan'
            ], 404);
        }
        
        $user = request()->user();
        
        // If user is authenticated, check if user is allowed to view this result
        if ($user) {
            if (!$user->isAdmin() && !$user->isHrd() && $user->id_user !== $hasilSeleksi->id_user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda tidak memiliki akses untuk melihat hasil seleksi ini'
                ], 403);
            }
        }
        // If no user is authenticated (public access), allow access to all data
        
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
        \Log::info('HasilSeleksi update request received', [
            'id' => $id,
            'request_data' => $request->all()
        ]);
        
        $hasilSeleksi = HasilSeleksi::find($id);
        
        if (!$hasilSeleksi) {
            \Log::warning('HasilSeleksi not found for update', ['id' => $id]);
            return response()->json([
                'status' => 'error',
                'message' => 'Hasil seleksi tidak ditemukan'
            ], 404);
        }
        
        \Log::info('Found existing HasilSeleksi', [
            'id' => $hasilSeleksi->id_hasil_seleksi,
            'current_status' => $hasilSeleksi->status,
            'user_id' => $hasilSeleksi->id_user,
            'lamaran_id' => $hasilSeleksi->id_lamaran_pekerjaan
        ]);
        
        $user = $request->user();
        
        // If user is authenticated, only admin or HRD can update results
        if ($user) {
            if (!$user->isAdmin() && !$user->isHrd()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda tidak memiliki akses untuk memperbarui hasil seleksi ini'
                ], 403);
            }
        }
        // If no user is authenticated (public access), allow updates
        
        $validator = Validator::make($request->all(), [
            'status' => 'sometimes|required|in:diterima,ditolak,pending',
            'catatan' => 'nullable|string',
        ]);
        
        if ($validator->fails()) {
            \Log::warning('HasilSeleksi update validation failed', [
                'errors' => $validator->errors()->toArray()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $updateData = $request->only(['status', 'catatan']);
        
        \Log::info('Updating HasilSeleksi', [
            'id' => $id,
            'update_data' => $updateData,
            'old_status' => $hasilSeleksi->status
        ]);
        
        $hasilSeleksi->update($updateData);
        
        \Log::info('HasilSeleksi updated successfully', [
            'id' => $hasilSeleksi->id_hasil_seleksi,
            'new_status' => $hasilSeleksi->status,
            'old_status' => $hasilSeleksi->getOriginal('status')
        ]);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Hasil seleksi berhasil diperbarui',
            'data' => $hasilSeleksi->load(['user', 'lamaranPekerjaan.lowonganPekerjaan.posisi'])
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
        
        // If user is authenticated, only admin or HRD can delete results
        if ($user) {
            if (!$user->isAdmin() && !$user->isHrd()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda tidak memiliki akses untuk menghapus hasil seleksi ini'
                ], 403);
            }
        }
        // If no user is authenticated (public access), allow deletion
        
        $hasilSeleksi->delete();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Hasil seleksi berhasil dihapus'
        ]);
    }
}
