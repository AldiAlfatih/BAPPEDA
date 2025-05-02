<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('kode_nomenklatur', function (Blueprint $table) {
            $table->id();
            $table->integer('jenis_nomenklatur');
            $table->string('nomor_kode');
            $table->string('nomenklatur')->nullable(); 
            $table->unsignedBigInteger('id_urusan')->nullable();
            $table->unsignedBigInteger('id_bidang_urusan')->nullable();
            $table->unsignedBigInteger('id_program')->nullable();
            $table->unsignedBigInteger('id_kegiatan')->nullable();
            $table->unsignedBigInteger('id_subkegiatan')->nullable();
            $table->timestamps();
        });

        Schema::create('kode_nomenklatur_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_nomenklatur')->constrained('kode_nomenklatur')->onDelete('cascade');
            $table->string('urusan')->nullable();
            $table->string('bidang_urusan')->nullable();
            $table->string('program')->nullable();
            $table->string('kegiatan')->nullable();
            $table->string('subkegiatan')->nullable();
            $table->timestamps();
        });

        Schema::table('kode_nomenklatur', function (Blueprint $table) {
            $table->foreign('id_urusan')->references('id')->on('kode_nomenklatur_detail')->nullOnDelete();
            $table->foreign('id_bidang_urusan')->references('id')->on('kode_nomenklatur_detail')->nullOnDelete();
            $table->foreign('id_program')->references('id')->on('kode_nomenklatur_detail')->nullOnDelete();
            $table->foreign('id_kegiatan')->references('id')->on('kode_nomenklatur_detail')->nullOnDelete();
            $table->foreign('id_subkegiatan')->references('id')->on('kode_nomenklatur_detail')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kode_nomenklatur_detail');
        Schema::dropIfExists('kode_nomenklatur');
    }
};
