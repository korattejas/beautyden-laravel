<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('blog_categories')->onDelete('cascade');
            $table->string('title', 200);           
            $table->text('excerpt')->nullable();                
            $table->longText('content')->nullable();           
            $table->string('read_time', 50)->nullable();       
            $table->string('author', 100)->nullable();         
            $table->date('publish_date')->nullable();           
            $table->json('tags')->nullable();       
            $table->string('icon')->nullable();
            $table->longText('meta_keywords')->nullable();
            $table->longText('meta_description')->nullable();
            $table->tinyInteger('featured')->default(0)->comment('1 = Featured, 0 = Normal');
            $table->tinyInteger('status')->default(1)->comment('1 = Active, 0 = InActive');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blogs');
    }
};
