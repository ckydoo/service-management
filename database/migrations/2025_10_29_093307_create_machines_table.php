<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('machines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->string('machine_name');
            $table->string('model');
            $table->string('serial_number')->unique();
            $table->string('manufacturer');
            $table->year('year_of_manufacture');
            $table->string('location');
            $table->text('specifications')->nullable();
            $table->timestamp('last_service_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('machines');
    }
};
