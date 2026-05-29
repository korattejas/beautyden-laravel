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
        Schema::create('service_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->string('icon', 255)->nullable();
            $table->text('description')->nullable();
            $table->tinyInteger('is_popular')->default(0)->comment('0 = No, 1 = Yes');
            $table->boolean('is_new')->default(0);
            $table->tinyInteger('status')->default(1)->comment('0 = inactive, 1 = active/approve, 2 = pending 3 = rejected');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_types');
    }
};
