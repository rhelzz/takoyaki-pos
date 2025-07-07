<?php

namespace App\Http\Controllers;

use App\Models\StockKeluar;
use Illuminate\Http\Request;

class StockKeluarController extends Controller
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
        $defaultItems = $this->defaultItems();
        return view('stock-keluar.create', compact('defaultItems'));
    }

    public function store(Request $request)
    {
        $defaultItems = $this->defaultItems();
        $itemKeys = array_keys($defaultItems);

        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'tanggal' => 'required|date',
            'items' => 'required|array',
        ]);

        $items = [];
        foreach ($itemKeys as $item) {
            $items[$item] = (int) ($request->items[$item] ?? 0);
        }

        StockKeluar::create([
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'tanggal' => $request->tanggal,
            'items' => $items,
        ]);

        return redirect()->route('stock-keluar.index')
            ->with('success', 'Stock keluar berhasil ditambahkan');
    }

    public function show(StockKeluar $stockKeluar)
    {
        $items = $stockKeluar->items ?? [];
        return view('stock-keluar.show', compact('stockKeluar', 'items'));
    }

    public function edit(StockKeluar $stockKeluar)
    {
        $defaultItems = $this->defaultItems();
        $items = array_merge($defaultItems, $stockKeluar->items ?? []);
        return view('stock-keluar.edit', compact('stockKeluar', 'items', 'defaultItems'));
    }

    public function update(Request $request, StockKeluar $stockKeluar)
    {
        $defaultItems = $this->defaultItems();
        $itemKeys = array_keys($defaultItems);

        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'tanggal' => 'required|date',
            'items' => 'required|array',
        ]);

        $items = [];
        foreach ($itemKeys as $item) {
            $items[$item] = (int) ($request->items[$item] ?? 0);
        }

        $stockKeluar->update([
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'tanggal' => $request->tanggal,
            'items' => $items,
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