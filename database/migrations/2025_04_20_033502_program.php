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
        Schema::create('program', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('nama');
            $table->string('kode', length: 5);
        });
        Schema::create('kegiatan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained('program');
            $table->string('nama');
            $table->string('kode', length: 5);
            $table->timestamps();
        });
        Schema::create('subkegiatan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kegiatan_id')->constrained('kegiatan');
            $table->string('nama');
            $table->string('kode', length: 5);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subkegiatan');
        Schema::dropIfExists('kegiatan');
        Schema::dropIfExists('program');
    }
};
