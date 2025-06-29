<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TbBookingTreatment extends Model
{
    use HasFactory;

    protected $table = 'tb_booking_treatment';
    protected $primaryKey = 'id_booking_treatment';

    protected $fillable = [
        'id_user',
        'waktu_treatment',
        'status_booking_treatment',
        'harga_total',
        'id_promo',
        'potongan_harga',
        'harga_akhir_treatment',
    ];

    protected $casts = [
        'waktu_treatment' => 'datetime',
        'harga_total' => 'decimal:2',
        'potongan_harga' => 'decimal:2',
        'harga_akhir_treatment' => 'decimal:2',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function promo()
    {
        return $this->belongsTo(TbPromo::class, 'id_promo', 'id_promo');
    }

    public function detailBookingTreatments()
    {
        return $this->hasMany(TbDetailBookingTreatment::class, 'id_booking_treatment', 'id_booking_treatment');
    }

    public function pembayaranTreatment()
    {
        return $this->hasOne(TbPembayaranTreatment::class, 'id_booking_treatment', 'id_booking_treatment');
    }

    // Scopes
    public function scopeVerifikasi($query)
    {
        return $query->where('status_booking_treatment', 'Verifikasi');
    }

    public function scopeBerhasilDibooking($query)
    {
        return $query->where('status_booking_treatment', 'Berhasil dibooking');
    }

    public function scopeDibatalkan($query)
    {
        return $query->where('status_booking_treatment', 'Dibatalkan');
    }

    public function scopeSelesai($query)
    {
        return $query->where('status_booking_treatment', 'Selesai');
    }
}
