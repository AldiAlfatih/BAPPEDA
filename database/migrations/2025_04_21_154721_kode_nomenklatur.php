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
        // Buat tabel utama terlebih dahulu
        Schema::create('kode_nomenklatur', function (Blueprint $table) {
            $table->id();
            $table->integer('jenis_nomenklatur');
            $table->string('nomor_kode');
            $table->text('nomenklatur'); // Changed from string to text to accommodate long descriptions
            $table->timestamps();
        });

        Schema::create('kode_nomenklatur_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_nomenklatur')->constrained('kode_nomenklatur')->onDelete('cascade');
            $table->foreignId('id_urusan')->nullable()->constrained('kode_nomenklatur')->onDelete('cascade');
            $table->foreignId('id_bidang_urusan')->nullable()->constrained('kode_nomenklatur')->onDelete('cascade');
            $table->foreignId('id_program')->nullable()->constrained('kode_nomenklatur')->onDelete('cascade');
            $table->foreignId('id_kegiatan')->nullable()->constrained('kode_nomenklatur')->onDelete('cascade');
            $table->foreignId('id_sub_kegiatan')->nullable()->constrained('kode_nomenklatur')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kode_nomenklatur_detail');
        Schema::dropIfExists('kode_nomenklatur');
    }
};