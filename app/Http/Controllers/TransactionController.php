<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with(['user', 'items.product'])
            ->orderBy('created_at', 'desc');

        // Filter by date
        if ($request->date) {
            $query->whereDate('created_at', $request->date);
        }

        // Filter by user
        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by payment method
        if ($request->payment_method) {
            $query->where('payment_method', $request->payment_method);
        }

        // Search by transaction code
        if ($request->search) {
            $query->where('transaction_code', 'like', '%' . $request->search . '%');
        }

        $transactions = $query->paginate(20);

        // Summary stats
        $totalTransactions = $query->count();
        $totalRevenue = $query->sum('total_amount');
        $totalProfit = $query->sum('net_profit');

        $users = \App\Models\User::active()->orderBy('name')->get();

        return view('transactions.index', compact(
            'transactions', 
            'totalTransactions', 
            'totalRevenue', 
            'totalProfit', 
            'users'
        ));
    }

    public function show(Transaction $transaction)
    {
        $transaction->load(['user', 'items.product.category']);
        
        return view('transactions.show', compact('transaction'));
    }

    public function receipt(Transaction $transaction)
    {
        $transaction->load(['user', 'items.product']);
        
        return view('transactions.receipt', compact('transaction'));
    }

    public function destroy(Transaction $transaction)
    {
        // Only allow deletion of today's transactions and by admin
        if (!Auth::user()->isAdmin() || !$transaction->created_at->isToday()) {
            abort(403, 'Tidak dapat menghapus transaksi ini');
        }

        $transaction->delete();

        return redirect()->route('transactions.index')
            ->with('success', 'Transaksi berhasil dihapus');
    }
}
