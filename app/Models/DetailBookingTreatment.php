<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailBookingTreatment extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tb_detail_booking_treatment';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id_detail_booking_treatment';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_booking_treatment',
        'id_treatment',
        'biaya_treatment',
        'id_kompensasi_diberikan',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'biaya_treatment' => 'decimal:2',
    ];

    /**
     * Get the booking treatment that owns the detail.
     */
    public function bookingTreatment()
    {
        return $this->belongsTo(BookingTreatment::class, 'id_booking_treatment', 'id_booking_treatment');
    }

    /**
     * Get the treatment for this detail.
     */
    public function treatment()
    {
        return $this->belongsTo(Treatment::class, 'id_treatment', 'id_treatment');
    }
}
