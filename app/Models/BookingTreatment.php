<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingTreatment extends Model
{
    use HasFactory;

    protected $table = 'tb_booking_treatment';
    protected $primaryKey = 'id_booking_treatment';

    protected $fillable = [
        'id_user',
        'waktu_treatment',
        'id_dokter',
        'id_beautician',
        'status_booking_treatment',
        'harga_total',
        'id_promo',
        'potongan_harga',
        'besaran_pajak',
        'harga_akhir_treatment',
    ];

    protected $casts = [
        'waktu_treatment' => 'datetime',
        'harga_total' => 'decimal:2',
        'potongan_harga' => 'decimal:2',
        'besaran_pajak' => 'decimal:2',
        'harga_akhir_treatment' => 'decimal:2',
    ];

    /**
     * Get the user that owns the booking
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    /**
     * Get the doctor for the booking
     */
    public function dokter()
    {
        return $this->belongsTo(User::class, 'id_dokter', 'id_user');
    }

    /**
     * Get the beautician for the booking
     */
    public function beautician()
    {
        return $this->belongsTo(User::class, 'id_beautician', 'id_user');
    }
}
