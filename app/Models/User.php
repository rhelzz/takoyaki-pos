<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    protected $appends = [
        'role_label',
        'status_label'
    ];

    // Relationships
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    public function scopeManagers($query)
    {
        return $query->where('role', 'manager');
    }

    public function scopeCashiers($query)
    {
        return $query->where('role', 'cashier');
    }

    // Role checker methods
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isManager()
    {
        return $this->role === 'manager';
    }

    public function isCashier()
    {
        return $this->role === 'cashier';
    }

    // Permission checker methods
    public function canManageUsers()
    {
        return in_array($this->role, ['admin']);
    }

    public function canManageProducts()
    {
        return in_array($this->role, ['admin', 'manager']);
    }

    public function canViewReports()
    {
        return in_array($this->role, ['admin', 'manager']);
    }

    public function canProcessTransactions()
    {
        return in_array($this->role, ['admin', 'manager', 'cashier']);
    }

    // Accessors
    public function getRoleLabelAttribute()
    {
        return match($this->role) {
            'admin' => 'Administrator',
            'manager' => 'Manager',
            'cashier' => 'Kasir',
            default => ucfirst($this->role)
        };
    }

    public function getStatusLabelAttribute()
    {
        return $this->is_active ? 'Aktif' : 'Tidak Aktif';
    }

    // Helper methods untuk statistik
    public function getTodayTransactionsCountAttribute()
    {
        return $this->transactions()->today()->count();
    }

    public function getTodayRevenueAttribute()
    {
        return $this->transactions()->today()->sum('total_amount');
    }

    public function getThisMonthTransactionsCountAttribute()
    {
        return $this->transactions()->thisMonth()->count();
    }

    public function getThisMonthRevenueAttribute()
    {
        return $this->transactions()->thisMonth()->sum('total_amount');
    }

    public function getTotalTransactionsAttribute()
    {
        return $this->transactions()->count();
    }

    public function getTotalRevenueAttribute()
    {
        return $this->transactions()->sum('total_amount');
    }

    // Format untuk display
    public function getDisplayNameAttribute()
    {
        return $this->name . ' (' . $this->role_label . ')';
    }
}
