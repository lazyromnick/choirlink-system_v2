<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mang_aawits', function (Blueprint $table) {
            $table->id();
            $table->string('mang_aawit_id', 20)->unique();
            $table->string('first_name', 100);
            $table->string('middle_name', 100)->nullable();
            $table->string('last_name', 100);
            $table->string('suffix', 20)->nullable();
            $table->date('birthday');
            $table->string('sex', 10);
            $table->text('address');
            $table->date('date_registered')->default(DB::raw('CURRENT_DATE'));
            $table->softDeletes();
            $table->timestamps();
        });

        DB::statement('ALTER TABLE mang_aawits ADD CONSTRAINT mang_aawits_sex_check CHECK (sex IN (\'Male\', \'Female\'))');
    }

    public function down(): void
    {
        Schema::dropIfExists('mang_aawits');
    }
};