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
        Schema::create('nomenklatur', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kode');
            $table->enum('nomenklatur', ['A', 'B', 'C'])->nullable(); 
            $table->enum('urusan', ['Urusan 1', 'Urusan 2', 'Urusan 3'])->nullable();
            $table->enum('bidang_urusan', ['Bidang 1', 'Bidang 2'])->nullable();
            $table->enum('program', ['Program 1', 'Program 2'])->nullable();
            $table->enum('kegiatan', ['Kegiatan 1', 'Kegiatan 2'])->nullable();
            $table->enum('subkegiatan', ['Sub 1', 'Sub 2'])->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nomenklatur');
    }
};
