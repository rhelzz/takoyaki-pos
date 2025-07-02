<?php

namespace App\Http\Controllers;

use App\Models\StockMasuk;
use App\Models\StockKeluar;
use Illuminate\Http\Request;

class StockSummaryController extends Controller
{
    public function index(Request $request)
    {
        // Ambil semua nama barang unik dari stock masuk dan keluar
        $stockMasukBarang = StockMasuk::select('nama_barang')->distinct()->pluck('nama_barang');
        $stockKeluarBarang = StockKeluar::select('nama_barang')->distinct()->pluck('nama_barang');
        $allBarang = $stockMasukBarang->merge($stockKeluarBarang)->unique()->sort()->values();

        $stockSummary = [];

        foreach ($allBarang as $namaBarang) {
            $totalMasuk = StockMasuk::where('nama_barang', $namaBarang)->sum('qty');
            $totalKeluar = StockKeluar::where('nama_barang', $namaBarang)->sum('qty');
            $stockNow = $totalMasuk - $totalKeluar;

            $stockSummary[] = [
                'nama_barang' => $namaBarang,
                'total_masuk' => $totalMasuk,
                'total_keluar' => $totalKeluar,
                'stock_now' => $stockNow
            ];
        }

        // Sort berdasarkan nama barang
        usort($stockSummary, function($a, $b) {
            return strcmp($a['nama_barang'], $b['nama_barang']);
        });

        // Filter search jika ada
        if ($request->filled('search')) {
            $search = strtolower($request->search);
            $stockSummary = array_filter($stockSummary, function($item) use ($search) {
                return strpos(strtolower($item['nama_barang']), $search) !== false;
            });
        }

        // Convert ke collection untuk pagination
        $collection = collect($stockSummary);
        
        // Manual pagination
        $perPage = 10;
        $currentPage = $request->get('page', 1);
        $offset = ($currentPage - 1) * $perPage;
        
        $paginatedItems = $collection->slice($offset, $perPage)->values();
        
        // Create paginator instance
        $stockSummary = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedItems,
            $collection->count(),
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        // Summary stats
        $totalItems = $collection->count();
        $lowStock = $collection->where('stock_now', '<=', 3)->count(); // Changed to 3
        $outOfStock = $collection->where('stock_now', '<=', 0)->count();

        return view('stock-summary.index', compact('stockSummary', 'totalItems', 'lowStock', 'outOfStock'));
    }
}
