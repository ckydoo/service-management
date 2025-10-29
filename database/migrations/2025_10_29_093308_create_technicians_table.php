<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('technicians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('specialization');
            $table->string('license_number')->unique();
            $table->decimal('current_location_lat', 10, 8)->nullable();
            $table->decimal('current_location_lng', 10, 8)->nullable();
            $table->enum('availability_status', ['available', 'busy', 'off_duty'])->default('available');
            $table->integer('current_workload')->default(0);
            $table->text('skills')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('technicians');
    }
};