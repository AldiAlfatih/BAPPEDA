<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('monitoring', function (Blueprint $table) {
            $table->id();
            $table->foreignId('skpd_tugas_id')->constrained('skpd_tugas');
            // $table->string('sumber_dana');
            $table->foreignId('periode_id')->nullable()->constrained('periode')->onDelete('cascade');
            $table->year('tahun');
            $table->text('deskripsi');
            $table->string('nama_pptk');
            // $table->integer('pagu_anggaran');
            $table->timestamps();
        });
    
            Schema::create('sumber_anggaran', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
});  

             Schema::create('monitoring_anggaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sumber_anggaran_id')->constrained('sumber_anggaran');
            // $table->foreignId('periode_id')->nullable()->constrained('periode')->onDelete('cascade');
            // $table->integer('kinerja_fisik');
            // $table->integer('keuangan');
            $table->foreignId('monitoring_id')->constrained('monitoring');

            $table->timestamps();
}); 

             Schema::create('monitoring_pagu', function (Blueprint $table) {
            $table->id();
            $table->foreignId('monitoring_anggaran_id')->constrained('monitoring_anggaran');
            $table->foreignId('periode_id')->nullable()->constrained('periode')->onDelete('cascade');
            $table->integer('kategori'); //pkok, parsial, perubahan
            $table->integer('dana'); //RP
            $table->timestamps();
}); 

        Schema::create('monitoring_realisasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('monitoring_anggaran_id')->constrained('monitoring_anggaran');
            $table->foreignId('periode_id')->nullable()->constrained('periode')->onDelete('cascade');
            $table->integer('kinerja_fisik');
            $table->integer('keuangan');
            $table->timestamps();
        });

        Schema::create('monitoring_target', function (Blueprint $table) {
            $table->id();
            $table->foreignId('monitoring_anggaran_id')->constrained('monitoring_anggaran');
            $table->foreignId('periode_id')->nullable()->constrained('periode')->onDelete('cascade');
            $table->integer('kinerja_fisik');
            $table->integer('keuangan');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('monitoring_target');
        Schema::dropIfExists('monitoring_realisasi');
         Schema::dropIfExists('monitoring_pagu');
         Schema::dropIfExists('monitoring_anggaran');
         Schema::dropIfExists('sumber_anggaran');
        Schema::dropIfExists('monitoring');
}
};