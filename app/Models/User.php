<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tb_user';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id_user';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama_user',
        'no_telp',
        'email',
        'tanggal_lahir',
        'password',
        'foto_profil',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tanggal_lahir' => 'date',
        'password' => 'hashed',
    ];

    /**
     * Get the pegawai record associated with the user.
     */
    public function pegawai()
    {
        return $this->hasOne(Pegawai::class, 'id_user', 'id_user');
    }

    /**
     * Get the lamaran pekerjaan for the user.
     */
    public function lamaranPekerjaan()
    {
        return $this->hasMany(LamaranPekerjaan::class, 'id_user', 'id_user');
    }

    /**
     * Get the hasil seleksi for the user.
     */
    public function hasilSeleksi()
    {
        return $this->hasMany(HasilSeleksi::class, 'id_user', 'id_user');
    }

    /**
     * Get the wawancara for the user.
     */
    public function wawancara()
    {
        return $this->hasMany(Wawancara::class, 'id_user', 'id_user');
    }

    /**
     * Check if user is admin
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is HRD
     */
    public function isHrd()
    {
        return $this->role === 'admin'; // Assuming admin has HRD privileges
    }

    /**
     * Check if user is employee
     */
    public function isEmployee()
    {
        return in_array($this->role, ['front office', 'kasir', 'dokter', 'beautician']);
    }

    /**
     * Check if user is customer
     */
    public function isCustomer()
    {
        return $this->role === 'pelanggan';
    }
}
