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
        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->string('state', 50);
            $table->string('area', 50)->nullable();
            $table->string('slug')->unique()->nullable();
            $table->string('icon')->nullable();
            $table->string('launch_quarter')->nullable();
            $table->tinyInteger('is_popular')->default(1)->comment('1 = Yes, 0 = No');
            $table->tinyInteger('status')->default(1)->comment('1 = Active, 0 = InActive');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cities');
    }
};
