<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->index(['status', 'billing_month']);
        });

        Schema::table('visitors', function (Blueprint $table) {
            $table->index('status');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->index('payment_method');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropIndex(['status', 'billing_month']);
        });

        Schema::table('visitors', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['payment_method']);
        });
    }
};
