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
        Schema::table('skpd', function (Blueprint $table) {
            $table->string('nama_dinas')->nullable()->after('nama_skpd');
            $table->string('no_dpa')->nullable()->after('kode_organisasi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('skpd', function (Blueprint $table) {
            $table->dropColumn(['nama_dinas', 'no_dpa']);
        });
    }
};
