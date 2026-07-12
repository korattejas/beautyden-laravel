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
        Schema::table('category_lookbooks', function (Blueprint $table) {
            $table->unsignedBigInteger('sub_category_id')->nullable()->after('category_id');

            // Drop the old unique constraint on category_id since it might not be unique anymore
            // if we have lookbooks for multiple subcategories under the same category
            $table->dropUnique(['category_id']);

            // Create a new composite unique index if needed, or just let it be.
            // A category + sub_category combination should be unique.
            // If sub_category_id is NULL, MySQL allows multiple NULLs in a unique index (but in InnoDB it is distinct).
            // Let's just create a unique constraint.
            $table->unique(['category_id', 'sub_category_id'], 'cat_subcat_unique');
            
            $table->foreign('sub_category_id')->references('id')->on('service_subcategories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('category_lookbooks', function (Blueprint $table) {
            $table->dropForeign(['sub_category_id']);
            $table->dropUnique('cat_subcat_unique');
            
            $table->unique(['category_id']);
            $table->dropColumn('sub_category_id');
        });
    }
};
