<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TransactionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'product_id',
        'quantity',
        'unit_cost',
        'unit_price',
        'total_cost',
        'total_price'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_cost' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'total_price' => 'decimal:2'
    ];

    protected $appends = [
        'profit',
        'profit_margin',
        'formatted_total_price',
        'formatted_profit'
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Scopes
    public function scopeByProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    public function scopeByTransaction($query, $transactionId)
    {
        return $query->where('transaction_id', $transactionId);
    }

    public function scopeToday($query)
    {
        return $query->whereHas('transaction', function($q) {
            $q->whereDate('created_at', today());
        });
    }

    public function scopeThisMonth($query)
    {
        return $query->whereHas('transaction', function($q) {
            $q->whereMonth('created_at', now()->month)
              ->whereYear('created_at', now()->year);
        });
    }

    // Accessors
    public function getProfitAttribute()
    {
        return $this->total_price - $this->total_cost;
    }

    public function getProfitMarginAttribute()
    {
        if ($this->total_price == 0) return 0;
        return round(($this->profit / $this->total_price) * 100, 2);
    }

    public function getFormattedTotalPriceAttribute()
    {
        return 'Rp ' . number_format($this->total_price, 0, ',', '.');
    }

    public function getFormattedProfitAttribute()
    {
        return 'Rp ' . number_format($this->profit, 0, ',', '.');
    }

    public function getFormattedUnitPriceAttribute()
    {
        return 'Rp ' . number_format($this->unit_price, 0, ',', '.');
    }

    public function getFormattedUnitCostAttribute()
    {
        return 'Rp ' . number_format($this->unit_cost, 0, ',', '.');
    }

    // Helper methods
    public function getProductNameAttribute()
    {
        return $this->product ? $this->product->name : 'Produk Tidak Ditemukan';
    }

    public function getCategoryNameAttribute()
    {
        return $this->product && $this->product->category ? 
            $this->product->category->name : 'Kategori Tidak Ditemukan';
    }

    // Validasi quantity
    public function isValidQuantity()
    {
        return $this->quantity > 0;
    }

    // Check apakah menguntungkan
    public function isProfitable()
    {
        return $this->profit > 0;
    }

    // Format untuk display di cart atau receipt
    public function getDisplayDataAttribute()
    {
        return [
            'product_name' => $this->product_name,
            'quantity' => $this->quantity,
            'unit_price' => $this->formatted_unit_price,
            'total_price' => $this->formatted_total_price,
            'profit' => $this->formatted_profit,
            'profit_margin' => $this->profit_margin . '%'
        ];
    }
}
