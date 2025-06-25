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
        Schema::create('arsip_monitoring', function (Blueprint $table) {
            $table->id();
            $table->foreignId('skpd_tugas_id')->constrained('skpd_tugas');
            $table->string('periode'); // 'rencana_awal', 'triwulan_1', 'triwulan_2', 'triwulan_3', 'triwulan_4'
            $table->year('tahun');
            $table->string('nama_file');
            $table->string('path_file');
            $table->integer('ukuran_file'); // dalam bytes
            $table->string('tipe_file'); // pdf, doc, etc
            $table->timestamp('tanggal_upload');
            $table->foreignId('uploaded_by')->constrained('users');
            $table->text('keterangan')->nullable();
            $table->timestamps();
            
            // Index untuk pencarian cepat
            $table->index(['skpd_tugas_id', 'periode', 'tahun']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('arsip_monitoring');
    }
}; 