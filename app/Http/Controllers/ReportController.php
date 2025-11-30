<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\TransactionItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function index()
    {
        // Today's stats
        $todayStats = [
            'transactions' => Transaction::whereDate('created_at', today())->count(),
            'revenue' => Transaction::whereDate('created_at', today())->sum('total_amount') ?? 0,
            'profit' => Transaction::whereDate('created_at', today())->sum('net_profit') ?? 0,
        ];

        // Month's stats
        $monthStats = [
            'transactions' => Transaction::whereMonth('created_at', now()->month)
                                       ->whereYear('created_at', now()->year)
                                       ->count(),
            'revenue' => Transaction::whereMonth('created_at', now()->month)
                                  ->whereYear('created_at', now()->year)
                                  ->sum('total_amount') ?? 0,
            'profit' => Transaction::whereMonth('created_at', now()->month)
                                 ->whereYear('created_at', now()->year)
                                 ->sum('net_profit') ?? 0,
        ];

        // Recent transactions
        $recentTransactions = Transaction::with('user')
                                       ->whereDate('created_at', today())
                                       ->orderBy('created_at', 'desc')
                                       ->take(5)
                                       ->get();

        return view('reports.index', compact(
            'todayStats',
            'monthStats',
            'recentTransactions'
        ));
    }

    public function dailyReport(Request $request)
    {
        $date = $request->date ? Carbon::parse($request->date) : Carbon::today();
        $currentUser = Auth::user();
        
        // Jika kasir, otomatis filter ke user_id sendiri
        if ($currentUser->role === 'cashier') {
            $userId = $currentUser->id;
        } else {
            // Admin/Manager bisa pilih kasir atau lihat semua
            $userId = $request->user_id;
        }
        
        // Base query
        $transactionQuery = Transaction::whereDate('created_at', $date);
        
        // Apply user filter if selected
        if ($userId) {
            $transactionQuery->where('user_id', $userId);
        }
        
        $transactions = $transactionQuery
            ->with(['items.product', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();

        $summary = [
            'total_transactions' => $transactions->count(),
            'total_revenue' => $transactions->sum('total_amount'),
            'total_cost' => $transactions->sum('total_cost'),
            'gross_profit' => $transactions->sum('gross_profit'),
            'net_profit' => $transactions->sum('net_profit'),
            'total_tax' => $transactions->sum('tax_amount'),
            'total_discount' => $transactions->sum('discount_amount')
        ];

        // Breakdown per jam dengan filter user
        $hourlyQuery = Transaction::whereDate('created_at', $date);
        if ($userId) {
            $hourlyQuery->where('user_id', $userId);
        }
        
        $hourlyData = $hourlyQuery
            ->select(
                DB::raw('HOUR(created_at) as hour'),
                DB::raw('COUNT(*) as transaction_count'),
                DB::raw('SUM(total_amount) as revenue'),
                DB::raw('SUM(net_profit) as profit')
            )
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        // Payment method breakdown dengan filter user
        $paymentQuery = Transaction::whereDate('created_at', $date);
        if ($userId) {
            $paymentQuery->where('user_id', $userId);
        }
        
        $paymentMethods = $paymentQuery
            ->select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(total_amount) as total'))
            ->groupBy('payment_method')
            ->get();

        // Get all users yang pernah melakukan transaksi untuk dropdown filter
        $users = User::whereHas('transactions', function($query) use ($date) {
            $query->whereDate('created_at', $date);
        })->orderBy('name')->get();

        // Get selected user info
        $selectedUser = $userId ? User::find($userId) : null;

        return view('reports.daily', compact(
            'date', 
            'transactions', 
            'summary', 
            'hourlyData', 
            'paymentMethods',
            'users',
            'selectedUser',
            'userId'
        ));
    }

    public function busiestHours(Request $request)
    {
        // Set timezone Indonesia untuk semua operasi
        $timezone = 'Asia/Jakarta';
        
        // Perbaiki range tanggal
        $startDate = $request->start_date 
            ? Carbon::parse($request->start_date, $timezone)->startOfDay()
            : Carbon::now($timezone)->subDays(6)->startOfDay(); // 7 hari terakhir = hari ini + 6 hari sebelumnya

        $endDate = $request->end_date
            ? Carbon::parse($request->end_date, $timezone)->endOfDay()
            : Carbon::now($timezone)->endOfDay(); // Include sampai akhir hari ini

        // Debug logging (opsional)
        Log::info('Busiest Hours Filter:', [
            'start_date' => $startDate->format('Y-m-d H:i:s T'),
            'end_date' => $endDate->format('Y-m-d H:i:s T'),
            'timezone' => $timezone,
            'now_jakarta' => Carbon::now($timezone)->format('Y-m-d H:i:s T')
        ]);

        // Query dengan timezone conversion
        $busiestHours = Transaction::whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('HOUR(CONVERT_TZ(created_at, "+00:00", "+07:00")) as hour'),
                DB::raw('COUNT(*) as transaction_count'),
                DB::raw('SUM(total_amount) as revenue'),
                DB::raw('SUM(net_profit) as profit'),
                DB::raw('AVG(total_amount) as avg_transaction')
            )
            ->groupBy('hour')
            ->orderBy('transaction_count', 'desc')
            ->get();

        // Debug transaksi yang ditemukan
        $totalTransactions = Transaction::whereBetween('created_at', [$startDate, $endDate])->count();
        Log::info('Transactions found:', [
            'total_count' => $totalTransactions,
            'busiest_hours_count' => $busiestHours->count()
        ]);

        // Breakdown per hari dalam minggu
        $dailyPattern = Transaction::whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DAYNAME(CONVERT_TZ(created_at, "+00:00", "+07:00")) as day_name'),
                DB::raw('DAYOFWEEK(CONVERT_TZ(created_at, "+00:00", "+07:00")) as day_number'),
                DB::raw('COUNT(*) as transaction_count'),
                DB::raw('SUM(total_amount) as revenue')
            )
            ->groupBy('day_name', 'day_number')
            ->orderBy('day_number')
            ->get();

        return view('reports.busiest-hours', compact('busiestHours', 'dailyPattern', 'startDate', 'endDate'));
    }

    public function bestSellingProducts(Request $request)
    {
        $period = $request->period ?? 'month';
        
        $startDate = match($period) {
            '3months' => Carbon::now()->subMonths(3),
            '6months' => Carbon::now()->subMonths(6),
            'year' => Carbon::now()->subYear(),
            default => Carbon::now()->subMonth()
        };

        $bestSelling = TransactionItem::select(
                'product_id',
                DB::raw('SUM(quantity) as total_sold'),
                DB::raw('SUM(total_price) as total_revenue'),
                DB::raw('SUM(total_cost) as total_cost'),
                DB::raw('SUM(total_price - total_cost) as total_profit'),
                DB::raw('COUNT(DISTINCT transaction_id) as transaction_count')
            )
            ->with('product.category')
            ->whereHas('transaction', function($query) use ($startDate) {
                $query->where('created_at', '>=', $startDate);
            })
            ->groupBy('product_id')
            ->orderBy('total_sold', 'desc')
            ->limit(20)
            ->get();

        // Top categories
        $topCategories = DB::table('transaction_items')
            ->join('products', 'transaction_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
            ->where('transactions.created_at', '>=', $startDate)
            ->select(
                'categories.name',
                DB::raw('SUM(transaction_items.quantity) as total_sold'),
                DB::raw('SUM(transaction_items.total_price) as total_revenue')
            )
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('total_sold', 'desc')
            ->get();

        return view('reports.best-selling', compact('bestSelling', 'topCategories', 'period', 'startDate'));
    }

    public function financialReport(Request $request)
    {
        // Set timezone Indonesia untuk semua operasi
        $timezone = 'Asia/Jakarta';
        
        // Perbaiki range tanggal dengan timezone Indonesia
        $startDate = $request->start_date 
            ? Carbon::parse($request->start_date, $timezone)->startOfDay()
            : Carbon::now($timezone)->startOfMonth()->startOfDay();

        $endDate = $request->end_date
            ? Carbon::parse($request->end_date, $timezone)->endOfDay()
            : Carbon::now($timezone)->endOfMonth()->endOfDay();

        // Debug logging (opsional)
        Log::info('Financial Report Filter:', [
            'start_date' => $startDate->format('Y-m-d H:i:s T'),
            'end_date' => $endDate->format('Y-m-d H:i:s T'),
            'timezone' => $timezone,
            'now_jakarta' => Carbon::now($timezone)->format('Y-m-d H:i:s T')
        ]);

        // Query dengan timezone yang tepat
        $transactions = Transaction::whereBetween('created_at', [$startDate, $endDate])->get();

        $summary = [
            'total_revenue' => $transactions->sum('total_amount'),
            'total_cost' => $transactions->sum('total_cost'),
            'gross_profit' => $transactions->sum('gross_profit'),
            'net_profit' => $transactions->sum('net_profit'),
            'total_tax' => $transactions->sum('tax_amount'),
            'total_discount' => $transactions->sum('discount_amount'),
            'transaction_count' => $transactions->count(),
            'avg_transaction' => $transactions->count() > 0 ? $transactions->sum('total_amount') / $transactions->count() : 0,
            'profit_margin' => $transactions->sum('total_amount') > 0 ? ($transactions->sum('net_profit') / $transactions->sum('total_amount')) * 100 : 0
        ];

        // Breakdown harian dengan timezone conversion
        $dailyData = Transaction::whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(CONVERT_TZ(created_at, "+00:00", "+07:00")) as date'),
                DB::raw('COUNT(*) as transaction_count'),
                DB::raw('SUM(total_amount) as revenue'),
                DB::raw('SUM(total_cost) as cost'),
                DB::raw('SUM(gross_profit) as gross_profit'),
                DB::raw('SUM(net_profit) as net_profit')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Payment method analysis dengan timezone
        $paymentAnalysis = Transaction::whereBetween('created_at', [$startDate, $endDate])
            ->select(
                'payment_method',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(total_amount) as total'),
                DB::raw('AVG(total_amount) as average')
            )
            ->groupBy('payment_method')
            ->get();

        // Tax and discount analysis dengan timezone
        $taxDiscountAnalysis = [
            'transactions_with_discount' => Transaction::whereBetween('created_at', [$startDate, $endDate])
                ->where('discount_percentage', '>', 0)->count(),
            'transactions_with_tax' => Transaction::whereBetween('created_at', [$startDate, $endDate])
                ->where('tax_percentage', '>', 0)->count(),
            'avg_discount_percentage' => Transaction::whereBetween('created_at', [$startDate, $endDate])
                ->where('discount_percentage', '>', 0)->avg('discount_percentage'),
        ];

        // Debug informasi
        Log::info('Financial Report Data:', [
            'transactions_found' => $transactions->count(),
            'date_range_days' => $startDate->diffInDays($endDate) + 1,
            'total_revenue' => $summary['total_revenue']
        ]);

        return view('reports.financial', compact(
            'startDate', 
            'endDate', 
            'summary', 
            'dailyData', 
            'paymentAnalysis',
            'taxDiscountAnalysis'
        ));
    }

    public function exportDaily(Request $request)
    {
        $date = $request->date ? Carbon::parse($request->date) : Carbon::today();
        
        $transactions = Transaction::whereDate('created_at', $date)
            ->with(['items.product', 'user'])
            ->get();

        // Implementasi export ke Excel/PDF bisa ditambahkan di sini
        // Untuk sekarang return JSON
        return response()->json([
            'success' => true,
            'data' => $transactions,
            'message' => 'Data ready for export'
        ]);
    }
}
