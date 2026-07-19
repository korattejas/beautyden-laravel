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
        Schema::table('coupon_codes', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('id')->comment('Null for all users');
            $table->string('color_code')->nullable()->after('code')->comment('Hex color code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coupon_codes', function (Blueprint $table) {
            $table->dropColumn(['user_id', 'color_code']);
        });
    }
};
