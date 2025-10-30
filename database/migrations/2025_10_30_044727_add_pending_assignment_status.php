<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add 'pending_assignment' to the status enum
        DB::statement("ALTER TABLE service_requests MODIFY COLUMN status ENUM('submitted', 'assessed', 'assigned', 'pending_assignment', 'in_progress', 'completed', 'rejected', 'cancelled') NOT NULL DEFAULT 'submitted'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove 'pending_assignment' from the status enum
        DB::statement("ALTER TABLE service_requests MODIFY COLUMN status ENUM('submitted', 'assessed', 'assigned', 'in_progress', 'completed', 'rejected', 'cancelled') NOT NULL DEFAULT 'submitted'");
    }
};
