<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nagsasanay_monitoring_sessions', function (Blueprint $table) {
            $table->id();
            $table->date('session_date');
            $table->integer('month');
            $table->integer('year');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nagsasanay_monitoring_sessions');
    }
};