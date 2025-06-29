<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tanggal',
        'jam_masuk',
        'jam_keluar',
        'latitude_masuk',
        'longitude_masuk',
        'alamat_masuk',
        'jarak_masuk',
        'latitude_keluar',
        'longitude_keluar',
        'alamat_keluar',
        'jarak_keluar',
        'status',
        'menit_terlambat',
        'menit_lembur',
        'keterangan',
        'is_approved',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'jam_masuk' => 'datetime:H:i',
            'jam_keluar' => 'datetime:H:i',
            'latitude_masuk' => 'decimal:8',
            'longitude_masuk' => 'decimal:8',
            'jarak_masuk' => 'decimal:2',
            'latitude_keluar' => 'decimal:8',
            'longitude_keluar' => 'decimal:8',
            'jarak_keluar' => 'decimal:2',
            'is_approved' => 'boolean',
        ];
    }

    /**
     * Get the user that owns the attendance
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if attendance is complete (has check-in and check-out)
     */
    public function isComplete(): bool
    {
        return !is_null($this->jam_masuk) && !is_null($this->jam_keluar);
    }

    /**
     * Calculate working hours
     */
    public function getJamKerjaAttribute(): float
    {
        if (!$this->isComplete()) {
            return 0;
        }

        $masuk = Carbon::parse($this->jam_masuk);
        $keluar = Carbon::parse($this->jam_keluar);
        
        return $keluar->diffInHours($masuk);
    }

    /**
     * Check if user is late
     */
    public function isLate(): bool
    {
        return $this->menit_terlambat > 0;
    }

    /**
     * Check if user has overtime
     */
    public function hasOvertime(): bool
    {
        return $this->menit_lembur > 0;
    }

    /**
     * Calculate distance using Haversine formula
     */
    public static function calculateDistance($lat1, $lon1, $lat2, $lon2): float
    {
        $earthRadius = 6371000; // Earth radius in meters

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Check if location is within office radius
     */
    public function isWithinOfficeRadius($latitude, $longitude, $officeRadius = 100): bool
    {
        // Default office location (should be configurable)
        $officeLat = config('app.office_latitude', -6.2088);
        $officeLon = config('app.office_longitude', 106.8456);

        $distance = self::calculateDistance($officeLat, $officeLon, $latitude, $longitude);

        return $distance <= $officeRadius;
    }

    /**
     * Scope untuk filter by date range
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('tanggal', [$startDate, $endDate]);
    }

    /**
     * Scope untuk filter by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}
