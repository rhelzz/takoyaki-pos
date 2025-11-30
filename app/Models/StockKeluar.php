<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StockKeluar extends Model
{
    use HasFactory;

    protected $table = 'stock_keluar';

    protected $fillable = [
        'judul',
        'deskripsi',
        'toppings',
        'packagings',
        'tanggal'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'toppings' => 'array',
        'packagings' => 'array'
    ];
}
