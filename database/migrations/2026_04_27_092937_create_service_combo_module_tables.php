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
        // 1. Service Combos Master Table (Simplified)
        Schema::create('service_combos', function (Blueprint $col) {
            $col->id();
            $col->string('name');
            $col->text('description')->nullable();
            $col->string('image')->nullable();
            $col->decimal('min_price', 10, 2)->default(0.00)->comment('Global minimum order value for this combo');
            $col->tinyInteger('status')->default(1)->comment('1=Active, 0=Inactive');
            $col->timestamps();
        });

        // 2. Combo Items (Linking existing ServiceMaster)
        Schema::create('service_combo_items', function (Blueprint $col) {
            $col->id();
            $col->unsignedBigInteger('combo_id');
            $col->unsignedBigInteger('service_master_id');
            $col->tinyInteger('is_default')->default(1)->comment('1=Pre-selected, 0=Optional Add-on');
            $col->timestamps();

            $col->foreign('combo_id')->references('id')->on('service_combos')->onDelete('cascade');
            $col->foreign('service_master_id')->references('id')->on('service_masters')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_combo_items');
        Schema::dropIfExists('service_combos');
    }
};
