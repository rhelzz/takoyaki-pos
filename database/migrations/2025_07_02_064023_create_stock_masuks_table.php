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
        Schema::create('stock_masuk', function (Blueprint $table) {
            $table->id();
            $table->string('judul'); // Judul laporan
            $table->text('deskripsi')->nullable(); // Deskripsi laporan
            $table->json('items'); // Isi qty semua item (toping & packaging), format JSON
            $table->date('tanggal');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_masuks');
    }
};
