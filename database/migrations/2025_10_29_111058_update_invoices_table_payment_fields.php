<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add fields to invoices table if they don't exist
        Schema::table('invoices', function (Blueprint $table) {
            // Add these columns if they're missing
            if (!Schema::hasColumn('invoices', 'payment_status')) {
                $table->enum('payment_status', ['pending', 'proof_uploaded', 'verified', 'rejected'])
                    ->default('pending')
                    ->after('total_amount');
            }

            if (!Schema::hasColumn('invoices', 'payment_verified_at')) {
                $table->timestamp('payment_verified_at')->nullable()->after('payment_status');
            }

            if (!Schema::hasColumn('invoices', 'invoice_number')) {
                $table->string('invoice_number')->unique()->after('id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            if (Schema::hasColumn('invoices', 'payment_status')) {
                $table->dropColumn('payment_status');
            }
            if (Schema::hasColumn('invoices', 'payment_verified_at')) {
                $table->dropColumn('payment_verified_at');
            }
            if (Schema::hasColumn('invoices', 'invoice_number')) {
                $table->dropColumn('invoice_number');
            }
        });
    }
};