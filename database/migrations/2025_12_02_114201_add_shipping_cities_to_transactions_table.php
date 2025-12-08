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
        Schema::table('transactions', function (Blueprint $table) {
            // Add shipping origin and destination cities
            $table->string('origin_city_id')->nullable()->after('shipping_service');
            $table->string('origin_city_name')->nullable()->after('origin_city_id');
            $table->string('destination_city_id')->nullable()->after('origin_city_name');
            $table->string('destination_city_name')->nullable()->after('destination_city_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['origin_city_id', 'origin_city_name', 'destination_city_id', 'destination_city_name']);
        });
    }
};
