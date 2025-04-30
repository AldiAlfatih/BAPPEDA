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
        
        Schema::create('jenis_periode', function (Blueprint $table) {
            $table->id();
            $table->string('Jenis');
        }); 

        Schema::create('periode', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jenis_periode_id')->constrained('periode');
            $$table->string('status');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->timestamps();

            // Foreign key constraint ke tabel users
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('periode');
    }
};
