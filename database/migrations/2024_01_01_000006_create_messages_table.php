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
        Schema::create('messages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('conversation_id');
            $table->uuid('sender_id');
            $table->text('message')->nullable();
            $table->enum('message_type', ['text', 'offer', 'image'])->default('text');
            $table->decimal('offer_amount', 10, 2)->nullable();
            $table->enum('offer_status', ['pending', 'accepted', 'rejected', 'counter_offer'])->nullable();
            $table->integer('offer_counter_count')->default(0);
            $table->boolean('is_read')->default(false);
            $table->timestamps();
            
            $table->foreign('conversation_id')->references('id')->on('conversations')->onDelete('cascade');
            $table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade');
            $table->index('conversation_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
