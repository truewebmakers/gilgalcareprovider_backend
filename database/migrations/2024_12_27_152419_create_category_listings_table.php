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


        Schema::create('category_listings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('business_listing_id'); // Foreign key for business_listing
            $table->unsignedBigInteger('category_id'); // Foreign key for category

            // Add foreign key constraints
            $table->foreign('business_listing_id')->references('id')->on('business_listings')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_listings');
    }
};
