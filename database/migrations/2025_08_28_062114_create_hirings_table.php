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
        Schema::create('hirings', function (Blueprint $table) {
            $table->id();
            $table->string('title',150);
            $table->text('description')->nullable();
            $table->string('city',100);
            $table->integer('min_experience')->nullable();
            $table->integer('max_experience')->nullable();
            $table->string('salary_range',150)->nullable();
            $table->tinyInteger('experience_level')->default(2)->comment('1 = Fresher, 2 = Experienced, 3 = Expert');
            $table->tinyInteger('hiring_type')->default(4)->comment('1 = Full-time, 2 = Part-time, 3 = Internship, 4 = Work from home');
            $table->tinyInteger('gender_preference')->default(3)->comment('1 = Female, 2 = Male, 3 = Any');
            $table->json('required_skills')->nullable();  
            $table->tinyInteger('is_popular')->default(1)->comment('1 = Yes, 0 = No');     
            $table->tinyInteger('status')->default(1)->comment('1 = Active, 0 = InActive');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hirings');
    }
};
