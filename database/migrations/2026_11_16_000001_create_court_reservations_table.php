<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('court_reservations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone', 30);
            $table->unsignedTinyInteger('participant_count')->default(4);
            $table->date('reservation_date');
            $table->time('starts_at');
            $table->time('ends_at');
            $table->timestamps();
            $table->unique(['reservation_date', 'starts_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('court_reservations');
    }
};
