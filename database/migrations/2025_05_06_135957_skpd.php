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
        Schema::create('skpd', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('nama_skpd');
            $table->string('nama_dinas');
            $table->string('no_dpa');
            $table->string('kode_organisasi');
            $table->timestamps();
        });

	    Schema::create('skpd_kepala', function (Blueprint $table) {
            $table->id();
            $table->foreignId('skpd_id')->constrained('skpd'); 
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); 
            $table->integer('is_aktif');
            $table->timestamps();
        });

        Schema::create('skpd_tugas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('skpd_id')->constrained('skpd');
            $table->foreignId('kode_nomenklatur_id')->constrained('kode_nomenklatur'); 
            $table->integer('is_aktif');
            $table->timestamps();
        });
        Schema::create('tim_kerja', function (Blueprint $table) {
            $table->id();
            $table->foreignId('skpd_id')->constrained('skpd'); 
            $table->foreignId('operator_id')->constrained('users')->onDelete('cascade'); 
            $table->integer('is_aktif');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    Schema::dropIfExists('skpd');
	Schema::dropIfExists('skpd_kepala');
	Schema::dropIfExists('skpd_tugas');
    Schema::dropIfExists('tim_kerja');
    }
};