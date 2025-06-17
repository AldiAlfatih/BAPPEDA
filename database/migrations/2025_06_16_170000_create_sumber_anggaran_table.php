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
        Schema::create('sumber_anggaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('skpd_tugas_id')->constrained('skpd_tugas')->onDelete('cascade');
            $table->foreignId('periode_id')->constrained('periode')->onDelete('cascade');
            $table->boolean('dak')->default(false);
            $table->boolean('dak_peruntukan')->default(false);
            $table->boolean('dak_fisik')->default(false);
            $table->boolean('dak_non_fisik')->default(false);
            $table->boolean('blud')->default(false);
            $table->double('nilai_dak')->default(0);
            $table->double('nilai_dak_peruntukan')->default(0);
            $table->double('nilai_dak_fisik')->default(0);
            $table->double('nilai_dak_non_fisik')->default(0);
            $table->double('nilai_blud')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sumber_anggaran');
    }
};
