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
        Schema::table('service_categories', function (Blueprint $table) {
            $table->unsignedBigInteger('service_type_id')->nullable()->after('id');
            // If you want a foreign key, you could add: $table->foreign('service_type_id')->references('id')->on('service_types')->onDelete('set null');
            // But let's keep it simple for now as requested.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_categories', function (Blueprint $table) {
            $table->dropColumn('service_type_id');
        });
    }
};
