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
        Schema::create('service_city_masters', function (Blueprint $col) {
            $col->id();
            $col->unsignedBigInteger('city_id');
            $col->unsignedBigInteger('category_id');
            $col->unsignedBigInteger('sub_category_id')->nullable();
            $col->unsignedBigInteger('service_master_id'); // Renamed to link to ServiceMaster
            $col->decimal('price', 10, 2)->default(0.00);
            $col->decimal('discount_price', 10, 2)->default(0.00);
            $col->decimal('app_discount_percentage', 5, 2)->default(0.00);
            $col->decimal('beautician_commission', 10, 2)->default(0.00);
            $col->tinyInteger('is_available')->default(1)->comment('1=Available, 0=Unavailable in this city');
            $col->tinyInteger('status')->default(1);
            $col->timestamps();

            $col->foreign('city_id')->references('id')->on('cities')->onDelete('cascade');
            $col->foreign('category_id')->references('id')->on('service_categories')->onDelete('cascade');
            $col->foreign('service_master_id')->references('id')->on('service_masters')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_city_masters');
    }
};
