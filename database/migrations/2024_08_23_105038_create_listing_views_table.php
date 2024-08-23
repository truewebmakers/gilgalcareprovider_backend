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
        Schema::create('listing_views', function (Blueprint $table) {
            Schema::create('listing_views', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('business_listing_id');
                $table->string('ip_address')->unique();
                $table->timestamps();

                $table->foreign('business_listing_id')->references('id')->on('business_listings')->onDelete('cascade');
             });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('listing_views');
    }
};
