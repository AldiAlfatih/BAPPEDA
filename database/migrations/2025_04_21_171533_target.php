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
        Schema::create('target', function (Blueprint $table) {
            $table->id();
            $table->text('indikator');
            $table->string('satuan');
            $table->float('capaian_akhir');
            $table->unsignedBigInteger('id_perangkat_daerah');
            $table->unsignedBigInteger('id_operator');
            $table->timestamps();

            // Foreign key constraint (optional, bisa disesuaikan jika punya tabel relasi)
            $table->foreign('id_perangkat_daerah')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('id_operator')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('target');
    }
};
