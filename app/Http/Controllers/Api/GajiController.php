<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Gaji;
use App\Models\Pegawai;
use App\Models\Absensi;
use App\Models\Dokter;
use App\Models\Beautician;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\BookingTreatment;
use Carbon\Carbon;

class GajiController extends Controller
{
    use ApiResponseTrait;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Gaji::with(['pegawai.user', 'pegawai.posisi']);
        
        // Filter berdasarkan user untuk role non-admin/HRD
        if (!$user->hasAdminPrivileges()) {
            $pegawai = Pegawai::where('id_user', $user->id_user)->first();
            if ($pegawai) {
                $query->where('id_pegawai', $pegawai->id_pegawai);
            } else {
                // Jika user tidak memiliki record pegawai, tampilkan hasil kosong
                $query->whereRaw('1 = 0');
            }
        }
        
        // Filter berdasarkan tahun
        if ($request->filled('tahun')) {
            $query->where('periode_tahun', $request->tahun);
        }
        
        // Filter berdasarkan bulan
        if ($request->filled('bulan')) {
            $query->where('periode_bulan', $request->bulan);
        }
        
        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter berdasarkan pegawai
        if ($user->hasAdminPrivileges() && $request->filled('id_pegawai')) {
            $query->where('id_pegawai', $request->id_pegawai);
        }
        
        $gaji = $query->orderBy('periode_tahun', 'desc')
                      ->orderBy('periode_bulan', 'desc')
                      ->paginate(15);
        
        // Tambahkan data absensi untuk setiap record gaji
        $gaji->getCollection()->transform(function ($item) {
            $startDate = Carbon::createFromDate($item->periode_tahun, $item->periode_bulan, 1);
            $endDate = $startDate->copy()->endOfMonth();
            
            $jumlahAbsensi = Absensi::where('id_pegawai', $item->id_pegawai)
                                ->whereBetween('tanggal_absensi', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                                ->where('status', 'Hadir')
                                ->count();
            
            $totalHariKerja = $startDate->diffInDaysFiltered(function(Carbon $date) {
                return $date->isWeekday();
            }, $endDate);

            $item->jumlah_absensi = $jumlahAbsensi;
            $item->total_hari_kerja = $totalHariKerja;
            $item->persentase_kehadiran = $totalHariKerja > 0
                ? round(($jumlahAbsensi * 100) / $totalHariKerja, 2)
                : 0;

            return $item;
        });
        
        return response()->json([
            'status' => 'sukses',
            'pesan' => 'Data gaji berhasil diambil',
            'data' => $gaji
        ]);
    }

