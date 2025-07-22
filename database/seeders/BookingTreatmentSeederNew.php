<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BookingTreatment;
use Carbon\Carbon;

class BookingTreatmentSeederNew extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        BookingTreatment::truncate();
        
        // Generate booking data for the last 3 months
        $this->generateBookingsForMonths();
    }
    
    private function generateBookingsForMonths()
    {
        // Generate for May, June, July 2025
        $months = [
            ['year' => 2025, 'month' => 5], // May
            ['year' => 2025, 'month' => 6], // June  
            ['year' => 2025, 'month' => 7], // July
        ];
        
        foreach ($months as $period) {
            $this->generateMonthlyBookings($period['year'], $period['month']);
        }
    }
    
    private function generateMonthlyBookings($year, $month)
    {
        $startDate = Carbon::createFromDate($year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();
        
        // Generate 15-25 bookings per month
        $bookingCount = rand(15, 25);
        
        for ($i = 0; $i < $bookingCount; $i++) {
            // Random date within the month
            $randomDay = rand(1, $endDate->day);
            $bookingDate = Carbon::createFromDate($year, $month, $randomDay);
            
            // Random time between 09:00 and 17:00
            $hour = rand(9, 17);
            $minute = rand(0, 1) * 30; // 00 or 30 minutes
            $bookingDate->setTime($hour, $minute);
            
            // Random service provider (doctor or beautician)
            $isDoctorService = rand(0, 1);
            
            if ($isDoctorService) {
                // Doctor service - use auto-increment IDs (1 and 2)
                $doctorId = rand(1, 2); // ID 1 (Dr. Ahmad) or ID 2 (Dr. Sari)
                $beauticianId = null;
                $basePrice = rand(500000, 3000000); // Doctor services are more expensive
            } else {
                // Beautician service - use auto-increment IDs (1 and 2)
                $doctorId = null;
                $beauticianId = rand(1, 2); // ID 1 (Maria) or ID 2 (Linda)
                $basePrice = rand(200000, 800000); // Beautician services
            }
            
            // Random customer
            $customerId = rand(9, 10); // Budi (9) or Sinta (10)
            
            // Random status - mostly completed for salary calculation
            $statuses = ['Selesai', 'Selesai', 'Selesai', 'Berhasil dibooking', 'Verifikasi'];
            $status = $statuses[array_rand($statuses)];
            
            // Calculate pricing
            $hasPromo = rand(0, 3) == 0; // 25% chance of promo
            $promoId = $hasPromo ? 1 : null;
            $discount = $hasPromo ? ($basePrice * 0.5) : 0; // 50% discount if promo
            $tax = $basePrice * 0.1; // 10% tax
            $finalPrice = $basePrice - $discount + $tax;
            
            BookingTreatment::create([
                'id_user' => $customerId,
                'waktu_treatment' => $bookingDate->format('Y-m-d H:i:s'),
                'id_dokter' => $doctorId,
                'id_beautician' => $beauticianId,
                'status_booking_treatment' => $status,
                'harga_total' => $basePrice,
                'id_promo' => $promoId,
                'potongan_harga' => $discount,
                'besaran_pajak' => $tax,
                'harga_akhir_treatment' => $finalPrice,
            ]);
        }
    }
}
