<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mang_aawit_qr_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mang_aawit_account_id')->constrained('mang_aawit_accounts')->restrictOnDelete();
            $table->string('token_hash', 255)->unique();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('revoked_at')->nullable();
        });

        // Only one active QR token per account
        DB::statement('CREATE UNIQUE INDEX mang_aawit_qr_tokens_one_active_per_account ON mang_aawit_qr_tokens (mang_aawit_account_id) WHERE revoked_at IS NULL');
    }

    public function down(): void
    {
        Schema::dropIfExists('mang_aawit_qr_tokens');
    }
};