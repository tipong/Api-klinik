<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Treatment extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tb_treatment';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id_treatment';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_jenis_treatment',
        'nama_treatment',
        'deskripsi_treatment',
        'biaya_treatment',
        'estimasi_treatment',
        'gambar_treatment',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'biaya_treatment' => 'decimal:2',
        'estimasi_treatment' => 'datetime',
    ];

    /**
     * Get the jenis treatment that owns the treatment.
     */
    public function jenisTreatment()
    {
        return $this->belongsTo(JenisTreatment::class, 'id_jenis_treatment', 'id_jenis_treatment');
    }

    /**
     * Get the detail booking treatments for the treatment.
     */
    public function detailBookingTreatments()
    {
        return $this->hasMany(DetailBookingTreatment::class, 'id_treatment', 'id_treatment');
    }
}
