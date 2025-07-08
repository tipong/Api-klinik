<?php

namespace Database\Seeders;

use App\Models\Absensi;
use App\Models\Pegawai;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class AbsensiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all active employees
        $pegawai = Pegawai::whereNull('tanggal_keluar')
            ->orWhere('tanggal_keluar', '>=', Carbon::now())
            ->get();
            
        if ($pegawai->isEmpty()) {
            $this->command->info('No active employees found, skipping Absensi seeding');
            return;
        }
        
        // Generate attendance records for the last 3 months
        $startDate = Carbon::now()->subMonths(3)->startOfMonth();
        $endDate = Carbon::now();
        $currentDate = clone $startDate;
        
        $absensiCount = 0;
        
        while ($currentDate->lte($endDate)) {
            // Skip weekends
            if ($currentDate->isWeekend()) {
                $currentDate->addDay();
                continue;
            }
            
            // Each employee has about 90% chance of having attendance on each workday
            foreach ($pegawai as $employee) {
                // 90% chance of attendance, adjusted with some randomness
                if (rand(1, 100) <= 90) {
                    Absensi::create([
                        'id_pegawai' => $employee->id_pegawai,
                        'tanggal' => $currentDate->format('Y-m-d'),
                    ]);
                    $absensiCount++;
                }
            }
            
            $currentDate->addDay();
        }
        
        $this->command->info("Successfully seeded {$absensiCount} attendance records for {$pegawai->count()} employees");
    }
}
