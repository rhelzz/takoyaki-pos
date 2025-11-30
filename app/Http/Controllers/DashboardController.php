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
    public function index(Request $request)
    {
        $today = Carbon::today();
        $user = Auth::user();
        
        // Jika kasir, otomatis filter ke user_id sendiri
        if ($user->role === 'cashier') {
            $userId = $user->id;
        } else {
            // Admin/Manager bisa pilih kasir atau lihat semua
            $userId = $request->get('user_id');
        }

        // Query dasar transaction
        $transactionQuery = Transaction::whereDate('created_at', $today);
        if ($userId) {
            $transactionQuery->where('user_id', $userId);
        }

        // Statistik hari ini
        $todayTransactions = (clone $transactionQuery)->count();
        $todayRevenue = (clone $transactionQuery)->sum('total_amount');
        $todayGrossProfit = (clone $transactionQuery)->sum('gross_profit');
        $todayNetProfit = (clone $transactionQuery)->sum('net_profit');
        $todayCost = (clone $transactionQuery)->sum('total_cost');

        // Jam tersibuk
        $hourlyTransactions = (clone $transactionQuery)
            ->select(DB::raw('HOUR(created_at) as hour'), DB::raw('COUNT(*) as count'))
            ->groupBy('hour')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get();

        // Produk terlaris hari ini (per kasir)
        $topProducts = DB::table('transaction_items')
            ->join('products', 'transaction_items.product_id', '=', 'products.id')
            ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
            ->whereDate('transactions.created_at', $today);

        if ($userId) {
            $topProducts->where('transactions.user_id', $userId);
        }

        $topProducts = $topProducts
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
                ->when($userId, fn($q) => $q->where('user_id', $userId))
                ->sum('total_amount');
        }

        // Transaksi terbaru (5 terakhir, sesuai filter user)
        $recentTransactions = Transaction::with(['user', 'items.product'])
            ->when($userId, fn($q) => $q->where('user_id', $userId))
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Data kasir untuk dropdown filter
        $cashiers = User::where('role', 'cashier')->active()->get();

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
            'recentTransactions',
            'cashiers',
            'userId'
        ));
    }
}
