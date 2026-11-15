<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gawain_monitoring_nagsasanay_attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('week_id')->constrained('gawain_monitoring_weeks')->cascadeOnDelete();
            $table->foreignId('nagsasanay_id')->constrained('nagsasanay')->cascadeOnDelete();
            $table->boolean('is_present')->default(false);
            $table->timestamps();

            $table->unique(['week_id', 'nagsasanay_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gawain_monitoring_nagsasanay_attendance');
    }
};