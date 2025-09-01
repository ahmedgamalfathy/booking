<?php

use App\Enums\TypeEnum;
use App\Enums\StatusEnum;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.//name , color , price , status , type
     */
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('color')->default('#0055CC');
            $table->string('path')->nullable();
            $table->decimal('price', 5, 2)->default(0);
            $table->tinyInteger('status')->default(StatusEnum::ACTIVE->value);
            $table->tinyInteger('type')->default(TypeEnum::OFFLINE->value);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
