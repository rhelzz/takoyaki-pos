<?php

namespace App\Http\Controllers;

use App\Models\StockMasuk;
use Illuminate\Http\Request;

class StockMasukController extends Controller
{
    public function index(Request $request)
    {
        $query = StockMasuk::orderBy('tanggal', 'desc');

        // Filter berdasarkan judul
        if ($request->filled('search')) {
            $query->where('judul', 'like', '%' . $request->search . '%');
        }

        // Filter berdasarkan tanggal
        if ($request->filled('date')) {
            $query->whereDate('tanggal', $request->date);
        }

        $stockMasuk = $query->paginate(10)->appends($request->query());

        return view('stock-masuk.index', compact('stockMasuk'));
    }

    public function create()
    {
        return view('stock-masuk.create');
    }

    public function store(Request $request)
    {
        // Validasi
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

        $dataToCreate = [
            'judul' => $validatedData['judul'],
            'deskripsi' => $validatedData['deskripsi'],
            'tanggal' => $validatedData['tanggal'],
            'toppings' => $toppings,
            'packagings' => $packagings,
        ];

        StockMasuk::create($dataToCreate);

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

        $dataToUpdate = [
            'judul' => $validatedData['judul'],
            'deskripsi' => $validatedData['deskripsi'],
            'tanggal' => $validatedData['tanggal'],
            'toppings' => $toppings,
            'packagings' => $packagings,
        ];

        $stockMasuk->update($dataToUpdate);

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
