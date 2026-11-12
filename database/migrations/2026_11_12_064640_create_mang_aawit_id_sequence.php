<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('CREATE SEQUENCE IF NOT EXISTS mang_aawit_id_sequence START WITH 1 INCREMENT BY 1 NO CYCLE');
    }

    public function down(): void
    {
        // Intentionally empty — sequence numbers must never be reused
    }
};