<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Gaji;
use App\Models\Pegawai;
use App\Models\Absensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class GajiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Gaji::with(['pegawai.user', 'pegawai.posisi']);
        
        // Filter by user for non-admin roles
        if (!$user->isAdmin() && !$user->isHrd()) {
            $pegawai = $user->pegawai;
            if ($pegawai) {
                $query->where('id_pegawai', $pegawai->id_pegawai);
            } else {
                // If user has no pegawai record, show empty result
                $query->whereRaw('1 = 0');
            }
        }
        
        // Filter by year
        if ($request->filled('tahun')) {
            $query->where('periode_tahun', $request->tahun);
        }
        
        // Filter by month
        if ($request->filled('bulan')) {
            $query->where('periode_bulan', $request->bulan);
        }
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter by pegawai
        if (($user->isAdmin() || $user->isHrd()) && $request->filled('id_pegawai')) {
            $query->where('id_pegawai', $request->id_pegawai);
        }
        
        $gaji = $query->orderBy('periode_tahun', 'desc')
                      ->orderBy('periode_bulan', 'desc')
                      ->paginate(15);
        
        return response()->json([
            'status' => 'success',
            'data' => $gaji
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_pegawai' => 'required|exists:tb_pegawai,id_pegawai',
            'periode_bulan' => 'required|integer|between:1,12',
            'periode_tahun' => 'required|integer|min:2000',
            'gaji_pokok' => 'required|numeric|min:0',
            'gaji_bonus' => 'nullable|numeric|min:0',
            'gaji_kehadiran' => 'nullable|numeric|min:0',
            'tanggal_pembayaran' => 'nullable|date',
            'status' => 'required|in:Terbayar,Belum Terbayar',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Check if gaji for this pegawai and period already exists
        $existingGaji = Gaji::where('id_pegawai', $request->id_pegawai)
                          ->where('periode_bulan', $request->periode_bulan)
                          ->where('periode_tahun', $request->periode_tahun)
                          ->first();
                          
        if ($existingGaji) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gaji untuk pegawai dan periode ini sudah ada'
            ], 400);
        }
        
        // Calculate total
        $gajiPokok = $request->gaji_pokok ?? 0;
        $gajiBonus = $request->gaji_bonus ?? 0;
        $gajiKehadiran = $request->gaji_kehadiran ?? 0;
        $gajiTotal = $gajiPokok + $gajiBonus + $gajiKehadiran;
        
        $gaji = Gaji::create([
            'id_pegawai' => $request->id_pegawai,
            'periode_bulan' => $request->periode_bulan,
            'periode_tahun' => $request->periode_tahun,
            'gaji_pokok' => $gajiPokok,
            'gaji_bonus' => $gajiBonus,
            'gaji_kehadiran' => $gajiKehadiran,
            'gaji_total' => $gajiTotal,
            'tanggal_pembayaran' => $request->tanggal_pembayaran,
            'status' => $request->status,
        ]);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Gaji berhasil ditambahkan',
            'data' => $gaji->load(['pegawai.user', 'pegawai.posisi'])
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $gaji = Gaji::with(['pegawai.user', 'pegawai.posisi'])->find($id);
        
        if (!$gaji) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gaji tidak ditemukan'
            ], 404);
        }
        
        $user = request()->user();
        
        // Check if user is allowed to view this gaji
        if (!$user->isAdmin() && !$user->isHrd() && $user->pegawai && $user->pegawai->id_pegawai !== $gaji->id_pegawai) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda tidak memiliki akses untuk melihat data gaji ini'
            ], 403);
        }
        
        return response()->json([
            'status' => 'success',
            'data' => $gaji
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $gaji = Gaji::find($id);
        
        if (!$gaji) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gaji tidak ditemukan'
            ], 404);
        }
        
        $validator = Validator::make($request->all(), [
            'gaji_pokok' => 'sometimes|required|numeric|min:0',
            'gaji_bonus' => 'nullable|numeric|min:0',
            'gaji_kehadiran' => 'nullable|numeric|min:0',
            'tanggal_pembayaran' => 'nullable|date',
            'status' => 'sometimes|required|in:Terbayar,Belum Terbayar',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Calculate total if needed
        if ($request->has('gaji_pokok') || $request->has('gaji_bonus') || $request->has('gaji_kehadiran')) {
            $gajiPokok = $request->has('gaji_pokok') ? $request->gaji_pokok : $gaji->gaji_pokok;
            $gajiBonus = $request->has('gaji_bonus') ? $request->gaji_bonus : $gaji->gaji_bonus;
            $gajiKehadiran = $request->has('gaji_kehadiran') ? $request->gaji_kehadiran : $gaji->gaji_kehadiran;
            $gajiTotal = $gajiPokok + $gajiBonus + $gajiKehadiran;
            
            $request->merge(['gaji_total' => $gajiTotal]);
        }
        
        $gaji->update($request->only([
            'gaji_pokok',
            'gaji_bonus',
            'gaji_kehadiran',
            'gaji_total',
            'tanggal_pembayaran',
            'status',
        ]));
        
        return response()->json([
            'status' => 'success',
            'message' => 'Gaji berhasil diperbarui',
            'data' => $gaji->load(['pegawai.user', 'pegawai.posisi'])
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $gaji = Gaji::find($id);
        
        if (!$gaji) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gaji tidak ditemukan'
            ], 404);
        }
        
        $gaji->delete();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Gaji berhasil dihapus'
        ]);
    }

    /**
     * Generate gaji for all pegawai for a specific period
     */
    public function generateGaji(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'periode_bulan' => 'required|integer|between:1,12',
            'periode_tahun' => 'required|integer|min:2000',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $bulan = $request->periode_bulan;
        $tahun = $request->periode_tahun;
        
        // Get all active pegawai
        $pegawai = Pegawai::whereNull('tanggal_keluar')
                         ->orWhere(function($query) use ($bulan, $tahun) {
                             $query->whereYear('tanggal_keluar', '>=', $tahun)
                                   ->whereMonth('tanggal_keluar', '>=', $bulan);
                         })
                         ->get();
        
        $startDate = Carbon::createFromDate($tahun, $bulan, 1);
        $endDate = $startDate->copy()->endOfMonth();
        
        $count = 0;
        $errors = [];
        
        foreach ($pegawai as $p) {
            // Check if gaji already exists
            $existingGaji = Gaji::where('id_pegawai', $p->id_pegawai)
                              ->where('periode_bulan', $bulan)
                              ->where('periode_tahun', $tahun)
                              ->first();
                              
            if ($existingGaji) {
                $errors[] = "Gaji untuk {$p->nama_lengkap} periode {$bulan}/{$tahun} sudah ada";
                continue;
            }
            
            // Calculate kehadiran
            $totalHariKerja = $startDate->diffInDaysFiltered(function(Carbon $date) {
                return $date->isWeekday();
            }, $endDate);
            
            $kehadiran = Absensi::where('id_pegawai', $p->id_pegawai)
                               ->whereBetween('tanggal', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                               ->count();
            
            // Get posisi and calculate gaji
            $posisi = $p->posisi;
            if (!$posisi) {
                $errors[] = "Posisi untuk {$p->nama_lengkap} tidak ditemukan";
                continue;
            }
            
            $gajiPokok = $posisi->gaji_pokok;
            
            // Calculate bonus (persentase dari gaji pokok)
            $bonusPercentage = $posisi->persen_bonus / 100;
            $gajiBonus = $gajiPokok * $bonusPercentage;
            
            // Calculate kehadiran (proporsi dari gaji pokok berdasarkan kehadiran)
            $gajiKehadiran = $kehadiran > 0 ? ($kehadiran / $totalHariKerja) * ($gajiPokok * 0.1) : 0;
            
            // Total
            $gajiTotal = $gajiPokok + $gajiBonus + $gajiKehadiran;
            
            // Create gaji
            Gaji::create([
                'id_pegawai' => $p->id_pegawai,
                'periode_bulan' => $bulan,
                'periode_tahun' => $tahun,
                'gaji_pokok' => $gajiPokok,
                'gaji_bonus' => $gajiBonus,
                'gaji_kehadiran' => $gajiKehadiran,
                'gaji_total' => $gajiTotal,
                'status' => 'Belum Terbayar',
            ]);
            
            $count++;
        }
        
        return response()->json([
            'status' => 'success',
            'message' => "Berhasil generate {$count} data gaji",
            'errors' => $errors
        ]);
    }
}
