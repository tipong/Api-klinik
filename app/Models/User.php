<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    protected $table = 'tb_user';
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'email',
        'nama',
        'password',
        'role',
        'status',
        'phone',
        'alamat',
        'photo',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Check if user has admin role
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user has HRD role
     */
    public function isHrd(): bool
    {
        return $this->role === 'hrd';
    }

    /**
     * Check if user needs to do attendance (all roles except admin)
     */
    public function needsAttendance(): bool
    {
        return !in_array($this->role, ['admin']);
    }

    /**
     * Check if user can manage all features (admin and hrd)
     */
    public function canManageAll(): bool
    {
        return in_array($this->role, ['admin', 'hrd']);
    }

    /**
     * Get employee data
     */
    public function pegawai()
    {
        return $this->hasOne(Pegawai::class, 'id_user', 'id');
    }

    /**
     * Get attendances
     */
    public function absensi()
    {
        return $this->hasManyThrough(
            Absensi::class,
            Pegawai::class,
            'id_user', // Foreign key on pegawai table
            'id_pegawai', // Foreign key on absensi table
            'id', // Local key on user table
            'id_pegawai' // Local key on pegawai table
        );
    }

    /**
     * Get job applications
     */
    public function lamaranPekerjaan()
    {
        return $this->hasMany(LamaranPekerjaan::class, 'id_user', 'id');
    }

    /**
     * Get hasil seleksi
     */
    public function hasilSeleksi()
    {
        return $this->hasMany(HasilSeleksi::class, 'id_user', 'id');
    }

    /**
     * Get wawancara
     */
    public function wawancara()
    {
        return $this->hasMany(Wawancara::class, 'id_user', 'id');
    }
}
