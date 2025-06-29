<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Absensi extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tb_absensi';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id_absensi';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_pegawai',
        'tanggal',
        'jam_masuk',
        'jam_keluar',
        'latitude_masuk',
        'longitude_masuk',
        'latitude_keluar',
        'longitude_keluar',
        'alamat_masuk',
        'alamat_keluar',
        'catatan',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tanggal' => 'date',
        'jam_masuk' => 'datetime',
        'jam_keluar' => 'datetime',
        'latitude_masuk' => 'decimal:8',
        'longitude_masuk' => 'decimal:8',
        'latitude_keluar' => 'decimal:8',
        'longitude_keluar' => 'decimal:8',
    ];

    /**
     * Get the pegawai that owns the absensi.
     */
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'id_pegawai', 'id_pegawai');
    }

    /**
     * Calculate the duration of work in a human-readable format.
     */
    public function getDurasiKerjaAttribute()
    {
        if ($this->jam_masuk && $this->jam_keluar) {
            $masuk = Carbon::parse($this->jam_masuk);
            $keluar = Carbon::parse($this->jam_keluar);
            
            $minutes = $masuk->diffInMinutes($keluar);
            $hours = floor($minutes / 60);
            $remainingMinutes = $minutes % 60;
            
            return sprintf('%d jam %d menit', $hours, $remainingMinutes);
        }
        
        return '-';
    }

    /**
     * Check if the pegawai was late.
     */
    public function isLate()
    {
        if (!$this->jam_masuk) return false;
        
        $workStartTime = Carbon::createFromTime(8, 0, 0);
        $checkInTime = Carbon::parse($this->jam_masuk);
        
        return $checkInTime->format('H:i') > $workStartTime->format('H:i');
    }

    /**
     * Check if the pegawai can check out.
     */
    public function canCheckOut()
    {
        return $this->jam_masuk && !$this->jam_keluar;
    }

    /**
     * Get the status of the attendance.
     */
    public function getStatusAttribute()
    {
        if (!$this->jam_masuk) {
            return 'Tidak Hadir';
        }
        
        if ($this->isLate()) {
            return 'Terlambat';
        }
        
        return 'Hadir';
    }
}
