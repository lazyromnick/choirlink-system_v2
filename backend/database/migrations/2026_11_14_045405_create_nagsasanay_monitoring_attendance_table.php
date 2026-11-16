<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nagsasanay_monitoring_attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')
                ->constrained('nagsasanay_monitoring_sessions')
                ->cascadeOnDelete();
            $table->foreignId('nagsasanay_id')
                ->constrained('nagsasanay')
                ->cascadeOnDelete();
            $table->boolean('is_present')->default(false);
            $table->timestamps();

            $table->unique(['session_id', 'nagsasanay_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nagsasanay_monitoring_attendance');
    }
};