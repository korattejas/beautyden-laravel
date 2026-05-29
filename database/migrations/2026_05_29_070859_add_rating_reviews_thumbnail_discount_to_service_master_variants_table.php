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
        Schema::table('service_master_variants', function (Blueprint $table) {
            $table->decimal('rating', 2, 1)->default(0)->after('duration');
            $table->unsignedInteger('reviews')->default(0)->after('rating');
            $table->string('thumbnail_image')->nullable()->after('reviews');
            $table->decimal('discount_percentage', 5, 2)->nullable()->after('thumbnail_image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_master_variants', function (Blueprint $table) {
            $table->dropColumn(['rating', 'reviews', 'thumbnail_image', 'discount_percentage']);
        });
    }
};
