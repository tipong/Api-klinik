<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TrainingParticipant extends Model
{
    use HasFactory;

    protected $fillable = [
        'training_id',
        'user_id',
        'status_pendaftaran',
        'tanggal_daftar',
        'is_approved',
        'approved_by',
        'approved_at',
        'nilai_pre_test',
        'nilai_post_test',
        'nilai_praktik',
        'nilai_akhir',
        'grade',
        'is_certified',
        'sertifikat_path',
        'feedback_peserta',
        'rating_pelatihan',
        'saran_perbaikan',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_daftar' => 'datetime',
            'is_approved' => 'boolean',
            'approved_at' => 'datetime',
            'nilai_pre_test' => 'decimal:2',
            'nilai_post_test' => 'decimal:2',
            'nilai_praktik' => 'decimal:2',
            'nilai_akhir' => 'decimal:2',
            'is_certified' => 'boolean',
        ];
    }

    /**
     * Get the training this participant belongs to
     */
    public function training()
    {
        return $this->belongsTo(Training::class);
    }

    /**
     * Get the user who is participating
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who approved the participation
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Check if participant is approved
     */
    public function isApproved(): bool
    {
        return $this->is_approved;
    }

    /**
     * Check if participant attended
     */
    public function isAttended(): bool
    {
        return in_array($this->status_pendaftaran, ['hadir', 'lulus', 'tidak_lulus']);
    }

    /**
     * Check if participant passed
     */
    public function isPassed(): bool
    {
        return $this->status_pendaftaran === 'lulus';
    }

    /**
     * Check if participant is certified
     */
    public function isCertified(): bool
    {
        return $this->is_certified && !empty($this->sertifikat_path);
    }

    /**
     * Calculate final grade based on scores
     */
    public function calculateFinalGrade(): string
    {
        if (is_null($this->nilai_akhir)) {
            return '';
        }

        if ($this->nilai_akhir >= 85) return 'A';
        if ($this->nilai_akhir >= 75) return 'B';
        if ($this->nilai_akhir >= 65) return 'C';
        if ($this->nilai_akhir >= 55) return 'D';
        return 'E';
    }

    /**
     * Calculate final score from all components
     */
    public function calculateFinalScore(): float
    {
        $preTest = $this->nilai_pre_test ?? 0;
        $postTest = $this->nilai_post_test ?? 0;
        $praktik = $this->nilai_praktik ?? 0;

        // Weight: Pre-test 20%, Post-test 40%, Praktik 40%
        return ($preTest * 0.2) + ($postTest * 0.4) + ($praktik * 0.4);
    }

    /**
     * Update final score and grade
     */
    public function updateFinalScoreAndGrade()
    {
        $this->nilai_akhir = $this->calculateFinalScore();
        $this->grade = $this->calculateFinalGrade();
        
        // Update pass status
        if ($this->nilai_akhir >= 65) {
            $this->status_pendaftaran = 'lulus';
            $this->is_certified = true;
        } else {
            $this->status_pendaftaran = 'tidak_lulus';
            $this->is_certified = false;
        }

        $this->save();
    }

    /**
     * Scope for approved participants
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    /**
     * Scope for participants who attended
     */
    public function scopeAttended($query)
    {
        return $query->whereIn('status_pendaftaran', ['hadir', 'lulus', 'tidak_lulus']);
    }

    /**
     * Scope for participants who passed
     */
    public function scopePassed($query)
    {
        return $query->where('status_pendaftaran', 'lulus');
    }

    /**
     * Scope for certified participants
     */
    public function scopeCertified($query)
    {
        return $query->where('is_certified', true);
    }
}
