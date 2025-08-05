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
        // For MySQL, we can modify the enum directly
        \DB::statement("ALTER TABLE tb_wawancara MODIFY COLUMN status ENUM('pending', 'terjadwal', 'lulus', 'tidak_lulus', 'diterima', 'ditolak') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to old enum values  
        \DB::statement("ALTER TABLE tb_wawancara MODIFY COLUMN status ENUM('terjadwal', 'lulus', 'tidak_lulus') DEFAULT 'terjadwal'");
    }
};
