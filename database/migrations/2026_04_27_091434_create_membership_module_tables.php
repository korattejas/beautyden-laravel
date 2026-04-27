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
        // 1. Membership Plans Table
        Schema::create('membership_plans', function (Blueprint $col) {
            $col->id();
            $col->string('name');
            $col->text('description')->nullable();
            $col->decimal('price', 10, 2);
            $col->integer('discount_percentage')->default(0);
            $col->integer('duration_months')->comment('1, 3, 6, 12 months');
            $col->tinyInteger('status')->default(1)->comment('1=Active, 0=Inactive');
            $col->timestamps();
        });

        // 2. User Subscriptions Table
        Schema::create('user_subscriptions', function (Blueprint $col) {
            $col->id();
            $col->unsignedBigInteger('user_id');
            $col->unsignedBigInteger('plan_id');
            $col->date('start_date');
            $col->date('end_date');
            $col->decimal('price_paid', 10, 2);
            $col->string('payment_id')->nullable();
            $col->tinyInteger('status')->default(1)->comment('1=Active, 0=Expired/Cancelled');
            $col->timestamps();

            $col->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $col->foreign('plan_id')->references('id')->on('membership_plans')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_subscriptions');
        Schema::dropIfExists('membership_plans');
    }
};
