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
        Schema::table('kode_nomenklatur', function (Blueprint $table) {
            $table->text('nomenklatur')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kode_nomenklatur', function (Blueprint $table) {
            $table->string('nomenklatur')->change();
        });
    }
};
