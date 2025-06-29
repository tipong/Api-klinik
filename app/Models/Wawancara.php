<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wawancara extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tb_wawancara';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id_wawancara';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_lamaran_pekerjaan',
        'id_user',
        'tanggal_wawancara',
        'lokasi',
        'catatan',
        'hasil',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tanggal_wawancara' => 'datetime',
    ];

    /**
     * Get the lamaran pekerjaan that owns the wawancara.
     */
    public function lamaranPekerjaan()
    {
        return $this->belongsTo(LamaranPekerjaan::class, 'id_lamaran_pekerjaan', 'id_lamaran_pekerjaan');
    }

    /**
     * Get the user that owns the wawancara.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    /**
     * Check if the wawancara is accepted.
     */
    public function isAccepted()
    {
        return $this->hasil === 'diterima';
    }

    /**
     * Check if the wawancara is rejected.
     */
    public function isRejected()
    {
        return $this->hasil === 'ditolak';
    }

    /**
     * Check if the wawancara is pending.
     */
    public function isPending()
    {
        return $this->hasil === 'pending';
    }

    /**
     * Check if the wawancara is in the future.
     */
    public function isFuture()
    {
        return now()->lt($this->tanggal_wawancara);
    }

    /**
     * Check if the wawancara is today.
     */
    public function isToday()
    {
        return now()->isSameDay($this->tanggal_wawancara);
    }

    /**
     * Check if the wawancara is in the past.
     */
    public function isPast()
    {
        return now()->gt($this->tanggal_wawancara);
    }
}