    /**
     * Manual salary creation is disabled. Use generateGaji() instead.
     */
    public function store(Request $request)
    {
        return response()->json([
            'status' => 'gagal',
            'pesan' => 'Penambahan gaji manual tidak diizinkan. Gunakan fitur generate gaji massal.',
            'data' => null
        ], 403);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $gaji = Gaji::with(['pegawai.user', 'pegawai.posisi'])->find($id);
        
        if (!$gaji) {
            return response()->json([
                'status' => 'gagal',
                'pesan' => 'Gaji tidak ditemukan'
            ], 404);
        }
        
        $user = request()->user();
        
        // Check if user is allowed to view this gaji
        if (!$user->hasAdminPrivileges()) {
            // Find pegawai data for this user
            $pegawai = Pegawai::where('id_user', $user->id_user)->first();
            
            if (!$pegawai) {
                return response()->json([
                    'status' => 'gagal',
                    'pesan' => 'Data pegawai tidak ditemukan untuk user ini'
                ], 404);
            }
            
            // Check if this gaji belongs to the current user's pegawai
            if ($pegawai->id_pegawai !== $gaji->id_pegawai) {
                return response()->json([
                    'status' => 'gagal',
                    'pesan' => 'Anda tidak memiliki akses untuk melihat data gaji ini'
                ], 403);
            }
        }
        
        // Tambahkan data absensi untuk record gaji ini
        $startDate = Carbon::createFromDate($gaji->periode_tahun, $gaji->periode_bulan, 1);
        $endDate = $startDate->copy()->endOfMonth();
        
        $jumlahAbsensi = Absensi::where('id_pegawai', $gaji->id_pegawai)
                               ->whereBetween('tanggal_absensi', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                               ->where('status', 'Hadir')
                               ->count();
        
        $totalHariKerja = $startDate->diffInDaysFiltered(function(Carbon $date) {
            return $date->isWeekday();
        }, $endDate);
        
        $gaji->jumlah_absensi = $jumlahAbsensi;
        $gaji->total_hari_kerja = $totalHariKerja;
        $gaji->persentase_kehadiran = $totalHariKerja > 0 ? round(($jumlahAbsensi / $totalHariKerja) * 100, 2) : 0;
        
        return response()->json([
            'status' => 'sukses',
            'pesan' => 'Data gaji berhasil diambil',
            'data' => $gaji
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = $request->user();
        
        $gaji = Gaji::with(['pegawai.user'])->find($id);
        
        if (!$gaji) {
            return response()->json([
                'status' => 'gagal',
                'pesan' => 'Gaji tidak ditemukan'
            ], 404);
        }
        
        // Check permission - admin/hrd can edit all, pegawai can only edit their own
        if (!$user->hasAdminPrivileges()) {
            // Check if this gaji belongs to the current user
            if (!$gaji->pegawai || $gaji->pegawai->id_user != $user->id_user) {
                return response()->json([
                    'status' => 'gagal',
                    'pesan' => 'Anda tidak memiliki izin untuk mengubah data gaji ini'
                ], 403);
            }
        }
        
        $validator = Validator::make($request->all(), [
            'id_pegawai' => 'sometimes|required|integer|exists:tb_pegawai,id_pegawai',
            'periode_bulan' => 'sometimes|required|integer|min:1|max:12',
            'periode_tahun' => 'sometimes|required|integer|min:2020',
            'gaji_pokok' => 'sometimes|required|numeric|min:0',
            'gaji_bonus' => 'sometimes|nullable|numeric|min:0',
            'gaji_kehadiran' => 'sometimes|nullable|numeric|min:0',
            'gaji_total' => 'sometimes|nullable|numeric|min:0',
            'keterangan' => 'sometimes|nullable|string|max:1000',
            'tanggal_pembayaran' => 'nullable|date',
            'status' => 'sometimes|required|in:Terbayar,Belum Terbayar',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => 'gagal',
                'pesan' => 'Kesalahan validasi',
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Calculate total if needed
        if ($request->has('gaji_pokok') || $request->has('gaji_bonus') || $request->has('gaji_kehadiran')) {
            $gajiPokok = $request->has('gaji_pokok') ? $request->gaji_pokok : $gaji->gaji_pokok;
            $gajiBonus = $request->has('gaji_bonus') ? $request->gaji_bonus : ($gaji->gaji_bonus ?? 0);
            $gajiKehadiran = $request->has('gaji_kehadiran') ? $request->gaji_kehadiran : ($gaji->gaji_kehadiran ?? 0);
            $gajiTotal = $gajiPokok + $gajiBonus + $gajiKehadiran;
            
            $request->merge(['gaji_total' => $gajiTotal]);
        }
        
        // Prepare update data - now allow more fields to be updated
        $updateData = $request->only([
            'id_pegawai', 
            'periode_bulan', 
            'periode_tahun', 
            'gaji_pokok', 
            'gaji_bonus', 
            'gaji_kehadiran', 
            'gaji_total',
            'keterangan',
            'status'
        ]);
        
        // Remove null values to avoid overriding existing data
        $updateData = array_filter($updateData, function($value) {
            return $value !== null && $value !== '';
        });
        
        // Jika status berubah menjadi 'Terbayar', set tanggal pembayaran ke hari ini
        if ($request->has('status') && $request->status === 'Terbayar') {
            $updateData['tanggal_pembayaran'] = now();
        }
        
        // Jika ada tanggal_pembayaran yang dikirim secara manual
        if ($request->has('tanggal_pembayaran')) {
            $updateData['tanggal_pembayaran'] = $request->tanggal_pembayaran;
        }
        
        $gaji->update($updateData);
        
        // Tambahkan data absensi untuk record gaji yang sudah diupdate
        $startDate = Carbon::createFromDate($gaji->periode_tahun, $gaji->periode_bulan, 1);
        $endDate = $startDate->copy()->endOfMonth();
        
        $jumlahAbsensi = Absensi::where('id_pegawai', $gaji->id_pegawai)
                               ->whereBetween('tanggal_absensi', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                               ->where('status', 'Hadir')
                               ->count();
        
        $totalHariKerja = $startDate->diffInDaysFiltered(function(Carbon $date) {
            return $date->isWeekday();
        }, $endDate);
        
        $gaji->jumlah_absensi = $jumlahAbsensi;
        $gaji->total_hari_kerja = $totalHariKerja;
        $gaji->persentase_kehadiran = $totalHariKerja > 0 ? round(($jumlahAbsensi / $totalHariKerja) * 100, 2) : 0;
        
        return response()->json([
            'status' => 'sukses',
            'pesan' => 'Gaji berhasil diperbarui',
            'data' => $gaji->load(['pegawai.user', 'pegawai.posisi'])
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $gaji = Gaji::find($id);
            
            if (!$gaji) {
                return $this->errorResponse('Data gaji tidak ditemukan', 404, []);
            }
            
            // Store data for response before deletion
            $responseData = [
                'id_gaji' => $gaji->id_gaji,
                'pegawai' => $gaji->pegawai->nama_lengkap ?? 'Unknown',
                'periode_bulan' => $gaji->periode_bulan,
                'periode_tahun' => $gaji->periode_tahun,
                'total_gaji' => $gaji->total_gaji
            ];
            
            $gaji->delete();
            
            return $this->successResponse(
                $responseData,
                'Data gaji berhasil dihapus'
            );
            
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Gagal menghapus data gaji',
                500,
                ['error' => $e->getMessage()]
            );
        }
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
                'status' => 'gagal',
                'pesan' => 'Kesalahan validasi',
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
                               ->whereBetween('tanggal_absensi', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                               ->where('status', 'Hadir')
                               ->count();
            
            // Get posisi and calculate gaji
            $posisi = $p->posisi;
            if (!$posisi) {
                $errors[] = "Posisi untuk {$p->nama_lengkap} tidak ditemukan";
                continue;
            }
            
            // Get effective gaji pokok - prioritize gaji_pokok_tambahan
            $gajiPokok = $p->getGajiPokokEfektif();
            
            // Calculate bonus based on percentage from posisi * total booking treatment prices for the month
            $totalBookingTreatment = 0;
            
            // Check if pegawai is a doctor
            $dokter = \App\Models\Dokter::where('id_pegawai', $p->id_pegawai)->first();
            if ($dokter) {
                $totalBookingTreatment += BookingTreatment::where('status_booking_treatment', 'Selesai')
                                                         ->whereYear('waktu_treatment', $tahun)
                                                         ->whereMonth('waktu_treatment', $bulan)
                                                         ->where('id_dokter', $dokter->id_dokter)
                                                         ->sum('harga_total');
            }
            
            // Check if pegawai is a beautician
            $beautician = \App\Models\Beautician::where('id_pegawai', $p->id_pegawai)->first();
            if ($beautician) {
                $totalBookingTreatment += BookingTreatment::where('status_booking_treatment', 'Selesai')
                                                         ->whereYear('waktu_treatment', $tahun)
                                                         ->whereMonth('waktu_treatment', $bulan)
                                                         ->where('id_beautician', $beautician->id_beautician)
                                                         ->sum('harga_total');
            }
            
            $bonusPercentage = $posisi->persen_bonus / 100;
            $gajiBonus = $totalBookingTreatment * $bonusPercentage;
            
            // Calculate attendance salary: total absensi * gaji_absensi dari posisi
            $gajiKehadiran = $kehadiran * $posisi->gaji_absensi;
            
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
            'status' => 'sukses',
            'pesan' => "Berhasil generate {$count} data gaji",
            'errors' => $errors
        ]);
    }

    /**
     * Calculate and preview salary for a specific period
     */
    public function previewCalculation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'periode_bulan' => 'required|integer|between:1,12',
            'periode_tahun' => 'required|integer|min:2000',
            'id_pegawai' => 'nullable|exists:tb_pegawai,id_pegawai',
        ]);
        
        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }
        
        $bulan = $request->periode_bulan;
        $tahun = $request->periode_tahun;
        
        // Get pegawai based on filter
        $query = Pegawai::with(['posisi', 'user'])->whereNull('tanggal_keluar')
                       ->orWhere(function($query) use ($bulan, $tahun) {
                           $query->whereYear('tanggal_keluar', '>=', $tahun)
                                 ->whereMonth('tanggal_keluar', '>=', $bulan);
                       });
        
        if ($request->filled('id_pegawai')) {
            $query->where('id_pegawai', $request->id_pegawai);
        }
        
        $pegawai = $query->get();
        
        $startDate = Carbon::createFromDate($tahun, $bulan, 1);
        $endDate = $startDate->copy()->endOfMonth();
        
        $calculations = [];
        $totalGaji = 0;
        
        foreach ($pegawai as $p) {
            // Check if gaji already exists
            $existingGaji = Gaji::where('id_pegawai', $p->id_pegawai)
                              ->where('periode_bulan', $bulan)
                              ->where('periode_tahun', $tahun)
                              ->first();
            
            // Calculate kehadiran
            $totalHariKerja = $startDate->diffInDaysFiltered(function(Carbon $date) {
                return $date->isWeekday();
            }, $endDate);
            
            $kehadiran = Absensi::where('id_pegawai', $p->id_pegawai)
                               ->whereBetween('tanggal_absensi', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                               ->where('status', 'Hadir')
                               ->count();
            
            // Get posisi and calculate gaji
            $posisi = $p->posisi;
            
            $calculation = [
                'pegawai' => [
                    'id_pegawai' => $p->id_pegawai,
                    'nama_lengkap' => $p->nama_lengkap,
                    'nip' => $p->NIP,
                    'posisi' => $posisi ? $posisi->nama_posisi : null,
                ],
                'periode' => [
                    'bulan' => $bulan,
                    'tahun' => $tahun,
                    'periode_formatted' => Carbon::createFromDate($tahun, $bulan, 1)->format('F Y'),
                ],
                'kehadiran' => [
                    'total_hari_kerja' => $totalHariKerja,
                    'hadir' => $kehadiran,
                    'persentase_kehadiran' => $totalHariKerja > 0 ? round(($kehadiran / $totalHariKerja) * 100, 2) : 0,
                ],
                'already_exists' => (bool) $existingGaji,
            ];
            
            if (!$posisi) {
                $calculation['error'] = "Posisi tidak ditemukan";
                $calculation['gaji'] = null;
            } else {
                // Get gaji pokok from posisi
                $gajiPokok = $posisi->gaji_pokok;
                
                // Calculate bonus based on percentage from posisi * total booking treatment prices for the month
                $totalBookingTreatment = 0;
                
                // Check if pegawai is a doctor
                $dokter = Dokter::where('id_pegawai', $p->id_pegawai)->first();
                if ($dokter) {
                    $totalBookingTreatment += BookingTreatment::where('status_booking_treatment', 'Selesai')
                                                             ->whereYear('waktu_treatment', $tahun)
                                                             ->whereMonth('waktu_treatment', $bulan)
                                                             ->where('id_dokter', $dokter->id_dokter)
                                                             ->sum('harga_total');
                }
                
                // Check if pegawai is a beautician
                $beautician = Beautician::where('id_pegawai', $p->id_pegawai)->first();
                if ($beautician) {
                    $totalBookingTreatment += BookingTreatment::where('status_booking_treatment', 'Selesai')
                                                             ->whereYear('waktu_treatment', $tahun)
                                                             ->whereMonth('waktu_treatment', $bulan)
                                                             ->where('id_beautician', $beautician->id_beautician)
                                                             ->sum('harga_total');
                }
                
                $bonusPercentage = $posisi->persen_bonus / 100;
                $gajiBonus = $totalBookingTreatment * $bonusPercentage;
                
                // Calculate attendance salary: total absensi * gaji_absensi dari posisi
                $gajiKehadiran = $kehadiran * $posisi->gaji_absensi;
                
                // Total
                $gajiTotal = $gajiPokok + $gajiBonus + $gajiKehadiran;
                
                $calculation['gaji'] = [
                    'gaji_pokok' => (float) $gajiPokok,
                    'gaji_bonus' => (float) $gajiBonus,
                    'gaji_kehadiran' => (float) $gajiKehadiran,
                    'gaji_total' => (float) $gajiTotal,
                    'bonus_percentage' => (float) $posisi->persen_bonus,
                    'total_booking_treatment' => (float) $totalBookingTreatment,
                    'attendance_days' => $kehadiran,
                    'attendance_rate_per_day' => 100000,
                ];
                
                $totalGaji += $gajiTotal;
            }
            
            $calculations[] = $calculation;
        }
        
        return response()->json([
            'status' => 'sukses',
            'pesan' => 'Data pratinjau perhitungan gaji berhasil diambil',
            'data' => [
                'periode' => [
                    'bulan' => $bulan,
                    'tahun' => $tahun,
                    'periode_formatted' => Carbon::createFromDate($tahun, $bulan, 1)->format('F Y'),
                ],
                'summary' => [
                    'total_pegawai' => count($calculations),
                    'total_gaji_keseluruhan' => (float) $totalGaji,
                    'rata_rata_gaji' => count($calculations) > 0 ? (float) round($totalGaji / count($calculations), 2) : 0,
                ],
                'calculations' => $calculations,
            ]
        ]);
    }

    /**
     * Get salary records for a specific employee
     */
    public function getByPegawai(Request $request, string $pegawaiId)
    {
        try {
            $user = $request->user();
            
            // Check if user has permission to view this pegawai's data
            if (!$user->hasAdminPrivileges()) {
                $userPegawai = $user->pegawai;
                if (!$userPegawai || $userPegawai->id_pegawai != $pegawaiId) {
                    return $this->errorResponse('Unauthorized to view this employee data', [], 403);
                }
            }

            // Check if pegawai exists
            $pegawai = Pegawai::with(['user', 'posisi'])->find($pegawaiId);
            if (!$pegawai) {
                return $this->errorResponse('Pegawai tidak ditemukan', [], 404);
            }

            $query = Gaji::with(['pegawai.user', 'pegawai.posisi'])
                         ->where('id_pegawai', $pegawaiId);

            // Filter by year and month if provided
            if ($request->filled('tahun')) {
                $query->where('periode_tahun', $request->tahun);
            }
            
            if ($request->filled('bulan')) {
                $query->where('periode_bulan', $request->bulan);
            }

            // Order by most recent first
            $gaji = $query->orderBy('periode_tahun', 'desc')
                         ->orderBy('periode_bulan', 'desc')
                         ->get();

            return $this->successResponse(
                [
                    'pegawai' => [
                        'id' => $pegawai->id_pegawai,
                        'nama' => $pegawai->user->name,
                        'email' => $pegawai->user->email,
                        'posisi' => $pegawai->posisi->nama_posisi ?? null,
                    ],
                    'gaji_records' => $gaji
                ],
                'Data gaji pegawai berhasil diambil'
            );

        } catch (\Exception $e) {
            return $this->errorResponse('Gagal mengambil data gaji pegawai: ' . $e->getMessage(), [], 500);
        }
    }

    /**
     * Get salary statistics for a period
     */
    public function statistics(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'periode_bulan' => 'nullable|integer|between:1,12',
            'periode_tahun' => 'nullable|integer|min:2000',
        ]);
        
        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }
        
