<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\TransactionItem;
use Illuminate\Support\Facades\DB;

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
        
        $transactions = Transaction::whereDate('created_at', $date)
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

        // Breakdown per jam
        $hourlyData = Transaction::whereDate('created_at', $date)
            ->select(
                DB::raw('HOUR(created_at) as hour'),
                DB::raw('COUNT(*) as transaction_count'),
                DB::raw('SUM(total_amount) as revenue'),
                DB::raw('SUM(net_profit) as profit')
            )
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        // Payment method breakdown
        $paymentMethods = Transaction::whereDate('created_at', $date)
            ->select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(total_amount) as total'))
            ->groupBy('payment_method')
            ->get();

        return view('reports.daily', compact('date', 'transactions', 'summary', 'hourlyData', 'paymentMethods'));
    }

    public function busiestHours(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->subDays(7);
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now();

        $busiestHours = Transaction::whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('HOUR(created_at) as hour'),
                DB::raw('COUNT(*) as transaction_count'),
                DB::raw('SUM(total_amount) as revenue'),
                DB::raw('SUM(net_profit) as profit'),
                DB::raw('AVG(total_amount) as avg_transaction')
            )
            ->groupBy('hour')
            ->orderBy('transaction_count', 'desc')
            ->get();

        // Breakdown per hari dalam minggu
        $dailyPattern = Transaction::whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DAYNAME(created_at) as day_name'),
                DB::raw('DAYOFWEEK(created_at) as day_number'),
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
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now()->endOfMonth();

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

        // Breakdown harian
        $dailyData = Transaction::whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as transaction_count'),
                DB::raw('SUM(total_amount) as revenue'),
                DB::raw('SUM(total_cost) as cost'),
                DB::raw('SUM(gross_profit) as gross_profit'),
                DB::raw('SUM(net_profit) as net_profit')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Payment method analysis
        $paymentAnalysis = Transaction::whereBetween('created_at', [$startDate, $endDate])
            ->select(
                'payment_method',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(total_amount) as total'),
                DB::raw('AVG(total_amount) as average')
            )
            ->groupBy('payment_method')
            ->get();

        // Tax and discount analysis
        $taxDiscountAnalysis = [
            'transactions_with_discount' => Transaction::whereBetween('created_at', [$startDate, $endDate])
                ->where('discount_percentage', '>', 0)->count(),
            'transactions_with_tax' => Transaction::whereBetween('created_at', [$startDate, $endDate])
                ->where('tax_percentage', '>', 0)->count(),
            'avg_discount_percentage' => Transaction::whereBetween('created_at', [$startDate, $endDate])
                ->where('discount_percentage', '>', 0)->avg('discount_percentage'),
        ];

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
