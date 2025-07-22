<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LowonganPekerjaan;
use App\Models\Posisi;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class LowonganPekerjaanController extends Controller
{
    use ApiResponseTrait;
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
        try {
            // Authorization check
            $user = Auth::user();
            if (!$user || !$user->hasAdminPrivileges()) {
                Log::warning('Unauthorized delete attempt', [
                    'user_id' => $user ? $user->id : null,
                    'user_role' => $user ? $user->role : 'guest',
                    'lowongan_id' => $id,
                    'action' => 'soft_delete'
                ]);
                
                return $this->errorResponse(
                    'Forbidden. Admin or HRD access required for delete operations.',
                    403
                );
            }

            $lowongan = LowonganPekerjaan::find($id);
            
            if (!$lowongan) {
                Log::info('Delete attempt on non-existent lowongan', [
                    'user_id' => $user->id,
                    'lowongan_id' => $id,
                    'action' => 'soft_delete'
                ]);
                
                return $this->errorResponse('Lowongan pekerjaan tidak ditemukan', 404);
            }
            
            // Check if lowongan has related data
            $hasLamaran = $lowongan->lamaranPekerjaan()->count() > 0;
            $hasHasilSeleksi = $lowongan->hasilSeleksi()->count() > 0;
            
            if ($hasLamaran || $hasHasilSeleksi) {
                Log::info('Delete attempt blocked due to related data', [
                    'user_id' => $user->id,
                    'lowongan_id' => $id,
                    'judul_pekerjaan' => $lowongan->judul_pekerjaan,
                    'has_lamaran' => $hasLamaran,
                    'has_hasil_seleksi' => $hasHasilSeleksi,
                    'total_lamaran' => $lowongan->lamaranPekerjaan()->count(),
                    'total_hasil_seleksi' => $lowongan->hasilSeleksi()->count(),
                    'action' => 'soft_delete'
                ]);
                
                return $this->errorResponse(
                    'Lowongan pekerjaan tidak dapat dihapus karena memiliki data terkait (lamaran, hasil seleksi, dll). ' .
                    'Gunakan parameter force=true jika tetap ingin menghapus.',
                    400,
                    [
                        'has_lamaran' => $hasLamaran,
                        'has_hasil_seleksi' => $hasHasilSeleksi,
                        'total_lamaran' => $lowongan->lamaranPekerjaan()->count(),
                        'total_hasil_seleksi' => $lowongan->hasilSeleksi()->count()
                    ]
                );
            }
            
            // Store data sebelum dihapus untuk response dan logging
            $deletedData = [
                'id_lowongan' => $lowongan->id_lowongan,
                'judul_pekerjaan' => $lowongan->judul_pekerjaan,
                'posisi' => $lowongan->posisi->nama_posisi ?? 'Unknown',
                'status' => $lowongan->status,
                'deleted_at' => now()->format('Y-m-d H:i:s')
            ];
            
            $lowongan->delete();
            
            // Success logging
            Log::info('Lowongan pekerjaan soft deleted successfully', [
                'user_id' => $user->id,
                'user_role' => $user->role,
                'lowongan_data' => $deletedData,
                'action' => 'soft_delete'
            ]);
            
            return $this->successResponse(
                $deletedData,
                'Lowongan pekerjaan berhasil dihapus'
            );
            
        } catch (\Exception $e) {
            Log::error('Error during lowongan deletion', [
                'user_id' => Auth::id(),
                'lowongan_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'action' => 'soft_delete'
            ]);
            
            return $this->errorResponse(
                'Terjadi kesalahan saat menghapus lowongan pekerjaan: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Force delete lowongan pekerjaan beserta data terkait
     */
    public function forceDestroy(string $id)
    {
        try {
            // Authorization check
            $user = Auth::user();
            if (!$user || !$user->hasAdminPrivileges()) {
                Log::warning('Unauthorized force delete attempt', [
                    'user_id' => $user ? $user->id : null,
                    'user_role' => $user ? $user->role : 'guest',
                    'lowongan_id' => $id,
                    'action' => 'force_delete'
                ]);
                
                return $this->errorResponse(
                    'Forbidden. Admin or HRD access required for force delete operations.',
                    403
                );
            }

            $lowongan = LowonganPekerjaan::find($id);
            
            if (!$lowongan) {
                Log::info('Force delete attempt on non-existent lowongan', [
                    'user_id' => $user->id,
                    'lowongan_id' => $id,
                    'action' => 'force_delete'
                ]);
                
                return $this->errorResponse('Lowongan pekerjaan tidak ditemukan', 404);
            }
            
            // Count related data sebelum dihapus
            $relatedData = [
                'total_lamaran' => $lowongan->lamaranPekerjaan()->count(),
                'total_hasil_seleksi' => $lowongan->hasilSeleksi()->count(),
            ];
            
            // Store data sebelum dihapus untuk response dan logging
            $deletedData = [
                'id_lowongan' => $lowongan->id_lowongan,
                'judul_pekerjaan' => $lowongan->judul_pekerjaan,
                'posisi' => $lowongan->posisi->nama_posisi ?? 'Unknown',
                'status' => $lowongan->status,
                'related_data_deleted' => $relatedData,
                'deleted_at' => now()->format('Y-m-d H:i:s')
            ];
            
            Log::warning('Force delete initiated - will delete related data', [
                'user_id' => $user->id,
                'user_role' => $user->role,
                'lowongan_data' => $deletedData,
                'action' => 'force_delete'
            ]);
            
            // Delete related data first
            if ($lowongan->lamaranPekerjaan()->count() > 0) {
                $lowongan->lamaranPekerjaan()->delete();
            }
            
            if ($lowongan->hasilSeleksi()->count() > 0) {
                $lowongan->hasilSeleksi()->delete();
            }
            
            // Delete the lowongan itself
            $lowongan->delete();
            
            // Success logging
            Log::info('Lowongan pekerjaan force deleted successfully', [
                'user_id' => $user->id,
                'user_role' => $user->role,
                'lowongan_data' => $deletedData,
                'action' => 'force_delete'
            ]);
            
            return $this->successResponse(
                $deletedData,
                'Lowongan pekerjaan dan semua data terkait berhasil dihapus'
            );
            
        } catch (\Exception $e) {
            Log::error('Error during lowongan force deletion', [
                'user_id' => Auth::id(),
                'lowongan_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'action' => 'force_delete'
            ]);
            
            return $this->errorResponse(
                'Terjadi kesalahan saat menghapus lowongan pekerjaan: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Bulk delete multiple lowongan pekerjaan
     */
    public function bulkDestroy(Request $request)
    {
        // Authorization check
        $user = Auth::user();
        if (!$user || !$user->hasAdminPrivileges()) {
            Log::warning('Unauthorized bulk delete attempt', [
                'user_id' => $user ? $user->id : null,
                'user_role' => $user ? $user->role : 'guest',
                'requested_ids' => $request->input('ids', []),
                'action' => 'bulk_delete'
            ]);
            
            return $this->errorResponse(
                'Forbidden. Admin or HRD access required for bulk delete operations.',
                403
            );
        }

        $validator = Validator::make($request->all(), [
            'ids' => 'required|array|min:1',
            'ids.*' => 'required|integer|exists:tb_lowongan_pekerjaan,id_lowongan_pekerjaan',
            'force' => 'sometimes|boolean'
        ]);
        
        if ($validator->fails()) {
            Log::info('Bulk delete validation failed', [
                'user_id' => $user->id,
                'validation_errors' => $validator->errors(),
                'request_data' => $request->all(),
                'action' => 'bulk_delete'
            ]);
            
            return $this->errorResponse('Validation error', 422, $validator->errors());
        }
        
        try {
            $ids = $request->ids;
            $force = $request->input('force', false);
            $results = [
                'deleted' => [],
                'failed' => [],
                'skipped' => []
            ];
            
            Log::info('Bulk delete operation started', [
                'user_id' => $user->id,
                'user_role' => $user->role,
                'requested_ids' => $ids,
                'force_delete' => $force,
                'total_requested' => count($ids),
                'action' => 'bulk_delete'
            ]);
            
            foreach ($ids as $id) {
                $lowongan = LowonganPekerjaan::find($id);
                
                if (!$lowongan) {
                    $results['failed'][] = [
                        'id' => $id,
                        'reason' => 'Lowongan tidak ditemukan'
                    ];
                    continue;
                }
                
                // Check if has related data
                $hasRelatedData = $lowongan->lamaranPekerjaan()->count() > 0 || 
                                $lowongan->hasilSeleksi()->count() > 0;
                
                if ($hasRelatedData && !$force) {
                    $results['skipped'][] = [
                        'id' => $id,
                        'judul_pekerjaan' => $lowongan->judul_pekerjaan,
                        'reason' => 'Memiliki data terkait, gunakan force=true untuk menghapus'
                    ];
                    continue;
                }
                
                try {
                    if ($force && $hasRelatedData) {
                        // Delete related data first
                        $lowongan->lamaranPekerjaan()->delete();
                        $lowongan->hasilSeleksi()->delete();
                        
                        Log::info('Related data deleted during bulk force delete', [
                            'user_id' => $user->id,
                            'lowongan_id' => $id,
                            'judul_pekerjaan' => $lowongan->judul_pekerjaan,
                            'action' => 'bulk_delete'
                        ]);
                    }
                    
                    $lowongan->delete();
                    
                    $results['deleted'][] = [
                        'id' => $id,
                        'judul_pekerjaan' => $lowongan->judul_pekerjaan,
                        'deleted_at' => now()->format('Y-m-d H:i:s')
                    ];
                    
                } catch (\Exception $e) {
                    Log::error('Error deleting individual lowongan during bulk operation', [
                        'user_id' => $user->id,
                        'lowongan_id' => $id,
                        'error' => $e->getMessage(),
                        'action' => 'bulk_delete'
                    ]);
                    
                    $results['failed'][] = [
                        'id' => $id,
                        'reason' => $e->getMessage()
                    ];
                }
            }
            
            // Final logging
            Log::info('Bulk delete operation completed', [
                'user_id' => $user->id,
                'user_role' => $user->role,
                'summary' => [
                    'total_requested' => count($ids),
                    'deleted' => count($results['deleted']),
                    'failed' => count($results['failed']),
                    'skipped' => count($results['skipped'])
                ],
                'force_delete' => $force,
                'action' => 'bulk_delete'
            ]);
            
            return $this->successResponse(
                [
                    'summary' => [
                        'total_requested' => count($ids),
                        'deleted' => count($results['deleted']),
                        'failed' => count($results['failed']),
                        'skipped' => count($results['skipped'])
                    ],
                    'details' => $results
                ],
                'Bulk delete selesai'
            );
            
        } catch (\Exception $e) {
            Log::error('Critical error during bulk delete operation', [
                'user_id' => Auth::id(),
                'requested_ids' => $request->input('ids', []),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'action' => 'bulk_delete'
            ]);
            
            return $this->errorResponse(
                'Terjadi kesalahan saat bulk delete: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Public delete method for frontend integration (no auth required)
     */
    public function publicDestroy($id)
    {
        try {
            $lowongan = LowonganPekerjaan::find($id);
            
            if (!$lowongan) {
                return $this->errorResponse('Data lowongan tidak ditemukan', 404, []);
            }
            
            // Store data for response before deletion
            $responseData = [
                'id_lowongan' => $lowongan->id_lowongan,
                'judul_pekerjaan' => $lowongan->judul_pekerjaan,
                'posisi' => $lowongan->posisi->nama_posisi ?? 'Unknown',
                'status' => $lowongan->status
            ];
            
            $lowongan->delete();
            
            return $this->successResponse(
                $responseData,
                'Data lowongan berhasil dihapus'
            );
            
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Gagal menghapus data lowongan',
                500,
                ['error' => $e->getMessage()]
            );
        }
    }
}
