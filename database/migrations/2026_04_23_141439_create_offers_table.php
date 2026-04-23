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
        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->enum('position', ['top_header', 'footer', 'other'])->default('top_header');
            $table->enum('media_type', ['image', 'video'])->default('image');
            $table->text('media')->nullable(); // Store JSON array for multiple images or single video path
            $table->string('link')->nullable();
            $table->integer('priority')->default(0);
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offers');
    }
};
