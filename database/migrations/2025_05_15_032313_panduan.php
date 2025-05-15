<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('panduan', function (Blueprint $table) {
            $table->id();
            $table->string('judul', 255);  // Menyimpan judul dengan panjang maksimal 255 karakter
            $table->text('deskripsi');     // Menyimpan deskripsi dengan panjang yang lebih besar
            $table->string('file', 512)->nullable()->change();
            $table->string('sampul')->nullable()->change();  // Menyimpan path file dengan panjang maksimal 512 karakter
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
       Schema::table('panduan', function (Blueprint $table) {
            // Kembalikan ke kondisi semula jika diperlukan
            $table->string('file')->nullable(false)->change();
            $table->string('sampul')->nullable(false)->change();
        });
    }
};
