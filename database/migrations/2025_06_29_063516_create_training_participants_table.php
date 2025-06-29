<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('training_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_id')->constrained('trainings')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('status_pendaftaran', ['terdaftar', 'hadir', 'tidak_hadir', 'lulus', 'tidak_lulus'])->default('terdaftar');
            $table->datetime('tanggal_daftar'); // Tanggal pendaftaran
            $table->boolean('is_approved')->default(false); // Approval dari HRD/Admin
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            
            // Evaluasi pelatihan
            $table->decimal('nilai_pre_test', 5, 2)->nullable(); // Nilai pre-test
            $table->decimal('nilai_post_test', 5, 2)->nullable(); // Nilai post-test
            $table->decimal('nilai_praktik', 5, 2)->nullable(); // Nilai praktik
            $table->decimal('nilai_akhir', 5, 2)->nullable(); // Nilai akhir
            $table->enum('grade', ['A', 'B', 'C', 'D', 'E'])->nullable(); // Grade
            $table->boolean('is_certified')->default(false); // Mendapat sertifikat
            $table->string('sertifikat_path')->nullable(); // Path file sertifikat
            
            // Feedback
            $table->text('feedback_peserta')->nullable(); // Feedback dari peserta
            $table->integer('rating_pelatihan')->nullable(); // Rating 1-5
            $table->text('saran_perbaikan')->nullable(); // Saran untuk perbaikan
            
            $table->text('catatan')->nullable();
            $table->timestamps();
            
            $table->unique(['training_id', 'user_id']); // Satu peserta per pelatihan
            $table->index(['training_id', 'status_pendaftaran']);
            $table->index(['user_id', 'is_certified']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_participants');
    }
};
