<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MasterGajiController extends Controller
{
    /**
     * Display a listing of pegawai with their salary information.
     */
    public function index(Request $request)
    {
        try {
            $query = Pegawai::with(['posisi', 'user']);
            
            // Filter by name
            if ($request->filled('nama')) {
                $query->where('nama_lengkap', 'like', '%' . $request->nama . '%');
            }
            
            // Filter by posisi
            if ($request->filled('id_posisi')) {
                $query->where('id_posisi', $request->id_posisi);
            }
            
            // Filter by active status
            if ($request->filled('status')) {
                if ($request->status === 'aktif') {
                    $query->whereNull('tanggal_keluar');
                } else {
                    $query->whereNotNull('tanggal_keluar');
                }
            }
            
            $pegawais = $query->paginate($request->per_page ?? 15);
            
            // Transform data untuk menampilkan informasi gaji
            $pegawais->getCollection()->transform(function ($pegawai) {
                return [
                    'id_pegawai' => $pegawai->id_pegawai,
                    'nama_lengkap' => $pegawai->nama_lengkap,
                    'NIP' => $pegawai->NIP,
                    'posisi' => $pegawai->posisi ? [
                        'id_posisi' => $pegawai->posisi->id_posisi,
                        'nama_posisi' => $pegawai->posisi->nama_posisi,
                        'gaji_pokok_default' => $pegawai->posisi->gaji_pokok,
                        'persen_bonus' => $pegawai->posisi->persen_bonus,
                        'gaji_absensi' => $pegawai->posisi->gaji_absensi,
                    ] : null,
                    'gaji_pokok_tambahan' => $pegawai->gaji_pokok_tambahan,
                    'gaji_pokok_efektif' => $pegawai->getGajiPokokEfektif(),
                    'has_custom_salary' => $pegawai->hasCustomBasicSalary(),
                    'status_pegawai' => $pegawai->tanggal_keluar ? 'non_aktif' : 'aktif',
                    'tanggal_masuk' => $pegawai->tanggal_masuk?->format('Y-m-d'),
                    'tanggal_keluar' => $pegawai->tanggal_keluar?->format('Y-m-d'),
                ];
            });
            
            return response()->json([
                'status' => 'success',
                'message' => 'Data master gaji pegawai berhasil diambil',
                'data' => $pegawais
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting master gaji pegawai: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data master gaji pegawai',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
    
    /**
     * Show the specified pegawai salary information.
     */
    public function show($id)
    {
        try {
            $pegawai = Pegawai::with(['posisi', 'user'])->findOrFail($id);
            
            $data = [
                'id_pegawai' => $pegawai->id_pegawai,
                'nama_lengkap' => $pegawai->nama_lengkap,
                'NIP' => $pegawai->NIP,
                'email' => $pegawai->email,
                'telepon' => $pegawai->telepon,
                'posisi' => $pegawai->posisi ? [
                    'id_posisi' => $pegawai->posisi->id_posisi,
                    'nama_posisi' => $pegawai->posisi->nama_posisi,
                    'gaji_pokok_default' => $pegawai->posisi->gaji_pokok,
                    'persen_bonus' => $pegawai->posisi->persen_bonus,
                    'gaji_absensi' => $pegawai->posisi->gaji_absensi,
                ] : null,
                'gaji_pokok_tambahan' => $pegawai->gaji_pokok_tambahan,
                'gaji_pokok_efektif' => $pegawai->getGajiPokokEfektif(),
                'has_custom_salary' => $pegawai->hasCustomBasicSalary(),
                'status_pegawai' => $pegawai->tanggal_keluar ? 'non_aktif' : 'aktif',
                'tanggal_masuk' => $pegawai->tanggal_masuk?->format('Y-m-d'),
                'tanggal_keluar' => $pegawai->tanggal_keluar?->format('Y-m-d'),
            ];
            
            return response()->json([
                'status' => 'success',
                'message' => 'Data master gaji pegawai berhasil diambil',
                'data' => $data
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting pegawai salary info: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Pegawai tidak ditemukan',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 404);
        }
    }
    
    /**
     * Update the specified pegawai custom salary.
     */
    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'gaji_pokok_tambahan' => 'required|numeric|min:0',
                'persen_bonus' => 'nullable|numeric|min:0|max:100',
                'gaji_absensi' => 'nullable|numeric|min:0',
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            $pegawai = Pegawai::findOrFail($id);
            
            DB::beginTransaction();
            
            // Update gaji_pokok_tambahan di pegawai
            $pegawai->update([
                'gaji_pokok_tambahan' => $request->gaji_pokok_tambahan
            ]);
            
            // Update persen_bonus dan gaji_absensi di posisi jika disediakan
            if ($request->has('persen_bonus') || $request->has('gaji_absensi')) {
                $posisi = $pegawai->posisi;
                if ($posisi) {
                    $posisiUpdates = [];
                    if ($request->has('persen_bonus')) {
                        $posisiUpdates['persen_bonus'] = $request->persen_bonus;
                    }
                    if ($request->has('gaji_absensi')) {
                        $posisiUpdates['gaji_absensi'] = $request->gaji_absensi;
                    }
                    $posisi->update($posisiUpdates);
                }
            }
            
            DB::commit();
            
            // Load relations untuk response
            $pegawai->load(['posisi', 'user']);
            
            $data = [
                'id_pegawai' => $pegawai->id_pegawai,
                'nama_lengkap' => $pegawai->nama_lengkap,
                'gaji_pokok_tambahan' => $pegawai->gaji_pokok_tambahan,
                'gaji_pokok_efektif' => $pegawai->getGajiPokokEfektif(),
                'has_custom_salary' => $pegawai->hasCustomBasicSalary(),
                'posisi' => $pegawai->posisi ? [
                    'nama_posisi' => $pegawai->posisi->nama_posisi,
                    'gaji_pokok_default' => $pegawai->posisi->gaji_pokok,
                    'persen_bonus' => $pegawai->posisi->persen_bonus,
                    'gaji_absensi' => $pegawai->posisi->gaji_absensi,
                ] : null,
            ];
            
            return response()->json([
                'status' => 'success',
                'message' => 'Data gaji pegawai berhasil diperbarui',
                'data' => $data
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating pegawai salary data: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memperbarui data gaji pegawai',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
    
    /**
     * Reset pegawai custom salary (set to 0).
     */
    public function resetCustomSalary($id)
    {
        try {
            $pegawai = Pegawai::findOrFail($id);
            
            DB::beginTransaction();
            
            $pegawai->update([
                'gaji_pokok_tambahan' => 0
            ]);
            
            DB::commit();
            
            // Load relations untuk response
            $pegawai->load(['posisi', 'user']);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Gaji pokok tambahan pegawai berhasil direset ke default posisi',
                'data' => [
                    'id_pegawai' => $pegawai->id_pegawai,
                    'nama_lengkap' => $pegawai->nama_lengkap,
                    'gaji_pokok_tambahan' => $pegawai->gaji_pokok_tambahan,
                    'gaji_pokok_efektif' => $pegawai->getGajiPokokEfektif(),
                    'has_custom_salary' => $pegawai->hasCustomBasicSalary(),
                ]
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error resetting pegawai custom salary: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mereset gaji pokok tambahan pegawai',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}
