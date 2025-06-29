<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LowonganPekerjaan;
use App\Models\Posisi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LowonganPekerjaanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = LowonganPekerjaan::with(['posisi']);
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter by posisi
        if ($request->filled('id_posisi')) {
            $query->where('id_posisi', $request->id_posisi);
        }
        
        // Search by title
        if ($request->filled('search')) {
            $query->where('judul_pekerjaan', 'like', '%' . $request->search . '%');
        }
        
        // Filter active lowongan
        if ($request->has('active') && $request->active) {
            $query->where('status', 'aktif')
                  ->where('tanggal_selesai', '>=', now());
        }
        
        $lowongan = $query->orderBy('tanggal_mulai', 'desc')->paginate(15);
        
        return response()->json([
            'status' => 'success',
            'data' => $lowongan
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'judul_pekerjaan' => 'required|string|max:100',
            'id_posisi' => 'required|exists:tb_posisi,id_posisi',
            'jumlah_lowongan' => 'required|integer|min:1',
            'pengalaman_minimal' => 'nullable|string|max:50',
            'gaji_minimal' => 'nullable|numeric|min:0',
            'gaji_maksimal' => 'nullable|numeric|min:0',
            'status' => 'required|in:aktif,nonaktif',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'deskripsi' => 'nullable|string',
            'persyaratan' => 'nullable|string',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Ensure gaji_maksimal is greater than or equal to gaji_minimal
        if ($request->filled('gaji_minimal') && $request->filled('gaji_maksimal')) {
            if ($request->gaji_minimal > $request->gaji_maksimal) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Gaji maksimal harus lebih besar atau sama dengan gaji minimal'
                ], 422);
            }
        }
        
        $lowongan = LowonganPekerjaan::create($request->all());
        
        return response()->json([
            'status' => 'success',
            'message' => 'Lowongan pekerjaan berhasil ditambahkan',
            'data' => $lowongan->load('posisi')
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $lowongan = LowonganPekerjaan::with(['posisi', 'lamaranPekerjaan'])->find($id);
        
        if (!$lowongan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lowongan pekerjaan tidak ditemukan'
            ], 404);
        }
        
        return response()->json([
            'status' => 'success',
            'data' => $lowongan
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $lowongan = LowonganPekerjaan::find($id);
        
        if (!$lowongan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lowongan pekerjaan tidak ditemukan'
            ], 404);
        }
        
        $validator = Validator::make($request->all(), [
            'judul_pekerjaan' => 'sometimes|required|string|max:100',
            'id_posisi' => 'sometimes|required|exists:tb_posisi,id_posisi',
            'jumlah_lowongan' => 'sometimes|required|integer|min:1',
            'pengalaman_minimal' => 'nullable|string|max:50',
            'gaji_minimal' => 'nullable|numeric|min:0',
            'gaji_maksimal' => 'nullable|numeric|min:0',
            'status' => 'sometimes|required|in:aktif,nonaktif',
            'tanggal_mulai' => 'sometimes|required|date',
            'tanggal_selesai' => 'sometimes|required|date|after_or_equal:tanggal_mulai',
            'deskripsi' => 'nullable|string',
            'persyaratan' => 'nullable|string',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Ensure gaji_maksimal is greater than or equal to gaji_minimal
        if ($request->filled('gaji_minimal') && $request->filled('gaji_maksimal')) {
            if ($request->gaji_minimal > $request->gaji_maksimal) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Gaji maksimal harus lebih besar atau sama dengan gaji minimal'
                ], 422);
            }
        }
        
        $lowongan->update($request->all());
        
        return response()->json([
            'status' => 'success',
            'message' => 'Lowongan pekerjaan berhasil diperbarui',
            'data' => $lowongan->load('posisi')
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $lowongan = LowonganPekerjaan::find($id);
        
        if (!$lowongan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lowongan pekerjaan tidak ditemukan'
            ], 404);
        }
        
        // Check if lowongan has related data
        if ($lowongan->lamaranPekerjaan()->count() > 0 || $lowongan->hasilSeleksi()->count() > 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lowongan pekerjaan tidak dapat dihapus karena memiliki data terkait (lamaran, hasil seleksi, dll)'
            ], 400);
        }
        
        $lowongan->delete();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Lowongan pekerjaan berhasil dihapus'
        ]);
    }
}
