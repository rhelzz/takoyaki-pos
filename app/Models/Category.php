<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function activeProducts()
    {
        return $this->hasMany(Product::class)->where('is_active', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Helper untuk menghitung total produk
    public function getTotalProductsAttribute()
    {
        return $this->products()->count();
    }

    // Helper untuk menghitung produk aktif
    public function getActiveProductsCountAttribute()
    {
        return $this->products()->where('is_active', true)->count();
    }
}
