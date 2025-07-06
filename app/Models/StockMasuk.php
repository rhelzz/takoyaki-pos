<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StockMasuk extends Model
{
    use HasFactory;

    protected $table = 'stock_masuk';

    protected $fillable = [
        'judul',
        'deskripsi',
        'items',
        'tanggal'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'items' => 'array'
    ];
}
