<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\StockMasuk;
use App\Models\StockKeluar;
use Illuminate\Http\Request;

class StockSummaryController extends Controller
{
    public function index(Request $request)
    {
        // Ambil semua StockMasuk & StockKeluar sebagai transaksi terpisah
        $masuks = StockMasuk::all()->map(function($m) {
            $tgl = Carbon::parse($m->tanggal)->timezone('Asia/Jakarta');
            return [
                'id' => $m->id,
                'jenis' => 'masuk',
                'judul' => $m->judul,
                'deskripsi' => $m->deskripsi,
                'tanggal' => $tgl->format('Y-m-d'),
                'waktu' => $tgl->toDateTimeString(),
                'barang' => collect($m->items)->map(function($qty, $nama) {
                    return ['nama_barang' => $nama, 'qty' => $qty];
                })->values()->all(),
            ];
        });

        $keluars = StockKeluar::all()->map(function($k) {
            $tgl = Carbon::parse($k->tanggal)->timezone('Asia/Jakarta');
            return [
                'id' => $k->id,
                'jenis' => 'keluar',
                'judul' => $k->judul ?? '', // jika ada field judul di StockKeluar
                'deskripsi' => $k->deskripsi ?? '',
                'tanggal' => $tgl->format('Y-m-d'),
                'waktu' => $tgl->toDateTimeString(),
                'barang' => collect($k->items)->map(function($qty, $nama) {
                    return ['nama_barang' => $nama, 'qty' => $qty];
                })->values()->all(),
            ];
        });

        // Gabungkan dan urutkan DESC by waktu dan ID
        $allTransaksi = $masuks->merge($keluars)
            ->sortByDesc(fn($t) => $t['waktu'].'-'.$t['id'])
            ->values();

        return view('stock-summary.index', [
            'summaryTransaksi' => $allTransaksi
        ]);
    }

}