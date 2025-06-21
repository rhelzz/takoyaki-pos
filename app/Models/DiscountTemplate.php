<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DiscountTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'percentage',
        'is_active'
    ];

    protected $casts = [
        'percentage' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrderByPercentage($query, $direction = 'asc')
    {
        return $query->orderBy('percentage', $direction);
    }

    // Helper untuk format tampilan
    public function getFormattedPercentageAttribute()
    {
        return $this->percentage . '%';
    }

    public function getDisplayNameAttribute()
    {
        return $this->name . ' (' . $this->formatted_percentage . ')';
    }

    // Helper untuk menghitung nominal diskon dari subtotal
    public function calculateDiscountAmount($subtotal)
    {
        return ($subtotal * $this->percentage) / 100;
    }

    // Validasi range diskon (5-25%)
    public function isValidPercentage()
    {
        return $this->percentage >= 5 && $this->percentage <= 25;
    }
}
