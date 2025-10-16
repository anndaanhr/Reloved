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
        Schema::create('user', function (Blueprint $table) {
            $table->uuid('id')->primary();                 // kolom id (primary key, UUID)
            $table->string('nama', 150);                   // kolom nama
            $table->string('npm', 20)->unique();           // kolom npm, dibuat unique
            $table->uuid('kelas_id');                      // kolom kelas_id (UUID)
            $table->timestamps();                          // created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user');
}
};
