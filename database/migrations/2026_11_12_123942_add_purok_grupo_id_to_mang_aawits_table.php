<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mang_aawits', function (Blueprint $table) {
            $table->foreignId('purok_grupo_id')
                ->nullable()
                ->constrained('purok_grupo')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('mang_aawits', function (Blueprint $table) {
            $table->dropForeign(['purok_grupo_id']);
            $table->dropColumn('purok_grupo_id');
        });
    }
};