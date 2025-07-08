<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Gaji;
use App\Models\Pegawai;
use App\Models\Absensi;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;

class GajiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = Gaji::with('pegawai');
            
            // Filter by month
            if ($request->has('bulan') && $request->bulan !== '') {
                $query->where('periode_bulan', $request->bulan);
            }
            
            // Filter by year
            if ($request->has('tahun') && $request->tahun !== '') {
                $query->where('periode_tahun', $request->tahun);
            }
            
            // Filter by payment status
            if ($request->has('status') && $request->status !== '') {
                $query->where('status', $request->status);
            }

            // Filter by employee id
            if ($request->has('id_pegawai') && $request->id_pegawai !== '') {
                $query->where('id_pegawai', $request->id_pegawai);
            }

            // Sort options
            $sortField = $request->input('sort_by', 'periode_tahun');
            $sortDirection = $request->input('sort_direction', 'desc');
            
            if (in_array($sortField, ['id_gaji', 'id_pegawai', 'periode_bulan', 'periode_tahun', 'gaji_total', 'status'])) {
                $query->orderBy($sortField, $sortDirection);
            }
            
            // Secondary sort by periode_bulan desc
            if ($sortField !== 'periode_bulan') {
                $query->orderBy('periode_bulan', 'desc');
            }
            
            // Paginate the results
            $perPage = $request->input('per_page', 15);
            $gaji = $query->paginate($perPage);
            
            return response()->json([
                'status' => 'success',
                'data' => $gaji,
                'message' => 'Daftar gaji berhasil diambil'
            ]);
        } catch (\Exception $e) {
            Log::error('Error retrieving salary data: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengambil data gaji',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get salary details for a specific employee.
     */
    public function getEmployeeSalary(Request $request, $id_pegawai)
    {
        try {
            // Validate employee exists
            $pegawai = Pegawai::find($id_pegawai);
            if (!$pegawai) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Pegawai tidak ditemukan'
                ], 404);
            }
            
            $query = Gaji::where('id_pegawai', $id_pegawai);
            
            // Filter by month and year if provided
            if ($request->has('bulan') && $request->bulan !== '') {
                $query->where('periode_bulan', $request->bulan);
            }
            
            if ($request->has('tahun') && $request->tahun !== '') {
                $query->where('periode_tahun', $request->tahun);
            }
            
            // Sort by period, most recent first
            $query->orderBy('periode_tahun', 'desc')
                  ->orderBy('periode_bulan', 'desc');
            
            // Paginate the results
            $perPage = $request->input('per_page', 12);
            $gaji = $query->paginate($perPage);
            
            return response()->json([
                'status' => 'success',
                'data' => $gaji,
                'message' => 'Data gaji pegawai berhasil diambil'
            ]);
        } catch (\Exception $e) {
            Log::error('Error retrieving employee salary: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengambil data gaji pegawai',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $gaji = Gaji::with('pegawai')->find($id);
            
            if (!$gaji) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data gaji tidak ditemukan'
                ], 404);
            }
            
            // Get attendance data for this period
            $startDate = Carbon::create($gaji->periode_tahun, $gaji->periode_bulan, 1)->startOfMonth();
            $endDate = Carbon::create($gaji->periode_tahun, $gaji->periode_bulan, 1)->endOfMonth();
            
            $absensi = Absensi::where('id_pegawai', $gaji->id_pegawai)
                ->whereBetween('tanggal', [$startDate, $endDate])
                ->count();
            
            // Add attendance count to response
            $responseData = $gaji->toArray();
            $responseData['jumlah_absensi'] = $absensi;
            
            return response()->json([
                'status' => 'success',
                'data' => $responseData,
                'message' => 'Detail gaji berhasil diambil'
            ]);
        } catch (\Exception $e) {
            Log::error('Error retrieving salary detail: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengambil detail gaji',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $gaji = Gaji::find($id);
            
            if (!$gaji) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data gaji tidak ditemukan'
                ], 404);
            }
            
            $validator = Validator::make($request->all(), [
                'status' => 'sometimes|required|in:Terbayar,Belum Terbayar',
                'tanggal_pembayaran' => 'sometimes|nullable|date',
                'gaji_pokok' => 'sometimes|numeric|min:0',
                'gaji_bonus' => 'sometimes|numeric|min:0',
                'gaji_kehadiran' => 'sometimes|numeric|min:0',
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data tidak valid',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            // Update status if provided
            if ($request->has('status')) {
                $gaji->status = $request->status;
                
                // If status is set to Terbayar, automatically set payment date to today if not provided
                if ($request->status === 'Terbayar' && !$request->has('tanggal_pembayaran')) {
                    $gaji->tanggal_pembayaran = Carbon::now()->format('Y-m-d');
                }
            }
            
            // Update payment date if provided
            if ($request->has('tanggal_pembayaran')) {
                $gaji->tanggal_pembayaran = $request->tanggal_pembayaran;
            }
            
            // Update salary components if provided
            if ($request->has('gaji_pokok')) {
                $gaji->gaji_pokok = $request->gaji_pokok;
            }
            
            if ($request->has('gaji_bonus')) {
                $gaji->gaji_bonus = $request->gaji_bonus;
            }
            
            if ($request->has('gaji_kehadiran')) {
                $gaji->gaji_kehadiran = $request->gaji_kehadiran;
            }
            
            // Recalculate total if any component was updated
            if ($request->has('gaji_pokok') || $request->has('gaji_bonus') || $request->has('gaji_kehadiran')) {
                $gaji->gaji_total = $gaji->gaji_pokok + $gaji->gaji_bonus + $gaji->gaji_kehadiran;
            }
            
            $gaji->save();
            
            return response()->json([
                'status' => 'success',
                'data' => $gaji,
                'message' => 'Data gaji berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating salary: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat memperbarui data gaji',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Calculate salary for a specific month.
     */
    public function calculateSalary(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'bulan' => 'required|integer|min:1|max:12',
                'tahun' => 'required|integer|min:2000|max:' . (date('Y') + 1),
                'id_pegawai' => 'nullable|exists:tb_pegawai,id_pegawai',
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data tidak valid',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            $options = ['--month=' . $request->bulan, '--year=' . $request->tahun];
            
            // Execute the command
            Artisan::call('salary:calculate', $options);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Kalkulasi gaji berhasil dijalankan untuk periode ' . $request->bulan . '/' . $request->tahun
            ]);
        } catch (\Exception $e) {
            Log::error('Error calculating salary: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat kalkulasi gaji',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get salary statistics.
     */
    public function getSalaryStatistics(Request $request)
    {
        try {
            // Default to current month and year if not specified
            $month = $request->input('bulan', Carbon::now()->month);
            $year = $request->input('tahun', Carbon::now()->year);

            // Get salary data for the specified period
            $salaries = Gaji::where('periode_bulan', $month)
                ->where('periode_tahun', $year)
                ->get();

            // Calculate statistics
            $totalSalaries = $salaries->sum('gaji_total');
            $averageSalary = $salaries->count() > 0 ? $salaries->avg('gaji_total') : 0;
            $highestSalary = $salaries->max('gaji_total');
            $lowestSalary = $salaries->count() > 0 ? $salaries->min('gaji_total') : 0;
            $employeeCount = $salaries->count();
            
            // Get payment status counts
            $paidCount = $salaries->where('status', 'Terbayar')->count();
            $unpaidCount = $salaries->where('status', 'Belum Terbayar')->count();

            // Get top 5 highest salaries with employee names
            $topSalaries = Gaji::with('pegawai')
                ->where('periode_bulan', $month)
                ->where('periode_tahun', $year)
                ->orderBy('gaji_total', 'desc')
                ->limit(5)
                ->get()
                ->map(function($salary) {
                    return [
                        'id_pegawai' => $salary->id_pegawai,
                        'nama_pegawai' => $salary->pegawai->nama_lengkap,
                        'posisi' => $salary->pegawai->posisi ? $salary->pegawai->posisi->nama_posisi : '-',
                        'gaji_total' => $salary->gaji_total,
                    ];
                });

            return response()->json([
                'status' => 'success',
                'data' => [
                    'periode' => [
                        'bulan' => $month,
                        'tahun' => $year,
                    ],
                    'statistik' => [
                        'total_gaji' => $totalSalaries,
                        'rata_rata_gaji' => $averageSalary,
                        'gaji_tertinggi' => $highestSalary,
                        'gaji_terendah' => $lowestSalary,
                        'jumlah_pegawai' => $employeeCount,
                        'status_pembayaran' => [
                            'terbayar' => $paidCount,
                            'belum_terbayar' => $unpaidCount,
                        ],
                    ],
                    'gaji_tertinggi' => $topSalaries,
                ],
                'message' => 'Statistik gaji berhasil diambil',
            ]);
        } catch (\Exception $e) {
            Log::error('Error retrieving salary statistics: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengambil statistik gaji',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
