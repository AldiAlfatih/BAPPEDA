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
        // Schema::create('periode_tahap', function (Blueprint $table) {
        //     $table->id();
        //     $table->string('tahap');
        // }); 

        // Schema::create('periode', function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignId('tahap_id')->constrained('periode_tahap')->onDelete('cascade');
        //     $table->string('status');
        //     $table->date('tahun');
        //     $table->timestamp('tanggal_mulai');
        //     $table->timestamp('tanggal_selesai');
        //     $table->timestamps();
        //     // Foreign key constraint ke tabel users
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::dropIfExists('periode');
    }
};
