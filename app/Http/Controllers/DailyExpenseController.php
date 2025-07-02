<?php

namespace App\Http\Controllers;

use App\Models\ExpenseItem;
use App\Models\DailyExpense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DailyExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = DailyExpense::with('items')->orderBy('tanggal', 'desc');

        // Filter berdasarkan tanggal
        if ($request->filled('date')) {
            $query->whereDate('tanggal', $request->date);
        }

        // Search berdasarkan nama pengeluaran
        if ($request->filled('search')) {
            $query->where('nama_pengeluaran', 'like', '%' . $request->search . '%');
        }

        $expenses = $query->paginate(10);

        return view('daily-expenses.index', compact('expenses'));
    }

    public function create()
    {
        return view('daily-expenses.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_pengeluaran' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'deskripsi' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.nama_bahan' => 'required|string|max:255',
            'items.*.qty' => 'required|string|max:100',
            'items.*.harga_satuan' => 'required|numeric|min:0',
            'items.*.subtotal' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Hitung total dari semua items
            $total = collect($request->items)->sum('subtotal');

            // Buat daily expense
            $expense = DailyExpense::create([
                'nama_pengeluaran' => $request->nama_pengeluaran,
                'tanggal' => $request->tanggal,
                'deskripsi' => $request->deskripsi,
                'total' => $total
            ]);

            // Buat expense items
            foreach ($request->items as $item) {
                ExpenseItem::create([
                    'daily_expense_id' => $expense->id,
                    'nama_bahan' => $item['nama_bahan'],
                    'qty' => $item['qty'],
                    'harga_satuan' => $item['harga_satuan'],
                    'subtotal' => $item['subtotal']
                ]);
            }

            DB::commit();

            return redirect()->route('daily-expenses.index')
                ->with('success', 'Pengeluaran harian berhasil ditambahkan');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Gagal menambahkan pengeluaran: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(DailyExpense $dailyExpense)
    {
        $dailyExpense->load('items');
        return view('daily-expenses.show', compact('dailyExpense'));
    }

    public function edit(DailyExpense $dailyExpense)
    {
        $dailyExpense->load('items');
        return view('daily-expenses.edit', compact('dailyExpense'));
    }

    public function update(Request $request, DailyExpense $dailyExpense)
    {
        $request->validate([
            'nama_pengeluaran' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'deskripsi' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.nama_bahan' => 'required|string|max:255',
            'items.*.qty' => 'required|string|max:100',
            'items.*.harga_satuan' => 'required|numeric|min:0',
            'items.*.subtotal' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Hitung total dari semua items
            $total = collect($request->items)->sum('subtotal');

            // Update daily expense
            $dailyExpense->update([
                'nama_pengeluaran' => $request->nama_pengeluaran,
                'tanggal' => $request->tanggal,
                'deskripsi' => $request->deskripsi,
                'total' => $total
            ]);

            // Hapus expense items lama
            $dailyExpense->items()->delete();

            // Buat expense items baru
            foreach ($request->items as $item) {
                ExpenseItem::create([
                    'daily_expense_id' => $dailyExpense->id,
                    'nama_bahan' => $item['nama_bahan'],
                    'qty' => $item['qty'],
                    'harga_satuan' => $item['harga_satuan'],
                    'subtotal' => $item['subtotal']
                ]);
            }

            DB::commit();

            return redirect()->route('daily-expenses.index')
                ->with('success', 'Pengeluaran harian berhasil diperbarui');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Gagal memperbarui pengeluaran: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(DailyExpense $dailyExpense)
    {
        try {
            $dailyExpense->delete();
            
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Pengeluaran harian berhasil dihapus'
                ]);
            }
            
            return redirect()->route('daily-expenses.index')
                ->with('success', 'Pengeluaran harian berhasil dihapus');
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus pengeluaran: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->with('error', 'Gagal menghapus pengeluaran: ' . $e->getMessage());
        }
    }
}
