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
        Schema::create('beautician_settlements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('team_member_id')->unique();
            $table->decimal('company_to_beautician', 15, 2)->default(0);
            $table->decimal('beautician_to_company', 15, 2)->default(0);
            $table->timestamps();

            $table->foreign('team_member_id')->references('id')->on('team_members')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('beautician_settlements');
    }
};