        $query = Gaji::with(['pegawai.posisi']);
        
        if ($request->filled('periode_bulan')) {
            $query->where('periode_bulan', $request->periode_bulan);
        }
        
        if ($request->filled('periode_tahun')) {
            $query->where('periode_tahun', $request->periode_tahun);
        } else {
            // Default to current year
            $query->where('periode_tahun', now()->year);
        }
        
        $gaji = $query->get();
        
        $statistics = [
            'total_pegawai' => $gaji->count(),
            'total_gaji_keseluruhan' => (float) $gaji->sum('gaji_total'),
            'rata_rata_gaji' => (float) $gaji->avg('gaji_total'),
            'gaji_tertinggi' => (float) $gaji->max('gaji_total'),
            'gaji_terendah' => (float) $gaji->min('gaji_total'),
            'status_pembayaran' => [
                'terbayar' => $gaji->where('status', 'Terbayar')->count(),
                'belum_terbayar' => $gaji->where('status', 'Belum Terbayar')->count(),
            ],
            'by_posisi' => $gaji->groupBy('pegawai.posisi.nama_posisi')->map(function ($items, $posisi) {
                return [
                    'posisi' => $posisi,
                    'jumlah_pegawai' => $items->count(),
                    'total_gaji' => (float) $items->sum('gaji_total'),
                    'rata_rata_gaji' => (float) $items->avg('gaji_total'),
                ];
            })->values(),
        ];
        
