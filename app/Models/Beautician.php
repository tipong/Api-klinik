<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Beautician extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tb_beautician';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id_beautician';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_pegawai',
        'nama_beautician',
        'no_telp',
        'email_beautician',
        'NIP',
    ];

    /**
     * Get the pegawai associated with the beautician.
     */
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'id_pegawai', 'id_pegawai');
    }

    /**
     * Get the booking treatments for the beautician.
     */
    public function bookingTreatments()
    {
        return $this->hasMany(BookingTreatment::class, 'id_beautician', 'id_beautician');
    }

    /**
     * Get the jadwal praktik for the beautician.
     */
    public function jadwalPraktik()
    {
        return $this->hasMany(JadwalPraktikBeautician::class, 'id_beautician', 'id_beautician');
    }
}
