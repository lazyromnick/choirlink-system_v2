<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mang_aawit_account_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mang_aawit_account_id')->constrained('mang_aawit_accounts')->restrictOnDelete();
            $table->string('event_type', 30);
            $table->string('previous_status', 10)->nullable();
            $table->string('new_status', 10)->nullable();
            $table->text('reason')->nullable();
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('actor_type', 10);
            $table->timestamp('created_at')->useCurrent();
        });

        DB::statement("ALTER TABLE mang_aawit_account_histories ADD CONSTRAINT account_histories_event_type_check CHECK (event_type IN ('Account Created', 'Activation', 'Deactivation', 'Password Reset'))");
        DB::statement("ALTER TABLE mang_aawit_account_histories ADD CONSTRAINT account_histories_actor_type_check CHECK (actor_type IN ('Admin', 'System'))");
        DB::statement("ALTER TABLE mang_aawit_account_histories ADD CONSTRAINT account_histories_status_check CHECK (
            (previous_status IS NULL OR previous_status IN ('Active', 'Inactive')) AND
            (new_status IS NULL OR new_status IN ('Active', 'Inactive'))
        )");
        DB::statement("ALTER TABLE mang_aawit_account_histories ADD CONSTRAINT account_histories_deactivation_reason_check CHECK (
            event_type <> 'Deactivation' OR (reason IS NOT NULL AND BTRIM(reason) <> '')
        )");
        DB::statement("ALTER TABLE mang_aawit_account_histories ADD CONSTRAINT account_histories_actor_relationship_check CHECK (
            (actor_type = 'Admin' AND changed_by IS NOT NULL) OR
            (actor_type = 'System' AND changed_by IS NULL)
        )");
    }

    public function down(): void
    {
        Schema::dropIfExists('mang_aawit_account_histories');
    }
};