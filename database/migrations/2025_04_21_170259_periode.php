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
        Schema::create('periode_tahap', function (Blueprint $table) {
            $table->id();
            $table->string('tahap');
        }); 

        Schema::create('periode_tahun', function (Blueprint $table) {
            $table->id();
            $table->integer('status')->default(0);
            $table->year('tahun');
            $table->timestamps();
        });

        Schema::create('periode', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahap_id')->constrained('periode_tahap')->onDelete('cascade');
            $table->foreignId('tahun_id')->nullable()->constrained('periode_tahun')->onDelete('cascade');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->integer('status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('periode');
        Schema::dropIfExists('periode_tahun');
        Schema::dropIfExists('periode_tahap');
    }
};