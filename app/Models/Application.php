<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'recruitment_id',
        'user_id',
        'nama_lengkap',
        'email',
        'phone',
        'alamat',
        'tanggal_lahir',
        'jenis_kelamin',
        'pendidikan_terakhir',
        'jurusan',
        'institusi',
        'tahun_lulus',
        'pengalaman_kerja',
        'skills',
        'surat_lamaran',
        'cv_path',
        'portfolio_path',
        'dokumen_pendukung',
        'status',
        'tanggal_interview',
        'catatan_interview',
        'alasan_penolakan',
        'gaji_yang_diinginkan',
        'reviewed_by',
        'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
            'tanggal_interview' => 'date',
            'gaji_yang_diinginkan' => 'decimal:2',
            'dokumen_pendukung' => 'json',
            'reviewed_at' => 'datetime',
        ];
    }

    /**
     * Get the recruitment this application belongs to
     */
    public function recruitment()
    {
        return $this->belongsTo(Recruitment::class);
    }

    /**
     * Get the user who applied
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who reviewed the application
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Check if application is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if application is accepted
     */
    public function isAccepted(): bool
    {
        return $this->status === 'diterima';
    }

    /**
     * Check if application is rejected
     */
    public function isRejected(): bool
    {
        return $this->status === 'ditolak';
    }

    /**
     * Check if application has interview scheduled
     */
    public function hasInterview(): bool
    {
        return $this->status === 'interview' && !is_null($this->tanggal_interview);
    }

    /**
     * Get age from birth date
     */
    public function getAgeAttribute(): int
    {
        return $this->tanggal_lahir ? $this->tanggal_lahir->age : 0;
    }

    /**
     * Scope for filtering by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for filtering by recruitment
     */
    public function scopeByRecruitment($query, $recruitmentId)
    {
        return $query->where('recruitment_id', $recruitmentId);
    }

    /**
     * Scope for pending applications
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for applications needing review
     */
    public function scopeNeedingReview($query)
    {
        return $query->whereIn('status', ['pending', 'review']);
    }
}
