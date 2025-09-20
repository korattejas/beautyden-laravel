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
        Schema::create('contracts_signed', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('provider_id')->nullable();
            $table->string('provider_name',100);
            $table->string('provider_mobile',20);
            $table->longText('provider_address')->nullable();
            $table->string('contract_type',150)->nullable(); // e.g. NDA, Service Agreement
            $table->string('signed_pdf')->nullable(); // path to saved PDF
            $table->string('signature_image')->nullable(); // base64 signature
            $table->string('ip_address')->nullable();
            $table->timestamp('signed_at')->nullable();
            $table->tinyInteger('status')->default(0)->comment('1 = Signed, 0 = Pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contracts_signed');
    }
};
