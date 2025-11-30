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
        Schema::create('stock_keluar', function (Blueprint $table) {
            $table->id();
            $table->string('judul'); // Judul laporan
            $table->text('deskripsi')->nullable(); // Deskripsi laporan
            $table->json('toppings'); // Array topping [{name: 'Gurita', qty: 10}, ...]
            $table->json('packagings'); // Array packaging [{name: 'Box S', qty: 5}, ...]
            $table->date('tanggal');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_keluars');
    }
};
