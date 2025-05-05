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
        Schema::create('pemberitahuan', function (Blueprint $table) {
            $table->id();
            $table->string('judul', 255);  
            $table->text('isi'); 
            $table->date('tanggal_dibuat'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('pemberitahuan');
    }
};