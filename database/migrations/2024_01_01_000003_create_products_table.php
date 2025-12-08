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
        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('category_id');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->decimal('price', 10, 2);
            $table->enum('condition', ['baru', 'lumayan_baru', 'bekas', 'rusak'])->default('bekas');
            $table->string('brand')->nullable();
            $table->string('size')->nullable();
            $table->string('model')->nullable();
            $table->integer('stock')->default(1);
            $table->json('deal_method')->nullable();
            $table->enum('status', ['active', 'sold', 'deleted'])->default('active');
            $table->integer('view_count')->default(0);
            $table->integer('favorite_count')->default(0);
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->index('user_id');
            $table->index('category_id');
            $table->index('status');
            $table->index('stock');
            $table->index('created_at');
            $table->index('price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
