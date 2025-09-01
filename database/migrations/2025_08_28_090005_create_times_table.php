<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void//service_id, start_time, end_time, day_of_week, session_time
    {
        Schema::create('times', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->references('id')->on('services')->onUpdate('cascade')->onDelete('cascade');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('day_of_week');
            $table->integer('session_time');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('times');
    }
};
