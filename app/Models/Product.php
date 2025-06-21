<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'description',
        'cost_price',
        'selling_price',
        'image',
        'quantity_per_serving',
        'is_active'
    ];

    protected $casts = [
        'cost_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    protected $appends = [
        'image_url',
        'profit_margin',
        'profit_per_unit'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function transactionItems()
    {
        return $this->hasMany(TransactionItem::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeByQuantityServing($query, $quantity)
    {
        return $query->where('quantity_per_serving', $quantity);
    }

    // Accessor untuk URL gambar
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }
        return asset('images/no-image.png');
    }

    // Hitung margin keuntungan dalam persen
    public function getProfitMarginAttribute()
    {
        if ($this->selling_price == 0) return 0;
        return round((($this->selling_price - $this->cost_price) / $this->selling_price) * 100, 2);
    }

    // Hitung keuntungan per unit
    public function getProfitPerUnitAttribute()
    {
        return $this->selling_price - $this->cost_price;
    }

    // Helper untuk format harga
    public function getFormattedCostPriceAttribute()
    {
        return 'Rp ' . number_format($this->cost_price, 0, ',', '.');
    }

    public function getFormattedSellingPriceAttribute()
    {
        return 'Rp ' . number_format($this->selling_price, 0, ',', '.');
    }

    public function getFormattedProfitAttribute()
    {
        return 'Rp ' . number_format($this->profit_per_unit, 0, ',', '.');
    }

    // Helper untuk statistik penjualan - PERBAIKAN DI SINI
    public function getTotalSoldAttribute()
    {
        return $this->transactionItems->sum('quantity');
    }

    public function getTotalRevenueAttribute()
    {
        return $this->transactionItems->sum('total_price');
    }

    // PERBAIKAN: Ganti closure dengan perhitungan manual
    public function getTotalProfitAttribute()
    {
        $totalProfit = 0;
        foreach ($this->transactionItems as $item) {
            $totalProfit += ($item->total_price - $item->total_cost);
        }
        return $totalProfit;
    }
}
