<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dokter extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tb_dokter';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id_dokter';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_pegawai',
        'nama_dokter',
        'no_telp',
        'email_dokter',
        'NIP',
        'foto_dokter',
    ];

    /**
     * Get the pegawai associated with the dokter.
     */
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'id_pegawai', 'id_pegawai');
    }

    /**
     * Get the booking treatments for the doctor.
     */
    public function bookingTreatments()
    {
        return $this->hasMany(BookingTreatment::class, 'id_dokter', 'id_dokter');
    }

    /**
     * Get the consultations for the doctor.
     */
    public function konsultasi()
    {
        return $this->hasMany(Konsultasi::class, 'id_dokter', 'id_dokter');
    }

    /**
     * Get the jadwal praktik for the doctor.
     */
    public function jadwalPraktik()
    {
        return $this->hasMany(JadwalPraktikDokter::class, 'id_dokter', 'id_dokter');
    }
}
