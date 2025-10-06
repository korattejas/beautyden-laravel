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
        Schema::create('customer_reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('service_id');
            $table->string('customer_name', 100);
            $table->string('customer_photo', 255)->nullable();
            $table->decimal('rating', 3, 1)->nullable(); 
            $table->text('review')->nullable();
            $table->date('review_date');
            $table->integer('helpful_count')->default(0);
            $table->json('photos')->nullable();
            $table->string('video', 255)->nullable();
            $table->tinyInteger('is_popular')->default(1)->comment('1 = Yes, 0 = No');
            $table->tinyInteger('status')->default(1)->comment('1 = Active, 0 = InActive');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_reviews');
    }
};
