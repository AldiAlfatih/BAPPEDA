<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('monitoring', function (Blueprint $table) {
            $table->id();
            $table->foreignId('skpd_id')->constrained('skpd');
            $table->string('sumber_dana');
            $table->foreignId('periode_id')->nullable()->constrained('periode')->onDelete('cascade');
            $table->year('tahun');
            $table->text('deskripsi');
            $table->integer('pagu_anggaran');
            $table->timestamps();
        });
        Schema::create('monitoring_realisasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('monitoring_id')->constrained('monitoring');
            $table->foreignId('periode_id')->nullable()->constrained('periode')->onDelete('cascade');
            $table->integer('kinerja_fisik');
            $table->integer('keuangan');
            $table->timestamps();
        });

        Schema::create('monitoring_target', function (Blueprint $table) {
            $table->id();
            $table->foreignId('monitoring_id')->constrained('monitoring');
            $table->foreignId('periode_id')->nullable()->constrained('periode')->onDelete('cascade');
            $table->integer('kinerja_fisik');
            $table->integer('keuangan');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('monitoring_target');
        Schema::dropIfExists('monitoring_realisasi');
        Schema::dropIfExists('monitoring');
}
};
