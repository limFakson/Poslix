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
        Schema::create('workinghours', function (Blueprint $table) {
            $table->id();
            $table->string('user_id');
            $table->boolean('enable_workinghours')->default(true);
            $table->boolean('sunday')->default(false);
            $table->time('sunday_first_time_start')->nullable();
            $table->time('sunday_first_time_end')->nullable();
            $table->boolean('sunday_second_time_enable')->default(false);
            $table->time('sunday_second_time_start')->nullable();
            $table->time('sunday_second_time_end')->nullable();
            $table->boolean('monday')->default(false);
            $table->time('monday_first_time_start')->nullable();
            $table->time('monday_first_time_end')->nullable();
            $table->boolean('monday_second_time_enable')->default(false);
            $table->time('monday_second_time_start')->nullable();
            $table->time('monday_second_time_end')->nullable();
            $table->boolean('tuesday')->default(false);
            $table->time('tuesday_first_time_start')->nullable();
            $table->time('tuesday_first_time_end')->nullable();
            $table->boolean('tuesday_second_time_enable')->default(false);
            $table->time('tuesday_second_time_start')->nullable();
            $table->time('tuesday_second_time_end')->nullable();
            $table->boolean('wednesday')->default(false);
            $table->time('wednesday_first_time_start')->nullable();
            $table->time('wednesday_first_time_end')->nullable();
            $table->boolean('wednesday_second_time_enable')->default(false);
            $table->time('wednesday_second_time_start')->nullable();
            $table->time('wednesday_second_time_end')->nullable();
            $table->boolean('thursday')->default(false);
            $table->time('thursday_first_time_start')->nullable();
            $table->time('thursday_first_time_end')->nullable();
            $table->boolean('thursday_second_time_enable')->default(false);
            $table->time('thursday_second_time_start')->nullable();
            $table->time('thursday_second_time_end')->nullable();
            $table->boolean('friday')->default(false);
            $table->time('friday_first_time_start')->nullable();
            $table->time('friday_first_time_end')->nullable();
            $table->boolean('friday_second_time_enable')->default(false);
            $table->time('friday_second_time_start')->nullable();
            $table->time('friday_second_time_end')->nullable();
            $table->boolean('saturday')->default(false);
            $table->time('saturday_first_time_start')->nullable();
            $table->time('saturday_first_time_end')->nullable();
            $table->boolean('saturday_second_time_enable')->default(false);
            $table->time('saturday_second_time_start')->nullable();
            $table->time('saturday_second_time_end')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workinghours');
    }
};