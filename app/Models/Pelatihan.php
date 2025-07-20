<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelatihan extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tb_pelatihan';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id_pelatihan';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'judul',
        'deskripsi',
        'jenis_pelatihan',
        'jadwal_pelatihan',
        'link_url',
        'durasi',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'jadwal_pelatihan' => 'datetime',
        'durasi' => 'integer',
    ];

    /**
     * Check if the pelatihan is in the future.
     */
    public function isFuture()
    {
        return now()->lt($this->jadwal_pelatihan);
    }

    /**
     * Check if the pelatihan is today.
     */
    public function isToday()
    {
        return now()->isSameDay($this->jadwal_pelatihan);
    }

    /**
     * Check if the pelatihan is in the past.
     */
    public function isPast()
    {
        return now()->gt($this->jadwal_pelatihan);
    }

    /**
     * Get the formatted duration.
     */
    public function getDurasiFormattedAttribute()
    {
        if ($this->durasi < 60) {
            return $this->durasi . ' menit';
        } else {
            $hours = floor($this->durasi / 60);
            $minutes = $this->durasi % 60;
            
            if ($minutes === 0) {
                return $hours . ' jam';
            } else {
                return $hours . ' jam ' . $minutes . ' menit';
            }
        }
    }
}
