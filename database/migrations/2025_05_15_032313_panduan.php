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
            $table->string('file', 512);
            $table->string('sampul')->nullable();  // Menyimpan path file dengan panjang maksimal 512 karakter
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('panduan');
    }
};
