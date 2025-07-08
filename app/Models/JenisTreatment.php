<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisTreatment extends Model
{
    use HasFactory;

    protected $table = 'tb_jenis_treatment';
    protected $primaryKey = 'id_jenis_treatment';

    protected $fillable = [
        'nama_jenis_treatment',
    ];

    public function treatments()
    {
        return $this->hasMany(Treatment::class, 'id_jenis_treatment', 'id_jenis_treatment');
    }
}
