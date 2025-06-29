<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Salary extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tahun',
        'bulan',
        'periode_mulai',
        'periode_selesai',
        'gaji_pokok',
        'tunjangan_tetap',
        'tunjangan_kehadiran',
        'bonus_kinerja',
        'bonus_penjualan',
        'uang_lembur',
        'tunjangan_lain',
        'potongan_terlambat',
        'potongan_alpha',
        'potongan_bpjs',
        'potongan_pajak',
        'potongan_lain',
        'hari_kerja',
        'hari_hadir',
        'hari_terlambat',
        'hari_alpha',
        'hari_izin',
        'hari_sakit',
        'total_menit_lembur',
        'total_pendapatan',
        'total_potongan',
        'gaji_bersih',
        'status',
        'tanggal_dibayar',
        'approved_by',
        'approved_at',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'periode_mulai' => 'date',
            'periode_selesai' => 'date',
            'gaji_pokok' => 'decimal:2',
            'tunjangan_tetap' => 'decimal:2',
            'tunjangan_kehadiran' => 'decimal:2',
            'bonus_kinerja' => 'decimal:2',
            'bonus_penjualan' => 'decimal:2',
            'uang_lembur' => 'decimal:2',
            'tunjangan_lain' => 'decimal:2',
            'potongan_terlambat' => 'decimal:2',
            'potongan_alpha' => 'decimal:2',
            'potongan_bpjs' => 'decimal:2',
            'potongan_pajak' => 'decimal:2',
            'potongan_lain' => 'decimal:2',
            'total_pendapatan' => 'decimal:2',
            'total_potongan' => 'decimal:2',
            'gaji_bersih' => 'decimal:2',
            'tanggal_dibayar' => 'date',
            'approved_at' => 'datetime',
        ];
    }

    /**
     * Get the user this salary belongs to
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who approved this salary
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Check if salary is approved
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if salary is paid
     */
    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    /**
     * Calculate total income
     */
    public function calculateTotalPendapatan(): float
    {
        return $this->gaji_pokok + 
               $this->tunjangan_tetap + 
               $this->tunjangan_kehadiran + 
               $this->bonus_kinerja + 
               $this->bonus_penjualan + 
               $this->uang_lembur + 
               $this->tunjangan_lain;
    }

    /**
     * Calculate total deductions
     */
    public function calculateTotalPotongan(): float
    {
        return $this->potongan_terlambat + 
               $this->potongan_alpha + 
               $this->potongan_bpjs + 
               $this->potongan_pajak + 
               $this->potongan_lain;
    }

    /**
     * Calculate net salary
     */
    public function calculateGajiBersih(): float
    {
        return $this->calculateTotalPendapatan() - $this->calculateTotalPotongan();
    }

    /**
     * Calculate attendance allowance based on attendance rate
     */
    public function calculateTunjanganKehadiran(): float
    {
        if ($this->hari_kerja == 0) return 0;

        $attendanceRate = $this->hari_hadir / $this->hari_kerja;
        
        // Base attendance allowance (configurable)
        $baseTunjangan = 200000;

        // Full allowance if attendance >= 95%
        if ($attendanceRate >= 0.95) {
            return $baseTunjangan;
        }
        
        // Proportional allowance
        return $baseTunjangan * $attendanceRate;
    }

    /**
     * Calculate late deductions
     */
    public function calculatePotonganTerlambat(): float
    {
        // Rp 10,000 per hari terlambat (configurable)
        $tarifTerlambat = 10000;
        return $this->hari_terlambat * $tarifTerlambat;
    }

    /**
     * Calculate absent deductions
     */
    public function calculatePotonganAlpha(): float
    {
        if ($this->hari_kerja == 0) return 0;

        // Deduct daily salary for each absent day
        $dailySalary = $this->gaji_pokok / $this->hari_kerja;
        return $this->hari_alpha * $dailySalary;
    }

    /**
     * Calculate overtime pay
     */
    public function calculateUangLembur(): float
    {
        if ($this->hari_kerja == 0) return 0;

        // Calculate hourly rate from basic salary
        $hourlySalary = $this->gaji_pokok / ($this->hari_kerja * 8); // 8 hours per day
        
        // Overtime rate is 1.5x normal rate
        $overtimeRate = $hourlySalary * 1.5;
        
        // Convert minutes to hours
        $overtimeHours = $this->total_menit_lembur / 60;
        
        return $overtimeHours * $overtimeRate;
    }

    /**
     * Auto calculate all salary components
     */
    public function autoCalculate()
    {
        $this->tunjangan_kehadiran = $this->calculateTunjanganKehadiran();
        $this->potongan_terlambat = $this->calculatePotonganTerlambat();
        $this->potongan_alpha = $this->calculatePotonganAlpha();
        $this->uang_lembur = $this->calculateUangLembur();
        
        $this->total_pendapatan = $this->calculateTotalPendapatan();
        $this->total_potongan = $this->calculateTotalPotongan();
        $this->gaji_bersih = $this->calculateGajiBersih();
    }

    /**
     * Get attendance rate percentage
     */
    public function getAttendanceRateAttribute(): float
    {
        if ($this->hari_kerja == 0) return 0;
        
        return ($this->hari_hadir / $this->hari_kerja) * 100;
    }

    /**
     * Scope for filtering by year and month
     */
    public function scopeByPeriod($query, $tahun, $bulan = null)
    {
        $query->where('tahun', $tahun);
        
        if ($bulan) {
            $query->where('bulan', $bulan);
        }
        
        return $query;
    }

    /**
     * Scope for filtering by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for pending salaries
     */
    public function scopePending($query)
    {
        return $query->where('status', 'draft');
    }
}
