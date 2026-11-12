<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nagsasanay', function (Blueprint $table) {
            $table->id();
            $table->string('mang_aawit_id', 20)->unique();
            $table->string('training_track', 10); // 'Mang-aawit' or 'Organista'

            // For Mang-aawit track — personal info
            $table->string('first_name', 100)->nullable();
            $table->string('middle_name', 100)->nullable();
            $table->string('last_name', 100)->nullable();
            $table->string('suffix', 20)->nullable();
            $table->date('birthday')->nullable();
            $table->string('sex', 10)->nullable();
            $table->text('address')->nullable();
            $table->date('date_registered')->default(DB::raw('CURRENT_DATE'));

            // For Organista track — link to existing mang-aawit
            $table->foreignId('mang_aawit_record_id')
                ->nullable()
                ->constrained('mang_aawits')
                ->restrictOnDelete();

            // Training info (both tracks)
            $table->string('training_category', 20); // Mang-aawit or Organista
            $table->date('training_start_date');
            $table->string('trainer_name', 150);
            $table->string('progress_stage', 30)->default('Newly Registered');
            $table->string('training_status', 10)->default('Active');
            $table->text('remarks')->nullable();

            // Membership details filled in when completing (Mang-aawit track only)
            $table->string('membership_classification', 20)->nullable();
            $table->string('mang_aawit_in', 3)->nullable();
            $table->string('previous_locale', 150)->nullable();
            $table->string('destination_locale', 150)->nullable();
            $table->date('transfer_date')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });

        DB::statement("ALTER TABLE nagsasanay ADD CONSTRAINT nagsasanay_training_track_check CHECK (training_track IN ('Mang-aawit', 'Organista'))");
        DB::statement("ALTER TABLE nagsasanay ADD CONSTRAINT nagsasanay_progress_stage_check CHECK (progress_stage IN ('Newly Registered', 'On Going', 'For Evaluation', 'Completed', 'Discontinued'))");
        DB::statement("ALTER TABLE nagsasanay ADD CONSTRAINT nagsasanay_training_status_check CHECK (training_status IN ('Active', 'Inactive'))");
        DB::statement("ALTER TABLE nagsasanay ADD CONSTRAINT nagsasanay_sex_check CHECK (sex IS NULL OR sex IN ('Male', 'Female'))");
        DB::statement("ALTER TABLE nagsasanay ADD CONSTRAINT nagsasanay_mang_aawit_in_check CHECK (mang_aawit_in IS NULL OR mang_aawit_in IN ('AWS', 'CWS'))");
        DB::statement("ALTER TABLE nagsasanay ADD CONSTRAINT nagsasanay_classification_check CHECK (membership_classification IS NULL OR membership_classification IN ('Taga-lokal', 'Transfer-In', 'Transfer-Out'))");

        // Mang-aawit track must have personal info, Organista track must have mang_aawit_record_id
        DB::statement("ALTER TABLE nagsasanay ADD CONSTRAINT nagsasanay_track_check CHECK (
            (training_track = 'Mang-aawit' AND first_name IS NOT NULL AND last_name IS NOT NULL AND birthday IS NOT NULL AND sex IS NOT NULL AND address IS NOT NULL AND mang_aawit_record_id IS NULL) OR
            (training_track = 'Organista' AND mang_aawit_record_id IS NOT NULL AND first_name IS NULL AND last_name IS NULL)
        )");
    }

    public function down(): void
    {
        Schema::dropIfExists('nagsasanay');
    }
};