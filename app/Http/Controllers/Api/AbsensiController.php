<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Pegawai;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class AbsensiController extends Controller
{
    use ApiResponseTrait;
    // Office coordinates (sesuaikan dengan lokasi kantor klinik)
    const OFFICE_LATITUDE = -8.781952;
    const OFFICE_LONGITUDE = 115.179793;
    const OFFICE_RADIUS = 100; // dalam meter
    
    /**
     * Calculate distance between two coordinates in meters
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // Earth radius in meters
        
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        
        $a = sin($dLat/2) * sin($dLat/2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon/2) * sin($dLon/2);
             
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        
        return $earthRadius * $c;
    }
    
    /**
     * Check if location is within office radius
     */
    private function isWithinOfficeRadius($latitude, $longitude)
    {
        $distance = $this->calculateDistance(
            self::OFFICE_LATITUDE, 
            self::OFFICE_LONGITUDE, 
            $latitude, 
            $longitude
        );
        
        return $distance <= self::OFFICE_RADIUS;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Absensi::with(['pegawai.user', 'pegawai.posisi']);
        
        // Filter by user for non-admin roles
        if (!$user->isAdmin() && !$user->isHrd()) {
            $pegawai = $user->pegawai;
            if ($pegawai) {
                $query->where('pegawai_id', $pegawai->id);
            } else {
                // If user has no pegawai record, show empty result
                $query->whereRaw('1 = 0');
            }
        }
        
        // Date filter
        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }
        
        // Month filter
        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal', $request->bulan);
        }
        
        // Year filter
        if ($request->filled('tahun')) {
            $query->whereYear('tanggal', $request->tahun);
        }
        
        // Status filter (only for admin/HRD)
        if (($user->isAdmin() || $user->isHrd()) && $request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // User filter (only for admin/HRD)
        if (($user->isAdmin() || $user->isHrd()) && $request->filled('id_user')) {
            $selectedUser = User::find($request->id_user);
            if ($selectedUser && $selectedUser->pegawai) {
                $query->where('pegawai_id', $selectedUser->pegawai->id);
            }
        }
        
        $absensi = $query->orderBy('tanggal', 'desc')->paginate(15);
        
        return response()->json([
            'status' => 'success',
            'data' => $absensi
        ]);
    }

    /**
     * Store a newly created resource in storage (Check In)
     */
    public function store(Request $request)
    {
        $user = $request->user();
        $pegawai = $user->pegawai;
        
        if (!$pegawai) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda belum terdaftar sebagai pegawai. Hubungi HRD untuk pendaftaran.'
            ], 400);
        }
        
        $today = Carbon::today();
        
        // Check if user already has attendance for today
        $existingAbsensi = Absensi::where('pegawai_id', $pegawai->id)
                                  ->whereDate('tanggal', $today)
                                  ->first();
        
        if ($existingAbsensi) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda sudah melakukan absensi hari ini.'
            ], 400);
        }
        
        $validator = Validator::make($request->all(), [
            'lokasi_masuk' => 'nullable|string|max:500',
            'keterangan' => 'nullable|string|max:255',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $checkInTime = now();
        
        $absensi = Absensi::create([
            'pegawai_id' => $pegawai->id,
            'tanggal' => $today,
            'jam_masuk' => $checkInTime,
            'lokasi_masuk' => $request->lokasi_masuk ?? 'Kantor',
            'keterangan' => $request->keterangan,
            'status' => 'Hadir',
        ]);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Check-in berhasil!',
            'data' => $absensi
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $absensi = Absensi::with(['pegawai.user', 'pegawai.posisi'])->find($id);
        
        if (!$absensi) {
            return response()->json([
                'status' => 'error',
                'message' => 'Absensi tidak ditemukan'
            ], 404);
        }
        
        $user = request()->user();
        
        // Check if user is allowed to view this attendance
        if (!$user->isAdmin() && !$user->isHrd() && $user->pegawai && $user->pegawai->id !== $absensi->pegawai_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda tidak memiliki akses untuk melihat absensi ini'
            ], 403);
        }
        
        return response()->json([
            'status' => 'success',
            'data' => $absensi
        ]);
    }

    /**
     * Check out functionality
     */
    public function checkOut(Request $request, string $id)
    {
        $user = $request->user();
        $pegawai = $user->pegawai;
        
        if (!$pegawai) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda belum terdaftar sebagai pegawai. Hubungi HRD untuk pendaftaran.'
            ], 400);
        }
        
        $absensi = Absensi::find($id);
        
        if (!$absensi) {
            return response()->json([
                'status' => 'error',
                'message' => 'Absensi tidak ditemukan'
            ], 404);
        }
        
        // Check if user is allowed to checkout for this attendance
        if (!$user->isAdmin() && !$user->isHrd() && $pegawai->id !== $absensi->pegawai_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda tidak memiliki akses untuk melakukan check-out pada absensi ini'
            ], 403);
        }
        
        // Check if already checked out
        if ($absensi->jam_keluar) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda sudah melakukan check-out untuk absensi ini'
            ], 400);
        }
        
        $validator = Validator::make($request->all(), [
            'lokasi_keluar' => 'nullable|string|max:500',
            'keterangan' => 'nullable|string|max:1000',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $checkOutTime = now();
        
        $absensi->update([
            'jam_keluar' => $checkOutTime,
            'lokasi_keluar' => $request->lokasi_keluar ?? 'Kantor',
            'keterangan' => $request->keterangan,
        ]);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Check-out berhasil!',
            'data' => $absensi
        ]);
    }
}
