<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TaxTemplate extends Model
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

    // Helper untuk menghitung nominal pajak dari amount
    public function calculateTaxAmount($amount)
    {
        return ($amount * $this->percentage) / 100;
    }

    // Validasi range pajak (0% atau 11%)
    public function isValidPercentage()
    {
        return in_array($this->percentage, [0, 11]);
    }

    // Check apakah pajak PPN
    public function isPpn()
    {
        return $this->percentage == 11;
    }

    // Check apakah tanpa pajak
    public function isTaxFree()
    {
        return $this->percentage == 0;
    }
}
