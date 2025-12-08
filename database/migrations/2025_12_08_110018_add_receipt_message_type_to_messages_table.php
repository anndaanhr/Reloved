<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // PostgreSQL doesn't support ALTER ENUM directly, so we need to recreate the column
        DB::statement("ALTER TABLE messages DROP CONSTRAINT IF EXISTS messages_message_type_check");
        DB::statement("ALTER TABLE messages ALTER COLUMN message_type TYPE VARCHAR(20)");
        DB::statement("ALTER TABLE messages ADD CONSTRAINT messages_message_type_check CHECK (message_type IN ('text', 'offer', 'image', 'receipt'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE messages DROP CONSTRAINT IF EXISTS messages_message_type_check");
        DB::statement("ALTER TABLE messages ALTER COLUMN message_type TYPE VARCHAR(20)");
        DB::statement("ALTER TABLE messages ADD CONSTRAINT messages_message_type_check CHECK (message_type IN ('text', 'offer', 'image'))");
    }
};
