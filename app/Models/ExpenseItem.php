<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExpenseItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'daily_expense_id',
        'nama_bahan',
        'qty',
        'harga_satuan',
        'subtotal'
    ];

    protected $casts = [
        'harga_satuan' => 'decimal:2',
        'subtotal' => 'decimal:2'
    ];

    public function dailyExpense()
    {
        return $this->belongsTo(DailyExpense::class);
    }

    // Accessor untuk format rupiah
    public function getFormattedHargaSatuanAttribute()
    {
        return 'Rp ' . number_format($this->harga_satuan, 0, ',', '.');
    }

    public function getFormattedSubtotalAttribute()
    {
        return 'Rp ' . number_format($this->subtotal, 0, ',', '.');
    }
}
