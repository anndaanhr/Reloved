<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // PostgreSQL doesn't support direct enum modification, so we need to:
        // 1. Add new column with new enum
        // 2. Migrate data
        // 3. Drop old column
        // 4. Rename new column
        
        // Map old values to new values
        DB::statement("
            ALTER TABLE products 
            ALTER COLUMN condition TYPE VARCHAR(50)
        ");
        
        // Update existing data
        DB::statement("
            UPDATE products 
            SET condition = CASE 
                WHEN condition = 'baru' THEN 'baru'
                WHEN condition = 'lumayan_baru' THEN 'bekas_bagus'
                WHEN condition = 'bekas' THEN 'bekas'
                WHEN condition = 'rusak' THEN 'bekas_rusak'
                ELSE 'bekas'
            END
        ");
        
        // Change back to enum with new values
        DB::statement("
            ALTER TABLE products 
            ALTER COLUMN condition TYPE VARCHAR(50)
        ");
        
        // Add check constraint to enforce enum-like behavior (only if not exists)
        $constraintExists = DB::select("
            SELECT constraint_name 
            FROM information_schema.table_constraints 
            WHERE table_name = 'products' 
            AND constraint_name = 'products_condition_check'
        ");
        
        if (empty($constraintExists)) {
            DB::statement("
                ALTER TABLE products 
                ADD CONSTRAINT products_condition_check 
                CHECK (condition IN ('baru', 'bekas_bagus', 'bekas', 'bekas_rusak'))
            ");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove constraint
        DB::statement("
            ALTER TABLE products 
            DROP CONSTRAINT IF EXISTS products_condition_check
        ");
        
        // Revert to old values
        DB::statement("
            UPDATE products 
            SET condition = CASE 
                WHEN condition = 'baru' THEN 'baru'
                WHEN condition = 'bekas_bagus' THEN 'lumayan_baru'
                WHEN condition = 'bekas' THEN 'bekas'
                WHEN condition = 'bekas_rusak' THEN 'rusak'
                ELSE 'bekas'
            END
        ");
        
        // Change back to old enum
        DB::statement("
            ALTER TABLE products 
            ALTER COLUMN condition TYPE VARCHAR(50)
        ");
        
        DB::statement("
            ALTER TABLE products 
            ADD CONSTRAINT products_condition_check 
            CHECK (condition IN ('baru', 'lumayan_baru', 'bekas', 'rusak'))
        ");
    }
};
