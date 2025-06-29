<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TbProduk extends Model
{
    use HasFactory;

    protected $table = 'tb_produk';
    protected $primaryKey = 'id_produk';

    protected $fillable = [
        'id_kategori',
        'nama_produk',
        'deskripsi_produk',
        'harga_produk',
        'stok_produk',
        'status_produk',
        'gambar_produk',
    ];

    protected $casts = [
        'harga_produk' => 'decimal:2',
        'stok_produk' => 'integer',
    ];

    // Relationships
    public function kategori()
    {
        return $this->belongsTo(TbKategori::class, 'id_kategori', 'id_kategori');
    }

    public function detailPenjualanProduks()
    {
        return $this->hasMany(TbDetailPenjualanProduk::class, 'id_produk', 'id_produk');
    }

    // Scopes
    public function scopeTersedia($query)
    {
        return $query->where('status_produk', 'Tersedia');
    }

    public function scopeHabis($query)
    {
        return $query->where('status_produk', 'Habis');
    }
}
