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
        Schema::create('prediksi', function (Blueprint $table) {
            $table->id();
            $table->string('nama_produk');
            $table->string('ukuran');
            $table->string('stok_produk');
            $table->string('output');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prediksi');
    }
};
