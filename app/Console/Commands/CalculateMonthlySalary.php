<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Pegawai;
use App\Models\Posisi;
use App\Models\Gaji;
use App\Models\Absensi;
use App\Models\BookingTreatment;
use App\Models\DetailBookingTreatment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CalculateMonthlySalary extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'salary:calculate {--month= : Month number (1-12)} {--year= : Year (YYYY)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate monthly salary for all employees';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get month and year from options or use previous month
        $now = Carbon::now();
        
        // By default, calculate for previous month
        $month = $this->option('month') ? (int)$this->option('month') : $now->subMonth()->month;
        $year = $this->option('year') ? (int)$this->option('year') : $now->year;
        
        // Validate month
        if ($month < 1 || $month > 12) {
            $this->error('Invalid month. Please provide a number between 1 and 12.');
            return 1;
        }

        $this->info("Calculating salary for {$year}-{$month}...");
        
        // Start a database transaction
        DB::beginTransaction();
        
        try {
            // Get all active employees
            $pegawai = Pegawai::whereNull('tanggal_keluar')
                ->orWhere('tanggal_keluar', '>=', Carbon::create($year, $month, 1))
                ->get();
                
            $this->info("Found " . $pegawai->count() . " active employees");
            
            $startDate = Carbon::create($year, $month, 1)->startOfMonth();
            $endDate = Carbon::create($year, $month, 1)->endOfMonth();
            
            // Process each employee
            foreach ($pegawai as $employee) {
                $this->info("Processing employee: {$employee->nama_lengkap}");
                
                // Get position details
                $posisi = Posisi::find($employee->id_posisi);
                if (!$posisi) {
                    $this->warn("Position not found for employee {$employee->nama_lengkap}, skipping...");
                    continue;
                }
                
                // 1. Calculate base salary from position
                $gajiPokok = $posisi->gaji_pokok;
                
                // 2. Calculate attendance salary: 100k per attendance day
                $absensiCount = Absensi::where('id_pegawai', $employee->id_pegawai)
                    ->whereBetween('tanggal', [$startDate, $endDate])
                    ->count();
                $gajiKehadiran = $absensiCount * 100000;
                
                // 3. Calculate bonus from treatments
                $gajiBonus = 0;
                
                // If the employee is a doctor or beautician, check for booking treatments
                if (in_array($posisi->nama_posisi, ['Dokter', 'Beautician'])) {
                    // First find the related record in tb_dokter or tb_beautician
                    if ($posisi->nama_posisi === 'Dokter') {
                        // Get treatments handled by this doctor
                        $bookings = DB::table('tb_booking_treatment')
                            ->join('tb_dokter', 'tb_booking_treatment.id_dokter', '=', 'tb_dokter.id_dokter')
                            ->where('tb_dokter.id_pegawai', $employee->id_pegawai)
                            ->whereBetween('tb_booking_treatment.waktu_treatment', [$startDate, $endDate])
                            ->where('tb_booking_treatment.status_booking_treatment', 'Selesai')
                            ->get();
                    } else {
                        // Get treatments handled by this beautician
                        $bookings = DB::table('tb_booking_treatment')
                            ->join('tb_beautician', 'tb_booking_treatment.id_beautician', '=', 'tb_beautician.id_beautician')
                            ->where('tb_beautician.id_pegawai', $employee->id_pegawai)
                            ->whereBetween('tb_booking_treatment.waktu_treatment', [$startDate, $endDate])
                            ->where('tb_booking_treatment.status_booking_treatment', 'Selesai')
                            ->get();
                    }
                    
                    // Calculate bonus based on treatment values
                    foreach ($bookings as $booking) {
                        $gajiBonus += ($booking->harga_total * $posisi->persen_bonus / 100);
                    }
                }
                
                // Calculate total salary
                $gajiTotal = $gajiPokok + $gajiBonus + $gajiKehadiran;
                
                // Check if salary record already exists for this month
                $existingGaji = Gaji::where('id_pegawai', $employee->id_pegawai)
                    ->where('periode_bulan', $month)
                    ->where('periode_tahun', $year)
                    ->first();
                    
                if ($existingGaji) {
                    // Update existing record
                    $existingGaji->gaji_pokok = $gajiPokok;
                    $existingGaji->gaji_bonus = $gajiBonus;
                    $existingGaji->gaji_kehadiran = $gajiKehadiran;
                    $existingGaji->gaji_total = $gajiTotal;
                    $existingGaji->save();
                    
                    $this->info("Updated salary record for {$employee->nama_lengkap}: Rp " . number_format($gajiTotal, 2));
                } else {
                    // Create new salary record
                    Gaji::create([
                        'id_pegawai' => $employee->id_pegawai,
                        'periode_bulan' => $month,
                        'periode_tahun' => $year,
                        'gaji_pokok' => $gajiPokok,
                        'gaji_bonus' => $gajiBonus,
                        'gaji_kehadiran' => $gajiKehadiran,
                        'gaji_total' => $gajiTotal,
                        'status' => 'Belum Terbayar',
                    ]);
                    
                    $this->info("Created salary record for {$employee->nama_lengkap}: Rp " . number_format($gajiTotal, 2));
                }
            }
            
            DB::commit();
            $this->info("Monthly salary calculation completed successfully!");
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Error calculating salaries: " . $e->getMessage());
            Log::error("Salary calculation error: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return 1;
        }
        
        return 0;
    }
}
