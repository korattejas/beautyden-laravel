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
        Schema::create('razorpay_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('razorpay_order_id')->index();
            $table->string('razorpay_payment_id')->nullable()->index();
            $table->string('razorpay_signature')->nullable();
            $table->decimal('amount', 12, 2);
            $table->string('currency', 10)->default('INR');
            $table->string('status')->comment('created, authorized, captured, refunded, failed');
            $table->string('method')->nullable()->comment('card, netbanking, wallet, upi');
            $table->json('payment_details')->nullable()->comment('Raw JSON from Razorpay');
            $table->json('meta_data')->nullable()->comment('Service details, appointments info etc');
            $table->string('error_code')->nullable();
            $table->text('error_description')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('razorpay_transactions');
    }
};
