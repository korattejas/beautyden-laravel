<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')
                ->constrained('service_categories')
                ->onDelete('cascade');
            $table->string('name', 150);
            $table->decimal('price', 8, 2);
            $table->decimal('discount_price', 8, 2)->nullable();
            $table->string('duration', 50);
            $table->decimal('rating', 3, 1)->nullable();
            $table->integer('reviews')->nullable();
            $table->text('description');
            $table->json('includes')->nullable();
            $table->string('icon')->nullable();
            $table->tinyInteger('is_popular')->default(1)->comment('1 = Yes, 0 = No');
            $table->tinyInteger('status')->default(1)->comment('1 = Active, 0 = InActive');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
