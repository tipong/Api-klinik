<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LamaranPekerjaan;
use App\Models\LowonganPekerjaan;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class LamaranPekerjaanController extends Controller
{
    use ApiResponseTrait;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $query = LamaranPekerjaan::with(['lowonganPekerjaan.posisi', 'user']);
        
        // Filter by user for non-admin roles
        if (!$user->isAdmin() && !$user->isHrd()) {
            $query->where('id_user', $user->id_user);
        } else {
            // For admin/HRD, allow filtering by specific user ID if provided
            if ($request->filled('id_user')) {
                $query->where('id_user', $request->id_user);
            }
        }
        
        // Filter by lowongan
        if ($request->filled('id_lowongan_pekerjaan')) {
            $query->where('id_lowongan_pekerjaan', $request->id_lowongan_pekerjaan);
        }
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Search by name
        if ($request->filled('search')) {
            $query->where('nama_pelamar', 'like', '%' . $request->search . '%');
        }
        
        $lamaran = $query->orderBy('created_at', 'desc')->paginate(15);
        
        return response()->json([
            'status' => 'success',
            'data' => $lamaran
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_lowongan_pekerjaan' => 'required|exists:tb_lowongan_pekerjaan,id_lowongan_pekerjaan',
            'nama_pelamar' => 'required|string|max:100',
            'email_pelamar' => 'required|email|max:100',
            'NIK_pelamar' => 'nullable|string|max:16',
            'telepon_pelamar' => 'required|string|max:20',
            'alamat_pelamar' => 'required|string',
            'pendidikan_terakhir' => 'required|string|max:50',
            'cv' => 'required|file|mimes:pdf,doc,docx|max:2048',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Check if lowongan is still active
        $lowongan = LowonganPekerjaan::find($request->id_lowongan_pekerjaan);
        if (!$lowongan || $lowongan->status !== 'aktif' || $lowongan->isExpired()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lowongan pekerjaan tidak aktif atau sudah berakhir'
            ], 400);
        }
        
        $user = $request->user();
        
        // Check if user already applied for this job
        $existingLamaran = LamaranPekerjaan::where('id_user', $user->id_user)
                                          ->where('id_lowongan_pekerjaan', $request->id_lowongan_pekerjaan)
                                          ->first();
                                          
        if ($existingLamaran) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda sudah melamar untuk lowongan pekerjaan ini'
            ], 400);
        }
        
        // Handle CV upload
        if ($request->hasFile('cv')) {
            $cvFile = $request->file('cv');
            $cvContent = file_get_contents($cvFile->getRealPath());
        } else {
            $cvContent = null;
        }
        
        $lamaran = LamaranPekerjaan::create([
            'id_lowongan_pekerjaan' => $request->id_lowongan_pekerjaan,
            'id_user' => $user->id_user,
            'nama_pelamar' => $request->nama_pelamar,
            'email_pelamar' => $request->email_pelamar,
            'NIK_pelamar' => $request->NIK_pelamar,
            'telepon_pelamar' => $request->telepon_pelamar,
            'alamat_pelamar' => $request->alamat_pelamar,
            'pendidikan_terakhir' => $request->pendidikan_terakhir,
            'CV' => $cvContent,
            'status' => 'pending',
        ]);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Lamaran pekerjaan berhasil dikirim',
            'data' => $lamaran->load('lowonganPekerjaan.posisi')
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $lamaran = LamaranPekerjaan::with(['lowonganPekerjaan.posisi', 'user', 'wawancara'])->find($id);
        
        if (!$lamaran) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lamaran pekerjaan tidak ditemukan'
            ], 404);
        }
        
        $user = request()->user();
        
        // Check if user is allowed to view this lamaran
        if (!$user->isAdmin() && !$user->isHrd() && $user->id_user !== $lamaran->id_user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda tidak memiliki akses untuk melihat lamaran ini'
            ], 403);
        }
        
        // Don't return the CV binary in the API response
        $lamaran = $lamaran->toArray();
        unset($lamaran['CV']);
        
        return response()->json([
            'status' => 'success',
            'data' => $lamaran
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $lamaran = LamaranPekerjaan::find($id);
        
        if (!$lamaran) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lamaran pekerjaan tidak ditemukan'
            ], 404);
        }
        
        $user = $request->user();
        
        // Only admin or HRD can update applications
        if (!$user->isAdmin() && !$user->isHrd()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda tidak memiliki akses untuk memperbarui lamaran ini'
            ], 403);
        }
        
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,diterima,ditolak',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $lamaran->update($request->only([
            'status',
        ]));
        
        return response()->json([
            'status' => 'success',
            'message' => 'Status lamaran pekerjaan berhasil diperbarui',
            'data' => $lamaran->load(['lowonganPekerjaan.posisi', 'user'])
        ]);
    }

    /**
     * Download CV from application
     */
    public function downloadCV(string $id)
    {
        $lamaran = LamaranPekerjaan::find($id);
        
        if (!$lamaran) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lamaran pekerjaan tidak ditemukan'
            ], 404);
        }
        
        $user = request()->user();
        
        // Check if user is allowed to download this CV
        if (!$user->isAdmin() && !$user->isHrd() && $user->id_user !== $lamaran->id_user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda tidak memiliki akses untuk mengunduh CV ini'
            ], 403);
        }
        
        if (!$lamaran->CV) {
            return response()->json([
                'status' => 'error',
                'message' => 'CV tidak ditemukan'
            ], 404);
        }
        
        $filename = 'CV_' . str_replace(' ', '_', $lamaran->nama_pelamar) . '.pdf';
        
        return response($lamaran->CV)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}
