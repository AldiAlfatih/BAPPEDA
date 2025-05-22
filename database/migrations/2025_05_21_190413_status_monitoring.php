<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('monitoring', function (Blueprint $table) {
            $table->boolean('is_finalized')->default(false)->after('pagu_perubahan');
        });
    }

    public function down(): void {
        Schema::table('monitoring', function (Blueprint $table) {
            $table->dropColumn('is_finalized');
        });
    }
};
