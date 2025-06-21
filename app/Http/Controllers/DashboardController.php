<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $user = Auth::user();
        
        // Statistik hari ini
        $todayTransactions = Transaction::whereDate('created_at', $today)->count();
        $todayRevenue = Transaction::whereDate('created_at', $today)->sum('total_amount');
        $todayGrossProfit = Transaction::whereDate('created_at', $today)->sum('gross_profit');
        $todayNetProfit = Transaction::whereDate('created_at', $today)->sum('net_profit');
        $todayCost = Transaction::whereDate('created_at', $today)->sum('total_cost');
        
        // Transaksi per jam hari ini (jam tersibuk)
        $hourlyTransactions = Transaction::whereDate('created_at', $today)
            ->select(DB::raw('HOUR(created_at) as hour'), DB::raw('COUNT(*) as count'))
            ->groupBy('hour')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get();
        
        // Produk terlaris hari ini
        $topProducts = DB::table('transaction_items')
            ->join('products', 'transaction_items.product_id', '=', 'products.id')
            ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
            ->whereDate('transactions.created_at', $today)
            ->select('products.name', DB::raw('SUM(transaction_items.quantity) as total_sold'))
            ->groupBy('products.id', 'products.name')
            ->orderBy('total_sold', 'desc')
            ->limit(5)
            ->get();

        // Statistik tambahan untuk admin/manager
        $totalUsers = null;
        $totalProducts = null;
        $monthlyRevenue = null;
        
        if ($user->canViewReports()) {
            $totalUsers = User::active()->count();
            $totalProducts = Product::active()->count();
            $monthlyRevenue = Transaction::whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->sum('total_amount');
        }

        // Transaksi terbaru (5 terakhir)
        $recentTransactions = Transaction::with(['user', 'items.product'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        return view('dashboard.index', compact(
            'todayTransactions',
            'todayRevenue',
            'todayGrossProfit',
            'todayNetProfit',
            'todayCost',
            'hourlyTransactions',
            'topProducts',
            'totalUsers',
            'totalProducts',
            'monthlyRevenue',
            'recentTransactions'
        ));
    }
}
