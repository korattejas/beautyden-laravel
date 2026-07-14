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
        Schema::table('appointments', function (Blueprint $table) {
            $table->enum('user_payment_status', ['pending', 'paid', 'failed'])->default('pending')->after('payment_type');
            $table->enum('beautician_payment_status', ['pending', 'paid'])->default('pending')->after('user_payment_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn(['user_payment_status', 'beautician_payment_status']);
        });
    }
};
