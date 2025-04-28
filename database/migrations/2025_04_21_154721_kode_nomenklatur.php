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
        Schema::create('kode_nomenklatur', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kode');
            $table->string('nomenklatur')->nullable(); 
            $table->string('urusan')->nullable();
            $table->string('bidang_urusan')->nullable();
            $table->string('program')->nullable();
            $table->string('kegiatan')->nullable();
            $table->string('subkegiatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kode_nomenklatur');
    }
};
