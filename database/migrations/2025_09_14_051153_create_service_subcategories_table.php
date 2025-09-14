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
        Schema::create('service_subcategories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('service_category_id'); // foreign key
            $table->string('name', 150);
            $table->string('icon')->nullable();
            $table->text('description')->nullable();
            $table->tinyInteger('is_popular')->default(0)->comment('0 = No, 1 = Yes');
            $table->tinyInteger('status')->default(1)->comment('1 = Active, 0 = InActive');
            $table->timestamps();

            // foreign key constraint
            $table->foreign('service_category_id')
                  ->references('id')
                  ->on('service_categories')
                  ->onDelete('cascade'); // category delete thay to subcategories pan delete thase
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_subcategories');
    }
};
