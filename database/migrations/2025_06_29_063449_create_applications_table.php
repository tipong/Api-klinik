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
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recruitment_id')->constrained('recruitments')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Pelamar
            
            // Data personal pelamar
            $table->string('nama_lengkap');
            $table->string('email');
            $table->string('phone');
            $table->text('alamat');
            $table->date('tanggal_lahir');
            $table->enum('jenis_kelamin', ['laki_laki', 'perempuan']);
            $table->string('pendidikan_terakhir');
            $table->string('jurusan')->nullable();
            $table->string('institusi')->nullable();
            $table->year('tahun_lulus')->nullable();
            
            // Experience dan skills
            $table->text('pengalaman_kerja')->nullable();
            $table->text('skills')->nullable();
            $table->text('surat_lamaran'); // Cover letter
            
            // File upload paths
            $table->string('cv_path')->nullable(); // Path file CV
            $table->string('portfolio_path')->nullable(); // Path file portfolio
            $table->json('dokumen_pendukung')->nullable(); // Array paths dokumen lain
            
            // Status lamaran
            $table->enum('status', ['pending', 'review', 'interview', 'diterima', 'ditolak'])->default('pending');
            $table->date('tanggal_interview')->nullable();
            $table->text('catatan_interview')->nullable();
            $table->text('alasan_penolakan')->nullable();
            $table->decimal('gaji_yang_diinginkan', 15, 2)->nullable();
            
            // Approval tracking
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('reviewed_at')->nullable();
            
            $table->timestamps();
            
            $table->unique(['recruitment_id', 'user_id']); // Satu lamaran per lowongan per user
            $table->index(['status', 'created_at']);
            $table->index(['recruitment_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
