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
        Schema::create('mata_kuliah', function (Blueprint $table) {
            $table->uuid('id')->primary();        // kolom id (primary key, UUID)
            $table->string('nama_mk', 150);       // kolom nama mata kuliah
            $table->integer('sks');               // kolom jumlah SKS
            $table->timestamps();                 // kolom created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mata_kuliah');
}
};
