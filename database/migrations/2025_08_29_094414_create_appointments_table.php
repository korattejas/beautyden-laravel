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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('city_id')->nullable()->comment('Reference to city table');
            $table->unsignedBigInteger('service_category_id')->nullable()->comment('Reference to service categories table');
            $table->unsignedBigInteger('service_sub_category_id')->nullable();
            $table->string('service_id')->nullable()->comment('Reference to services table');
            $table->string('assigned_to', 100)->nullable()->comment('Multiple team ids comma separated');
            $table->string('assigned_by', 100)->nullable()->comment('Admin who assigned appointment');
            $table->string('order_number', 50);
            $table->string('first_name', 50);
            $table->string('last_name', 50)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('phone', 20)->nullable();
            $table->integer('quantity')->default(1);
            $table->decimal('price', 10, 2)->nullable();
            $table->decimal('discount_price', 10, 2)->nullable();
            $table->string('service_address')->nullable();
            $table->date('appointment_date')->nullable();
            $table->time('appointment_time')->nullable();
            $table->text('special_notes')->nullable();
            $table->tinyInteger('status')->default(1)->comment('1 = Pending, 2 = Assigned, 3 = Completed, 4 = Rejected')->change();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
