<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Pegawai;
use App\Models\User;
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
        
        // Log request untuk debugging
        \Log::info('Absensi API Request', [
            'user_id' => $user->id_user,
            'user_role' => $user->role,
            'request_params' => $request->all(),
            'has_id_user_filter' => $request->filled('id_user')
        ]);
        
        // Filter by user for non-admin roles, tapi hanya jika tidak ada filter id_user yang spesifik
        if (!$user->isAdmin() && !$user->isHrd() && !$request->filled('id_user')) {
            $pegawai = $user->pegawai;
            if ($pegawai) {
                $query->where('id_pegawai', $pegawai->id_pegawai);
                \Log::info('Applied default filter for non-admin user', ['id_pegawai' => $pegawai->id_pegawai]);
            } else {
                // If user has no pegawai record, show empty result
                $query->whereRaw('1 = 0');
                \Log::info('User has no pegawai record, showing empty result');
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
        
        // User filter by id_user (untuk admin/HRD, atau untuk user yang request data sendiri)
        if ($request->filled('id_user')) {
            \Log::info('Processing id_user filter', ['requested_id_user' => $request->id_user]);
            
            if ($user->isAdmin() || $user->isHrd()) {
                // Admin/HRD bisa filter berdasarkan user mana saja
                $query->whereHas('pegawai', function($q) use ($request) {
                    $q->where('id_user', $request->id_user);
                });
                \Log::info('Applied admin/hrd filter', ['filter_id_user' => $request->id_user]);
            } else {
                // User biasa hanya bisa akses data mereka sendiri
                if ($request->id_user == $user->id_user) {
                    $query->whereHas('pegawai', function($q) use ($request) {
                        $q->where('id_user', $request->id_user);
                    });
                    \Log::info('Applied user self-filter', ['filter_id_user' => $request->id_user]);
                } else {
                    // Jika user biasa mencoba akses data user lain, return empty result
                    $query->whereRaw('1 = 0');
                    \Log::info('User trying to access other user data, blocking', [
                        'user_id' => $user->id_user,
                        'requested_id_user' => $request->id_user
                    ]);
                }
            }
        }
        
        // Filter by user_id (alternative parameter name for filtering by specific user)
        if (($user->isAdmin() || $user->isHrd()) && $request->filled('user_id')) {
            $selectedUser = User::find($request->user_id);
            if ($selectedUser && $selectedUser->pegawai) {
                $query->where('id_pegawai', $selectedUser->pegawai->id_pegawai);
            }
        }
        
        $absensi = $query->orderBy('tanggal', 'desc')->paginate(15);
        
        \Log::info('Absensi query result', [
            'total_records' => $absensi->total(),
            'current_page' => $absensi->currentPage()
        ]);
        
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
        $existingAbsensi = Absensi::where('id_pegawai', $pegawai->id_pegawai)
                                  ->whereDate('tanggal', $today)
                                  ->first();
        
        if ($existingAbsensi) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda sudah melakukan absensi hari ini.',
                'data' => $existingAbsensi
            ], 400);
        }
        
        $validator = Validator::make($request->all(), [
            'status' => 'nullable|in:Hadir,Sakit,Izin,Alpa',
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
            'id_pegawai' => $pegawai->id_pegawai,
            'tanggal' => $today,
            'jam_masuk' => $checkInTime->format('H:i:s'),
            'status' => $request->status ?? 'Hadir',
        ]);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Check-in berhasil!',
            'data' => $absensi->load('pegawai.user')
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
        if (!$user->isAdmin() && !$user->isHrd() && $user->pegawai && $user->pegawai->id_pegawai !== $absensi->id_pegawai) {
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
        if (!$user->isAdmin() && !$user->isHrd() && $pegawai->id_pegawai !== $absensi->id_pegawai) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda tidak memiliki akses untuk melakukan check-out pada absensi ini'
            ], 403);
        }
        
        // Check if already checked out
        if ($absensi->jam_keluar) {
            return response()->json([
                'status' => 'error', 
                'message' => 'Anda sudah melakukan check-out untuk absensi ini',
                'data' => $absensi
            ], 400);
        }
        
        $checkOutTime = now();
        
        // Update absensi with checkout information
        $absensi->update([
            'jam_keluar' => $checkOutTime->format('H:i:s'),
        ]);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Check-out berhasil!',
            'data' => $absensi->load('pegawai.user'),
            'work_duration' => $this->calculateWorkDuration($absensi->jam_masuk, $checkOutTime->format('H:i:s'))
        ]);
    }

    public function update(Request $request, $id)
    {
        // 1. Validasi input (tanpa keterangan)
        $validator = Validator::make($request->all(), [
            'tanggal'    => 'required|date',
            'jam_masuk'  => 'required|date_format:H:i:s',
            'jam_keluar' => 'nullable|date_format:H:i:s',
            'status'     => 'required|in:Hadir,Sakit,Izin,Alpa',
        ]);

        if ($validator->fails()) {
            // parameter ke-2: status code; ke-3: detail errors
            return $this->errorResponse(
                'Validation error',
                422,
                $validator->errors()
            );
        }

        // 2. Cari record absensi
        $absensi = Absensi::find($id);
        if (! $absensi) {
            return $this->errorResponse('Absensi tidak ditemukan', 404);
        }

        // 3. Cek hak akses (admin/HRD atau pemilik record)
        $user    = $request->user();
        $isOwner = $user->pegawai && $user->pegawai->id_pegawai === $absensi->id_pegawai;
        if (! $user->isAdmin() && ! $user->isHrd() && ! $isOwner) {
            return $this->errorResponse('Anda tidak memiliki akses untuk memperbarui absensi ini', 403);
        }

        // 4. Simpan perubahan (tanpa keterangan)
        $absensi->tanggal    = $request->tanggal;
        $absensi->jam_masuk  = $request->jam_masuk;
        $absensi->jam_keluar = $request->jam_keluar;
        $absensi->status     = $request->status;
        $absensi->save();

        // 5. Respons sukses
        return $this->successResponse('Absensi berhasil diperbarui', $absensi);
    }
    
    /**
     * Calculate work duration between check-in and check-out
     */
    private function calculateWorkDuration($jamMasuk, $jamKeluar)
    {
        $checkIn = Carbon::createFromFormat('H:i:s', $jamMasuk);
        $checkOut = Carbon::createFromFormat('H:i:s', $jamKeluar);
        
        $duration = $checkOut->diff($checkIn);
        
        return [
            'hours' => $duration->h,
            'minutes' => $duration->i,
            'total_minutes' => ($duration->h * 60) + $duration->i,
            'formatted' => $duration->format('%H:%I')
        ];
    }
    
    /**
     * Get today's attendance status for current user
     */
    public function getTodayStatus(Request $request)
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
        $todayAbsensi = Absensi::where('id_pegawai', $pegawai->id_pegawai)
                               ->whereDate('tanggal', $today)
                               ->first();
        
        if (!$todayAbsensi) {
            return response()->json([
                'status' => 'success',
                'message' => 'Belum melakukan absensi hari ini',
                'data' => [
                    'has_checked_in' => false,
                    'has_checked_out' => false,
                    'can_check_in' => true,
                    'can_check_out' => false,
                    'attendance' => null
                ]
            ]);
        }
        
        $hasCheckedOut = !is_null($todayAbsensi->jam_keluar);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Status absensi hari ini',
            'data' => [
                'has_checked_in' => true,
                'has_checked_out' => $hasCheckedOut,
                'can_check_in' => false,
                'can_check_out' => !$hasCheckedOut,
                'attendance' => $todayAbsensi->load('pegawai.user'),
                'work_duration' => $hasCheckedOut ? $this->calculateWorkDuration($todayAbsensi->jam_masuk, $todayAbsensi->jam_keluar) : null
            ]
        ]);
    }
}
