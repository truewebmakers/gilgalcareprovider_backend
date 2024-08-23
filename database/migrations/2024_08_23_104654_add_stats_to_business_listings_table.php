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
        Schema::table('business_listings', function (Blueprint $table) {
            $table->unsignedInteger('page_views')->default(0);
            $table->unsignedInteger('total_shares')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('business_listings', function (Blueprint $table) {
            $table->dropColumn(['page_views', 'total_shares']);

            //
        });
    }
};