        return response()->json([
            'status' => 'sukses',
            'pesan' => 'Statistik gaji berhasil diambil',
            'data' => $statistics
        ]);
    }

    /**
     * Automatically generate salary for current month for all active employees
     */
    public function autoGenerateMonthlyGaji()
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;
        
        return $this->generateGaji(new Request([
            'periode_bulan' => $currentMonth,
            'periode_tahun' => $currentYear
        ]));
    }

    /**
     * Get gaji data for current logged in user only
     */
    public function getMyGaji(Request $request)
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User tidak ditemukan atau tidak terautentikasi'
                ], 401);
            }
            
            // Find pegawai data first
            $pegawai = Pegawai::where('id_user', $user->id_user)->first();
            
            if (!$pegawai) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data pegawai tidak ditemukan untuk user ini',
                    'user_id' => $user->id_user
                ], 404);
            }
            
            // Get gaji data for this pegawai only
            $query = Gaji::with(['pegawai.user', 'pegawai.posisi'])
                ->where('id_pegawai', $pegawai->id_pegawai);
            
            // Apply filters if provided
            if ($request->filled('periode_tahun')) {
                $query->where('periode_tahun', $request->periode_tahun);
            }
            
            if ($request->filled('periode_bulan')) {
                $query->where('periode_bulan', $request->periode_bulan);
            }
            
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            
            // Pagination
            $perPage = $request->get('per_page', 15);
            $gaji = $query->orderBy('periode_tahun', 'desc')
                         ->orderBy('periode_bulan', 'desc')
                         ->paginate($perPage);
            
            // Tambahkan data absensi untuk setiap record gaji (sama seperti di method index)
            $gaji->getCollection()->transform(function ($item) {
                // Hitung absensi untuk periode tersebut
                $startDate = Carbon::createFromDate($item->periode_tahun, $item->periode_bulan, 1);
                $endDate = $startDate->copy()->endOfMonth();
                
                $jumlahAbsensi = Absensi::where('id_pegawai', $item->id_pegawai)
                                       ->whereBetween('tanggal_absensi', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                                       ->where('status', 'Hadir')
                                       ->count();
                
                // Hitung total hari kerja (weekdays) dalam bulan tersebut
                $totalHariKerja = $startDate->diffInDaysFiltered(function(Carbon $date) {
                    return $date->isWeekday();
                }, $endDate);
                
                $item->jumlah_absensi = $jumlahAbsensi;
                $item->total_hari_kerja = $totalHariKerja;
                $item->persentase_kehadiran = $totalHariKerja > 0 ? round(($jumlahAbsensi / $totalHariKerja) * 100, 2) : 0;
                
                return $item;
            });
            
            return response()->json([
                'status' => 'sukses',
                'message' => 'Data gaji berhasil ditemukan',
                'data' => $gaji
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengambil data gaji',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
