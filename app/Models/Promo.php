<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promo extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tb_promo';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id_promo';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama_promo',
        'jenis_promo',
        'deskripsi_promo',
        'tipe_potongan',
        'potongan_harga',
        'minimal_belanja',
        'tanggal_mulai',
        'tanggal_berakhir',
        'gambar_promo',
        'status_promo',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_berakhir' => 'date',
        'potongan_harga' => 'decimal:2',
        'minimal_belanja' => 'decimal:2',
    ];
}
