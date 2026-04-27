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
        Schema::create('staff_unavailabilities', function (Blueprint $col) {
            $col->id();
            $col->unsignedBigInteger('team_member_id');
            $col->date('start_date');
            $col->date('end_date');
            $col->string('start_time')->nullable(); // Optional: if they are away for part of the day
            $col->string('end_time')->nullable();   // Optional
            $col->text('reason')->nullable();
            $col->tinyInteger('type')->default(1)->comment('1=Leave, 2=Personal, 3=Sick, 4=Holiday');
            $col->tinyInteger('status')->default(1)->comment('1=Active, 0=Cancelled');
            $col->timestamps();

            $col->foreign('team_member_id')->references('id')->on('team_members')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_unavailabilities');
    }
};
