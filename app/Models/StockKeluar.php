<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StockKeluar extends Model
{
    use HasFactory;

    protected $table = 'stock_keluar';

    protected $fillable = [
        'nama_barang',
        'qty',
        'tanggal'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'qty' => 'integer'
    ];

    // Scope untuk filter berdasarkan nama barang
    public function scopeByBarang($query, $namaBarang)
    {
        return $query->where('nama_barang', 'like', '%' . $namaBarang . '%');
    }

    public function scopeByDate($query, $date)
    {
        return $query->whereDate('tanggal', $date);
    }

    // Accessor untuk format tanggal Indonesia
    public function getFormattedTanggalAttribute()
    {
        return $this->tanggal->format('d/m/Y');
    }
}
