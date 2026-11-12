<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mang_aawit_memberships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mang_aawit_record_id')->constrained('mang_aawits')->restrictOnDelete();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->boolean('is_current');
            $table->string('membership_classification', 20);
            $table->string('designation', 20);
            $table->string('tungkulin', 20);
            $table->string('tungkulin_status', 10);
            $table->string('membership_status', 20);
            $table->string('mang_aawit_in', 3);
            $table->string('previous_locale', 150)->nullable();
            $table->string('destination_locale', 150)->nullable();
            $table->date('transfer_date')->nullable();
            $table->timestamps();
        });

        // Only one current membership per person
        DB::statement('CREATE UNIQUE INDEX mang_aawit_memberships_one_current_per_member ON mang_aawit_memberships (mang_aawit_record_id) WHERE is_current = TRUE');

        // Allowed values
        DB::statement("ALTER TABLE mang_aawit_memberships ADD CONSTRAINT memberships_classification_check CHECK (membership_classification IN ('Taga-lokal', 'Transfer-In', 'Transfer-Out'))");
        DB::statement("ALTER TABLE mang_aawit_memberships ADD CONSTRAINT memberships_designation_check CHECK (designation IN ('Regular', 'Nagsasanay'))");
        DB::statement("ALTER TABLE mang_aawit_memberships ADD CONSTRAINT memberships_tungkulin_check CHECK (tungkulin IN ('Mang-aawit', 'Organista'))");
        DB::statement("ALTER TABLE mang_aawit_memberships ADD CONSTRAINT memberships_tungkulin_status_check CHECK (tungkulin_status IN ('Active', 'Inactive'))");
        DB::statement("ALTER TABLE mang_aawit_memberships ADD CONSTRAINT memberships_status_check CHECK (membership_status IN ('In-locale', 'Transfer-Out'))");
        DB::statement("ALTER TABLE mang_aawit_memberships ADD CONSTRAINT memberships_service_check CHECK (mang_aawit_in IN ('AWS', 'CWS'))");

        // Current period must have no end date; closed period must have one
        DB::statement('ALTER TABLE mang_aawit_memberships ADD CONSTRAINT memberships_current_period_check CHECK (
            (is_current = TRUE AND end_date IS NULL) OR
            (is_current = FALSE AND end_date IS NOT NULL)
        )');

        // Transfer field requirements per classification
        DB::statement("ALTER TABLE mang_aawit_memberships ADD CONSTRAINT memberships_transfer_check CHECK (
            (membership_classification = 'Taga-lokal' AND previous_locale IS NULL AND destination_locale IS NULL AND transfer_date IS NULL) OR
            (membership_classification = 'Transfer-In' AND BTRIM(previous_locale) <> '' AND previous_locale IS NOT NULL AND destination_locale IS NULL AND transfer_date IS NOT NULL) OR
            (membership_classification = 'Transfer-Out' AND BTRIM(destination_locale) <> '' AND destination_locale IS NOT NULL AND previous_locale IS NULL AND transfer_date IS NOT NULL)
        )");
    }

    public function down(): void
    {
        Schema::dropIfExists('mang_aawit_memberships');
    }
};