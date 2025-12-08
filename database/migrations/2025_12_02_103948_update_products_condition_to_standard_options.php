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
        // Update condition enum to standard marketplace options
        // Mapping: baru -> baru, lumayan_baru -> seperti_baru, bekas -> bekas_bagus, rusak -> bekas_cukup
        
        if (DB::getDriverName() === 'pgsql') {
            // PostgreSQL: Update existing data first
            DB::statement("
                UPDATE products 
                SET condition = CASE 
                    WHEN condition = 'baru' THEN 'baru'
                    WHEN condition = 'lumayan_baru' THEN 'seperti_baru'
                    WHEN condition = 'bekas' THEN 'bekas_bagus'
                    WHEN condition = 'rusak' THEN 'bekas_cukup'
                    ELSE 'bekas_bagus'
                END
            ");
            
            // Drop old constraint if exists
            DB::statement("ALTER TABLE products DROP CONSTRAINT IF EXISTS products_condition_check");
            
            // Change column type to VARCHAR
            DB::statement("ALTER TABLE products ALTER COLUMN condition TYPE VARCHAR(50)");
            
            // Add new constraint
            DB::statement("
                ALTER TABLE products 
                ADD CONSTRAINT products_condition_check 
                CHECK (condition IN ('baru', 'seperti_baru', 'bekas_bagus', 'bekas_cukup'))
            ");
        } else {
            // MySQL/MariaDB
            DB::statement("
                ALTER TABLE products 
                MODIFY COLUMN condition ENUM('baru', 'seperti_baru', 'bekas_bagus', 'bekas_cukup') 
                DEFAULT 'bekas_bagus'
            ");
            
            DB::statement("
                UPDATE products 
                SET condition = CASE 
                    WHEN condition = 'baru' THEN 'baru'
                    WHEN condition = 'lumayan_baru' THEN 'seperti_baru'
                    WHEN condition = 'bekas' THEN 'bekas_bagus'
                    WHEN condition = 'rusak' THEN 'bekas_cukup'
                    ELSE 'bekas_bagus'
                END
            ");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to old values
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("
                UPDATE products 
                SET condition = CASE 
                    WHEN condition = 'baru' THEN 'baru'
                    WHEN condition = 'seperti_baru' THEN 'lumayan_baru'
                    WHEN condition = 'bekas_bagus' THEN 'bekas'
                    WHEN condition = 'bekas_cukup' THEN 'rusak'
                    ELSE 'bekas'
                END
            ");
            
            DB::statement("ALTER TABLE products DROP CONSTRAINT IF EXISTS products_condition_check");
            DB::statement("ALTER TABLE products ALTER COLUMN condition TYPE VARCHAR(50)");
            DB::statement("
                ALTER TABLE products 
                ADD CONSTRAINT products_condition_check 
                CHECK (condition IN ('baru', 'lumayan_baru', 'bekas', 'rusak'))
            ");
        } else {
            DB::statement("
                ALTER TABLE products 
                MODIFY COLUMN condition ENUM('baru', 'lumayan_baru', 'bekas', 'rusak') 
                DEFAULT 'bekas'
            ");
            
            DB::statement("
                UPDATE products 
                SET condition = CASE 
                    WHEN condition = 'baru' THEN 'baru'
                    WHEN condition = 'seperti_baru' THEN 'lumayan_baru'
                    WHEN condition = 'bekas_bagus' THEN 'bekas'
                    WHEN condition = 'bekas_cukup' THEN 'rusak'
                    ELSE 'bekas'
                END
            ");
        }
    }
};
