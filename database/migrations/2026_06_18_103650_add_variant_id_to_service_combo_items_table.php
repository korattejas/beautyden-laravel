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
        Schema::table('service_combo_items', function (Blueprint $table) {
            $table->unsignedBigInteger('variant_id')->nullable()->after('service_master_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_combo_items', function (Blueprint $table) {
            $table->dropColumn('variant_id');
        });
    }
};
