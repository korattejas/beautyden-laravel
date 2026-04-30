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
        Schema::table('service_subcategories', function (Blueprint $table) {
            $table->decimal('starting_at_price', 10, 2)->default(0)->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_subcategories', function (Blueprint $table) {
            $table->dropColumn('starting_at_price');
        });
    }
};
