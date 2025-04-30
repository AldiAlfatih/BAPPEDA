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
            $table->string('nomor_kode');
            $table->string('nomenklatur')->nullable(); 
            $table->integer('jenis_kode');
            // $table->string('bidang_urusan')->nullable();
            // $table->string('program')->nullable();
            // $table->string('kegiatan')->nullable();
            // $table->string('subkegiatan')->nullable();
            $table->timestamps();
        });
        Schema::create('kode_nomenklatur_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_nomenklatur')->constrained('kode_nomenklatur');
            $table->foreignId('urusan')->nullable()->constrained('kode_nomenklatur'); // jenis_kode 0
            $table->foreignId('bidang_urusan')->nullable()->constrained('kode_nomenklatur'); // jenis_kode 1
            $table->foreignId('program')->nullable()->constrained('kode_nomenklatur'); // jenis_kode 2
            $table->foreignId('kegiatan')->nullable()->constrained('kode_nomenklatur'); // jenis_kode 3
            $table->foreignId('subkegiatan')->nullable()->constrained('kode_nomenklatur'); // jenis_kode 4
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
