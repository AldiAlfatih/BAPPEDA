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
<<<<<<< HEAD
            $table->integer('pagu_pokok')->nullable();
            $table->integer('pagu_parsial')->nullable();
            $table->integer(column: 'pagu_perubahan')->nullable();

=======
            $table->integer('pagu_pokok');
            $table->integer('pagu_parsial')->nullable();
            $table->integer('pagu_perubahan')->nullable();
>>>>>>> 2bf3b947d4508d4887650bd21bb12834090c1114
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
