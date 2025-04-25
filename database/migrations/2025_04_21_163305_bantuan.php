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
        Schema::create('status_bantuan', function (Blueprint $table) {
            $table->id();
            $table->string('status');
        }); 

        Schema::create('bantuan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('status_bantuan_id')->constrained('status_bantuan');
            $table->string('jenis_bantuan');
            $table->string('penerima');
            $table->date('tanggal_disalurkan');
            $table->timestamps(); // created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bantuan');
    }
};