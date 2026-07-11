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
        Schema::table('service_masters', function (Blueprint $table) {
            $table->boolean('catalog_lookbook')->default(0)->after('status');
            $table->boolean('portfolio')->default(0)->after('catalog_lookbook');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_masters', function (Blueprint $table) {
            $table->dropColumn(['catalog_lookbook', 'portfolio']);
        });
    }
};
