<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('team_members', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('phone', 20)->nullable();
            $table->string('role', 150)->nullable();
            $table->integer('experience_years')->nullable();
            $table->json('specialties')->nullable();
            $table->text('bio')->nullable();
            $table->string('icon', 255)->nullable(); 
            $table->json('certifications')->nullable();
            $table->string('state', 150)->nullable();
            $table->string('city', 150)->nullable();
            $table->string('taluko', 150)->nullable();
            $table->string('village', 150)->nullable();
            $table->longText('address')->nullable();
            $table->tinyInteger('is_popular')->default(0)->comment('1 = Yes, 0 = No');
            $table->tinyInteger('status')->default(1)->comment('1 = Active, 0 = InActive');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('team_members');
    }
};
