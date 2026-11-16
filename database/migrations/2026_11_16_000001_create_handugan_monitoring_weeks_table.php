<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('handugan_monitoring_weeks', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('week_number');
            $table->unsignedTinyInteger('month');
            $table->unsignedSmallInteger('year');
            $table->date('start_date')->unique();
            $table->date('end_date');
            $table->unsignedInteger('denominator_snapshot');
            $table->unsignedInteger('th_count')->nullable();
            $table->unsignedInteger('lingap_count')->nullable();
            $table->unsignedInteger('lagak_count')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('handugan_monitoring_weeks');
    }
};