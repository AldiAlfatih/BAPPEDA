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
        // Tabel kode_nomenklatur
        Schema::create('kode_nomenklatur', function (Blueprint $table) {
            $table->id();  // Tipe data id adalah unsigned big integer secara default
            $table->string('nomor_kode');
            $table->string('nomenklatur')->nullable(); 
            $table->integer('jenis_nomenklatur');
            $table->foreignId('parent_id')->nullable()->constrained('kode_nomenklatur')->onDelete('cascade');  // Self-referencing foreign key
            $table->timestamps();
        });
        
        // Tabel urusan
        Schema::create('urusan', function (Blueprint $table) {
            $table->id();
            // Kolom kode_nomenklatur_id yang terhubung ke kode_nomenklatur
            $table->foreignId('kode_nomenklatur_id')->nullable()->constrained('kode_nomenklatur')->onDelete('set null');
            $table->string('nama');
            $table->timestamps();
        });
        

        // Tabel bid_urusan
        Schema::create('bid_urusan', function (Blueprint $table) {
            $table->id();
            // Bidang Urusan terhubung ke Urusan melalui id_urusan
            $table->foreignId('id_urusan')->nullable()->constrained('urusan')->onDelete('cascade');
            $table->string('nama');
            $table->timestamps();
        });
        

        // Tabel program
        Schema::create('program', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_bid_urusan')->nullable()->constrained('bid_urusan')->onDelete('cascade');  // Relasi ke bid_urusan
            $table->string('nama');
            $table->timestamps();
        });

        // Tabel kegiatan
        Schema::create('kegiatan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_program')->nullable()->constrained('program')->onDelete('cascade');  // Relasi ke program
            $table->string('nama');
            $table->timestamps();
        });

        // Tabel sub_kegiatan
        Schema::create('sub_kegiatan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_kegiatan')->nullable()->constrained('kegiatan')->onDelete('cascade');  // Relasi ke kegiatan
            $table->string('nama');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_kegiatan');
        Schema::dropIfExists('kegiatan');
        Schema::dropIfExists('program');
        Schema::dropIfExists('bid_urusan');
        Schema::dropIfExists('urusan');
        Schema::dropIfExists('kode_nomenklatur');
    }
};
