<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('beach_classes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('level');
            $table->string('weekday');
            $table->time('starts_at');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone', 30)->nullable();
            $table->foreignId('beach_class_id')->nullable()->constrained('beach_classes')->nullOnDelete();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('monthly_fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->date('due_date');
            $table->date('paid_at')->nullable();
            $table->string('status')->default('pending');
            $table->string('reference');
            $table->timestamps();
            $table->unique(['student_id', 'reference']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monthly_fees');
        Schema::dropIfExists('students');
        Schema::dropIfExists('beach_classes');
    }
};
