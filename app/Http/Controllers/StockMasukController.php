<?php

namespace App\Http\Controllers;

use App\Models\StockMasuk;
use Illuminate\Http\Request;

class StockMasukController extends Controller
{
    public function index(Request $request)
    {
        $query = StockMasuk::orderBy('tanggal', 'desc');

        // Filter berdasarkan nama barang
        if ($request->filled('search')) {
            $query->byBarang($request->search);
        }

        // Filter berdasarkan tanggal
        if ($request->filled('date')) {
            $query->byDate($request->date);
        }

        $stockMasuk = $query->paginate(10);

        return view('stock-masuk.index', compact('stockMasuk'));
    }

    public function create()
    {
        return view('stock-masuk.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_barang' => 'required|string|max:255',
            'qty' => 'required|integer|min:1',
            'tanggal' => 'required|date',
        ]);

        StockMasuk::create($request->all());

        return redirect()->route('stock-masuk.index')
            ->with('success', 'Stock masuk berhasil ditambahkan');
    }

    public function show(StockMasuk $stockMasuk)
    {
        return view('stock-masuk.show', compact('stockMasuk'));
    }

    public function edit(StockMasuk $stockMasuk)
    {
        return view('stock-masuk.edit', compact('stockMasuk'));
    }

    public function update(Request $request, StockMasuk $stockMasuk)
    {
        $request->validate([
            'nama_barang' => 'required|string|max:255',
            'qty' => 'required|integer|min:1',
            'tanggal' => 'required|date',
        ]);

        $stockMasuk->update($request->all());

        return redirect()->route('stock-masuk.index')
            ->with('success', 'Stock masuk berhasil diperbarui');
    }

    public function destroy(StockMasuk $stockMasuk)
    {
        try {
            $stockMasuk->delete();
            
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Stock masuk berhasil dihapus'
                ]);
            }
            
            return redirect()->route('stock-masuk.index')
                ->with('success', 'Stock masuk berhasil dihapus');
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
