<?php

namespace App\Http\Controllers;

use App\Models\StockMasuk;
use Illuminate\Http\Request;

class StockMasukController extends Controller
{
    /**
     * Daftar template item default (toping & packaging).
     */
    protected function defaultItems()
    {
        return [
            'Gurita' => 0,
            'Crabstick' => 0,
            'Udang' => 0,
            'Beef' => 0,
            'Bakso' => 0,
            'Sosis' => 0,
            'Box S' => 0,
            'Box M' => 0,
            'Box L' => 0,
            'Styrofoam' => 0,
        ];
    }

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
        $defaultItems = $this->defaultItems();
        return view('stock-masuk.create', compact('defaultItems'));
    }

    public function store(Request $request)
    {
        $defaultItems = $this->defaultItems();
        $itemKeys = array_keys($defaultItems);

        // Validasi
        $validatedData = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'tanggal' => 'required|date',
            'items' => 'required|array',
            'items.*' => 'numeric|min:0'
        ]);

        $items = [];
        foreach ($itemKeys as $item) {
            $items[$item] = (int) ($validatedData['items'][$item] ?? 0);
        }

        $dataToCreate = [
            'judul' => $validatedData['judul'],
            'deskripsi' => $validatedData['deskripsi'],
            'tanggal' => $validatedData['tanggal'],
            'items' => $items,
        ];

        StockMasuk::create($dataToCreate);

        return redirect()->route('stock-masuk.index')
            ->with('success', 'Stock masuk berhasil ditambahkan');
    }

    public function show(StockMasuk $stockMasuk)
    {
        $items = $stockMasuk->items ?? [];
        return view('stock-masuk.show', compact('stockMasuk', 'items'));
    }

    public function edit(StockMasuk $stockMasuk)
    {
        $defaultItems = $this->defaultItems();
        $items = array_merge($defaultItems, $stockMasuk->items ?? []);
        return view('stock-masuk.edit', compact('stockMasuk', 'items', 'defaultItems'));
    }

    public function update(Request $request, StockMasuk $stockMasuk)
    {
        $defaultItems = $this->defaultItems();
        $itemKeys = array_keys($defaultItems);

        $validatedData = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'tanggal' => 'required|date',
            'items' => 'required|array',
            'items.*' => 'numeric|min:0'
        ]);

        $items = [];
        foreach ($itemKeys as $item) {
            $items[$item] = (int) ($validatedData['items'][$item] ?? 0);
        }

        $dataToUpdate = [
            'judul' => $validatedData['judul'],
            'deskripsi' => $validatedData['deskripsi'],
            'tanggal' => $validatedData['tanggal'],
            'items' => $items,
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
