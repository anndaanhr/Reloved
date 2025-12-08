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
        Schema::table('products', function (Blueprint $table) {
            // Add new fields for dynamic attributes
            // We'll reuse existing fields (brand, size, model) for different purposes based on category
            // But add specific fields for better data integrity
            $table->date('expired_date')->nullable()->after('model');
            $table->integer('weight')->nullable()->after('expired_date'); // in grams
            $table->string('author')->nullable()->after('weight');
            $table->string('publisher')->nullable()->after('author');
            $table->integer('year')->nullable()->after('publisher');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['expired_date', 'weight', 'author', 'publisher', 'year']);
        });
    }
};
