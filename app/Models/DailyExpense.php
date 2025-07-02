<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DailyExpense extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_pengeluaran',
        'tanggal',
        'deskripsi',
        'total'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'total' => 'decimal:2'
    ];

    public function items()
    {
        return $this->hasMany(ExpenseItem::class);
    }

    // Scope untuk filter berdasarkan tanggal
    public function scopeByDate($query, $date)
    {
        return $query->whereDate('tanggal', $date);
    }

    public function scopeByMonth($query, $month, $year)
    {
        return $query->whereMonth('tanggal', $month)
                    ->whereYear('tanggal', $year);
    }

    // Accessor untuk format tanggal Indonesia
    public function getFormattedTanggalAttribute()
    {
        return $this->tanggal->format('d/m/Y');
    }

    // Accessor untuk format rupiah
    public function getFormattedTotalAttribute()
    {
        return 'Rp ' . number_format($this->total, 0, ',', '.');
    }
}
