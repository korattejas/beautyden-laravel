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
        Schema::create('category_lookbooks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id')->unique(); // one lookbook per category
            $table->json('photos')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('service_categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_lookbooks');
    }
};
