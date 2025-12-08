<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('product_id');
            $table->uuid('buyer_id');
            $table->uuid('seller_id');
            $table->decimal('price', 10, 2);
            $table->enum('deal_method', ['meetup', 'shipping'])->default('meetup');
            $table->enum('status', ['menunggu_transaksi', 'barang_dikirim', 'paket_diterima', 'selesai', 'dibatalkan'])->default('menunggu_transaksi');
            $table->string('shipping_courier')->nullable();
            $table->string('shipping_service')->nullable();
            $table->decimal('shipping_cost', 10, 2)->nullable();
            $table->string('tracking_number')->nullable();
            $table->text('meetup_location')->nullable();
            $table->timestamp('seller_confirmed_at')->nullable();
            $table->timestamp('shipping_confirmed_at')->nullable();
            $table->timestamp('received_confirmed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('buyer_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('seller_id')->references('id')->on('users')->onDelete('cascade');
            $table->index('buyer_id');
            $table->index('seller_id');
            $table->index('product_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
