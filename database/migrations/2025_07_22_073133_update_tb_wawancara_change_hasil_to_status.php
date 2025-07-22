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
        Schema::table('tb_wawancara', function (Blueprint $table) {
            // Rename kolom hasil menjadi status
            $table->renameColumn('hasil', 'status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_wawancara', function (Blueprint $table) {
            // Rename kembali kolom status menjadi hasil
            $table->renameColumn('status', 'hasil');
        });
    }
};
