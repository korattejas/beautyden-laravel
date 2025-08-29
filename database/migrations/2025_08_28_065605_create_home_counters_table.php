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
        Schema::create('home_counters', function (Blueprint $table) {
            $table->id();
            $table->string('label', 100);
            $table->string('value', 50);
            $table->string('icon')->nullable();
            $table->tinyInteger('status')->default(1)->comment('1 = Active, 0 = InActive');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('home_counters');
    }
};
