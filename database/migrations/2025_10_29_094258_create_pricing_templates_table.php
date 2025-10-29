<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pricing_templates', function (Blueprint $table) {
            $table->id();
            $table->enum('service_type', ['breakdown', 'maintenance', 'installation']);
            $table->string('description');
            $table->decimal('labor_cost_per_hour', 10, 2);
            $table->decimal('minimum_charge', 10, 2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pricing_templates');
    }
};