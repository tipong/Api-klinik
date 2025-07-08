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
        Schema::create('tb_detail_booking_treatment', function (Blueprint $table) {
            $table->increments('id_detail_booking_treatment');
            $table->unsignedInteger('id_booking_treatment');
            $table->unsignedInteger('id_treatment');
            $table->decimal('biaya_treatment', 15, 2);
            $table->unsignedInteger('id_kompensasi_diberikan')->nullable();
            $table->timestamps();
            
            $table->foreign('id_booking_treatment')->references('id_booking_treatment')->on('tb_booking_treatment')->onDelete('cascade');
            $table->foreign('id_treatment')->references('id_treatment')->on('tb_treatment')->onDelete('cascade');
            $table->foreign('id_kompensasi_diberikan')->references('id_kompensasi_diberikan')->on('tb_kompensasi_diberikan')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_detail_booking_treatment');
    }
};
