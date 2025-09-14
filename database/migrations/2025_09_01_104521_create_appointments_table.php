<?php

use App\Enums\AppointmentStatusEnum;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained('services')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('phone_id')->nullable()->constrained('phones')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('email_id')->nullable()->constrained('emails')->cascadeOnUpdate()->cascadeOnDelete();
            $table->date('date');
            $table->time('start_at');
            $table->time('end_at');
            $table->text('note')->nullable();
            $table->tinyInteger('status')->default(AppointmentStatusEnum::PENDING->value);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
