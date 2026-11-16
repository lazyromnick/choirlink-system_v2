<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mang_aawit_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mang_aawit_record_id')->unique()->constrained('mang_aawits')->restrictOnDelete();
            $table->string('password', 255);
            $table->string('account_status', 10)->default('Active');
            $table->boolean('must_change_password')->default(true);
            $table->timestamps();
        });

        DB::statement("ALTER TABLE mang_aawit_accounts ADD CONSTRAINT mang_aawit_accounts_status_check CHECK (account_status IN ('Active', 'Inactive'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('mang_aawit_accounts');
    }
};