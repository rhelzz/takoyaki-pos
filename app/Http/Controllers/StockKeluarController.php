<?php

namespace App\Http\Controllers;

use App\Models\StockKeluar;
use Illuminate\Http\Request;

class StockKeluarController extends Controller
{
    public function index(Request $request)
    {
        $query = StockKeluar::orderBy('tanggal', 'desc');

        // Filter berdasarkan judul
        if ($request->filled('search')) {
            $query->where('judul', 'like', '%' . $request->search . '%');
        }

        // Filter berdasarkan tanggal
        if ($request->filled('date')) {
            $query->whereDate('tanggal', $request->date);
        }

        $stockKeluar = $query->paginate(10)->appends($request->query());

        return view('stock-keluar.index', compact('stockKeluar'));
    }

    public function create()
    {
        return view('stock-keluar.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'tanggal' => 'required|date',
            'toppings' => 'required|json',
            'packagings' => 'required|json'
        ]);

        // Decode JSON
        $toppings = json_decode($validatedData['toppings'], true);
        $packagings = json_decode($validatedData['packagings'], true);

        StockKeluar::create([
            'judul' => $validatedData['judul'],
            'deskripsi' => $validatedData['deskripsi'],
            'tanggal' => $validatedData['tanggal'],
            'toppings' => $toppings,
            'packagings' => $packagings,
        ]);

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
        $validatedData = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'tanggal' => 'required|date',
            'toppings' => 'required|json',
            'packagings' => 'required|json'
        ]);

        // Decode JSON
        $toppings = json_decode($validatedData['toppings'], true);
        $packagings = json_decode($validatedData['packagings'], true);

        $stockKeluar->update([
            'judul' => $validatedData['judul'],
            'deskripsi' => $validatedData['deskripsi'],
            'tanggal' => $validatedData['tanggal'],
            'toppings' => $toppings,
            'packagings' => $packagings,
        ]);

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