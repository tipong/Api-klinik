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
        
        // If user is authenticated, apply user-based filtering
        if ($user) {
            // Filter by user for non-admin roles
            if (!$user->isAdmin() && !$user->isHrd()) {
                $query->where('id_user', $user->id_user);
            } else {
                // For admin/HRD, allow filtering by specific user ID if provided
                if ($request->filled('id_user')) {
                    $query->where('id_user', $request->id_user);
                }
            }
        }
        // If no user is authenticated (public access), return all data

        // Allow filtering by id_user even if public
        if ($request->filled('id_user')) {
            $query->where('id_user', $request->id_user);
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
        
        // Add CV info to each lamaran without exposing binary data
        $lamaran->getCollection()->transform(function ($item) {
            $itemArray = $item->toArray();
            unset($itemArray['CV']);
            
            $fileInfo = $item->CV ? $this->detectFileType($item->CV) : null;
            
            $itemArray['cv_info'] = [
                'has_cv' => !empty($item->CV),
                'cv_size' => $item->CV ? strlen($item->CV) : 0,
                'cv_size_formatted' => $item->CV ? $this->formatBytes(strlen($item->CV)) : '0 bytes',
                'file_type' => $fileInfo ? $fileInfo['type'] : null,
                'file_extension' => $fileInfo ? $fileInfo['extension'] : null,
                'download_url' => !empty($item->CV) ? url("/api/lamaran/{$item->id_lamaran_pekerjaan}/download-cv") : null,
                'view_url' => !empty($item->CV) ? url("/api/lamaran/{$item->id_lamaran_pekerjaan}/view-cv") : null,
            ];
            
            return $itemArray;
        });
        
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
        
        // If user is authenticated, check if user is allowed to view this lamaran
        if ($user) {
            if (!$user->isAdmin() && !$user->isHrd() && $user->id_user !== $lamaran->id_user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda tidak memiliki akses untuk melihat lamaran ini'
                ], 403);
            }
        }
        // If no user is authenticated (public access), allow access to all data
        
        // Convert to array and don't return the CV binary in the API response
        $lamaranData = $lamaran->toArray();
        unset($lamaranData['CV']);
        
        // Add CV information without the binary data
        $fileInfo = $lamaran->CV ? $this->detectFileType($lamaran->CV) : null;
        
        $lamaranData['cv_info'] = [
            'has_cv' => !empty($lamaran->CV),
            'cv_size' => $lamaran->CV ? strlen($lamaran->CV) : 0,
            'cv_size_formatted' => $lamaran->CV ? $this->formatBytes(strlen($lamaran->CV)) : '0 bytes',
            'file_type' => $fileInfo ? $fileInfo['type'] : null,
            'file_extension' => $fileInfo ? $fileInfo['extension'] : null,
            'download_url' => !empty($lamaran->CV) ? url("/api/lamaran/{$id}/download-cv") : null,
            'view_url' => !empty($lamaran->CV) ? url("/api/lamaran/{$id}/view-cv") : null,
        ];
        
        return response()->json([
            'status' => 'success',
            'data' => $lamaranData
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
        // Allow public access, but if user is authenticated, apply role-based access control
        if ($user && !$user->isAdmin() && !$user->isHrd() && $user->id_user !== $lamaran->id_user) {
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
        
        $fileInfo = $this->detectFileType($lamaran->CV);
        $filename = 'CV_' . str_replace(' ', '_', $lamaran->nama_pelamar) . '.' . $fileInfo['extension'];
        
        return response($lamaran->CV)
            ->header('Content-Type', $fileInfo['type'])
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * View CV from application (inline preview)
     */
    public function viewCV(string $id)
    {
        $lamaran = LamaranPekerjaan::find($id);
        
        if (!$lamaran) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lamaran pekerjaan tidak ditemukan'
            ], 404);
        }
        
        $user = request()->user();
        
        // Check if user is allowed to view this CV
        // Allow public access, but if user is authenticated, apply role-based access control
        if ($user && !$user->isAdmin() && !$user->isHrd() && $user->id_user !== $lamaran->id_user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda tidak memiliki akses untuk melihat CV ini'
            ], 403);
        }
        
        if (!$lamaran->CV) {
            return response()->json([
                'status' => 'error',
                'message' => 'CV tidak ditemukan'
            ], 404);
        }
        
        $fileInfo = $this->detectFileType($lamaran->CV);
        $filename = 'CV_' . str_replace(' ', '_', $lamaran->nama_pelamar) . '.' . $fileInfo['extension'];
        
        return response($lamaran->CV)
            ->header('Content-Type', $fileInfo['type'])
            ->header('Content-Disposition', 'inline; filename="' . $filename . '"')
            ->header('Cache-Control', 'public, max-age=3600');
    }

    /**
     * Get CV info without downloading
     */
    public function getCVInfo(string $id)
    {
        $lamaran = LamaranPekerjaan::find($id);
        
        if (!$lamaran) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lamaran pekerjaan tidak ditemukan'
            ], 404);
        }
        
        $user = request()->user();
        
        // Check if user is allowed to access CV info
        // Allow public access, but if user is authenticated, apply role-based access control
        if ($user && !$user->isAdmin() && !$user->isHrd() && $user->id_user !== $lamaran->id_user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda tidak memiliki akses untuk melihat informasi CV ini'
            ], 403);
        }
        
        $fileInfo = $lamaran->CV ? $this->detectFileType($lamaran->CV) : null;
        
        $cvInfo = [
            'has_cv' => !empty($lamaran->CV),
            'cv_size' => $lamaran->CV ? strlen($lamaran->CV) : 0,
            'cv_size_formatted' => $lamaran->CV ? $this->formatBytes(strlen($lamaran->CV)) : '0 bytes',
            'file_type' => $fileInfo ? $fileInfo['type'] : null,
            'file_extension' => $fileInfo ? $fileInfo['extension'] : null,
            'nama_pelamar' => $lamaran->nama_pelamar,
            'download_url' => !empty($lamaran->CV) ? url("/api/lamaran/{$id}/download-cv") : null,
            'view_url' => !empty($lamaran->CV) ? url("/api/lamaran/{$id}/view-cv") : null,
            'uploaded_at' => $lamaran->created_at
        ];
        
        return response()->json([
            'status' => 'success',
            'data' => $cvInfo
        ]);
    }

    /**
     * Detect file type from binary content
     */
    private function detectFileType($binaryContent)
    {
        // PDF signature
        if (substr($binaryContent, 0, 4) === '%PDF') {
            return ['type' => 'application/pdf', 'extension' => 'pdf'];
        }
        
        // DOC signature (MS Office)
        if (substr($binaryContent, 0, 8) === "\xD0\xCF\x11\xE0\xA1\xB1\x1A\xE1") {
            return ['type' => 'application/msword', 'extension' => 'doc'];
        }
        
        // DOCX signature (ZIP file starting with PK)
        if (substr($binaryContent, 0, 2) === 'PK') {
            // Further check for DOCX by looking for word/ directory
            if (strpos($binaryContent, 'word/') !== false) {
                return ['type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'extension' => 'docx'];
            }
        }
        
        // Default to PDF if detection fails
        return ['type' => 'application/pdf', 'extension' => 'pdf'];
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }

    /**
     * Schedule interview for accepted application
     */
    public function scheduleInterview(Request $request, string $id)
    {
        $lamaran = LamaranPekerjaan::find($id);
        
        if (!$lamaran) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lamaran pekerjaan tidak ditemukan'
            ], 404);
        }
        
        $user = $request->user();
        
        // Only admin or HRD can schedule interview
        if (!$user->isAdmin() && !$user->isHrd()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda tidak memiliki akses untuk menjadwalkan wawancara'
            ], 403);
        }
        
        // Check if application status is diterima (accepted)
        if ($lamaran->status !== 'diterima') {
            return response()->json([
                'status' => 'error',
                'message' => 'Hanya lamaran yang diterima yang bisa dijadwalkan wawancara'
            ], 400);
        }
        
        $validator = Validator::make($request->all(), [
            'tanggal_wawancara' => 'required|date|after:today',
            'lokasi_wawancara' => 'required|string|max:255',
            'catatan' => 'nullable|string|max:1000'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Check if interview already exists for this application
        $existingInterview = \App\Models\Wawancara::where('id_lamaran_pekerjaan', $id)->first();
        
        if ($existingInterview) {
            return response()->json([
                'status' => 'error',
                'message' => 'Wawancara sudah dijadwalkan untuk lamaran ini'
            ], 400);
        }
        
        // Create new interview record
        $wawancara = \App\Models\Wawancara::create([
            'id_lamaran_pekerjaan' => $id,
            'id_user' => $lamaran->id_user,
            'tanggal_wawancara' => $request->tanggal_wawancara,
            'lokasi_wawancara' => $request->lokasi_wawancara,
            'status_wawancara' => 'dijadwalkan',
            'catatan' => $request->catatan,
        ]);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Wawancara berhasil dijadwalkan',
            'data' => $wawancara->load(['lamaranPekerjaan.lowonganPekerjaan.posisi', 'user'])
        ], 201);
    }
}
