<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_code',
        'user_id',
        'subtotal',
        'total_cost',
        'discount_percentage',
        'discount_amount',
        'tax_percentage',
        'tax_amount',
        'total_amount',
        'gross_profit',
        'net_profit',
        'payment_method'
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_percentage' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'gross_profit' => 'decimal:2',
        'net_profit' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $appends = [
        'profit_margin',
        'formatted_total_amount',
        'formatted_profit',
        'payment_method_label'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(TransactionItem::class);
    }

    // Boot method untuk auto generate transaction code
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($transaction) {
            $transaction->transaction_code = static::generateTransactionCode();
        });
    }

    // Generate unique transaction code
    public static function generateTransactionCode()
    {
        $date = now()->format('Ymd');
        $lastTransaction = static::whereDate('created_at', today())
            ->orderBy('id', 'desc')
            ->first();
        
        $sequence = $lastTransaction ? 
            ((int) substr($lastTransaction->transaction_code, -4)) + 1 : 1;
        
        return 'TXN-' . $date . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    // Scopes
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year);
    }

    public function scopeThisYear($query)
    {
        return $query->whereYear('created_at', now()->year);
    }

    public function scopeByPaymentMethod($query, $method)
    {
        return $query->where('payment_method', $method);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    // Accessors
    public function getProfitMarginAttribute()
    {
        if ($this->total_amount == 0) return 0;
        return round(($this->net_profit / $this->total_amount) * 100, 2);
    }

    public function getFormattedTotalAmountAttribute()
    {
        return 'Rp ' . number_format($this->total_amount, 0, ',', '.');
    }

    public function getFormattedProfitAttribute()
    {
        return 'Rp ' . number_format($this->net_profit, 0, ',', '.');
    }

    public function getPaymentMethodLabelAttribute()
    {
        return match($this->payment_method) {
            'cash' => 'Tunai',
            'card' => 'Kartu',
            'digital' => 'Digital',
            default => ucfirst($this->payment_method)
        };
    }

    public function getFormattedCreatedAtAttribute()
    {
        return $this->created_at->format('d/m/Y H:i');
    }

    public function getDateOnlyAttribute()
    {
        return $this->created_at->format('d/m/Y');
    }

    public function getTimeOnlyAttribute()
    {
        return $this->created_at->format('H:i');
    }

    // Helper methods
    public function getTotalItemsAttribute()
    {
        return $this->items->sum('quantity');
    }

    public function getItemsCountAttribute()
    {
        return $this->items->count();
    }

    public function hasDiscount()
    {
        return $this->discount_percentage > 0;
    }

    public function hasTax()
    {
        return $this->tax_percentage > 0;
    }

    // Format untuk receipt/struk
    public function getReceiptDataAttribute()
    {
        return [
            'code' => $this->transaction_code,
            'date' => $this->formatted_created_at,
            'cashier' => $this->user->name,
            'items' => $this->items->map(function($item) {
                return [
                    'name' => $item->product->name,
                    'qty' => $item->quantity,
                    'price' => $item->unit_price,
                    'total' => $item->total_price
                ];
            }),
            'subtotal' => $this->subtotal,
            'discount' => $this->discount_amount,
            'tax' => $this->tax_amount,
            'total' => $this->total_amount,
            'payment_method' => $this->payment_method_label
        ];
    }
}
