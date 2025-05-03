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
        Schema::create('bantuan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('judul');
            $table->integer('status')->default(0);
            $table->timestamps();
        });

        Schema::create('bantuan_faqs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bantuan_id')->constrained('bantuan')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->text('deskripsi')->nullable();
            $table->text('balasan')->nullable();
            $table->string('file')->nullable();
            $table->timestamps();
        }); 
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bantuan_faqs');
        Schema::dropIfExists('bantuan');
    }
};