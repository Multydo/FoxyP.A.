<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table -> string("owner_id");
            $table ->time('work_from');
            $table ->time('work_to');
            $table ->time('break_time');
            $table ->integer('time_zone');
            $table ->string('logic');
            $table ->integer('max_app');
            $table ->string('monday');
            $table ->string('tuesday');
            $table ->string('wednesday');
            $table ->string('thursday');
            $table ->string('friday');
            $table ->string('saturday');
            $table ->string('sunday');
            $table ->boolean('max_duration_swicth');
            $table ->time('max_duration_time');
            $table ->boolean('min_time_switch');
            $table ->time('min_time');
            $table ->boolean('app_fixed_duration_switch');
            $table ->time('app_fixed_duration');
            $table ->boolean('allow_dm');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};