<?php

namespace App\Http\Controllers;

use App\Models\StockKeluar;
use Illuminate\Http\Request;

class StockKeluarController extends Controller
{
    public function index(Request $request)
    {
        $query = StockKeluar::orderBy('tanggal', 'desc');

        // Filter berdasarkan nama barang
        if ($request->filled('search')) {
            $query->byBarang($request->search);
        }

        // Filter berdasarkan tanggal
        if ($request->filled('date')) {
            $query->byDate($request->date);
        }

        $stockKeluar = $query->paginate(10);

        return view('stock-keluar.index', compact('stockKeluar'));
    }

    public function create()
    {
        return view('stock-keluar.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_barang' => 'required|string|max:255',
            'qty' => 'required|integer|min:1',
            'tanggal' => 'required|date',
        ]);

        StockKeluar::create($request->all());

        return redirect()->route('stock-keluar.index')
            ->with('success', 'Stock keluar berhasil ditambahkan');
    }

    public function show(StockKeluar $stockKeluar)
    {
        return view('stock-keluar.show', compact('stockKeluar'));
    }

    public function edit(StockKeluar $stockKeluar)
    {
        return view('stock-keluar.edit', compact('stockKeluar'));
    }

    public function update(Request $request, StockKeluar $stockKeluar)
    {
        $request->validate([
            'nama_barang' => 'required|string|max:255',
            'qty' => 'required|integer|min:1',
            'tanggal' => 'required|date',
        ]);

        $stockKeluar->update($request->all());

        return redirect()->route('stock-keluar.index')
            ->with('success', 'Stock keluar berhasil diperbarui');
    }

    public function destroy(StockKeluar $stockKeluar)
    {
        try {
            $stockKeluar->delete();
            
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Stock keluar berhasil dihapus'
                ]);
            }
            
            return redirect()->route('stock-keluar.index')
                ->with('success', 'Stock keluar berhasil dihapus');
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus stock: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->with('error', 'Gagal menghapus stock: ' . $e->getMessage());
        }
    }
}
