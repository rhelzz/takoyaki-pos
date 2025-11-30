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
            
            // Gabungkan toppings dan packagings menjadi satu array barang
            $barangList = collect();
            
            // Tambahkan toppings
            if ($m->toppings) {
                foreach ($m->toppings as $topping) {
                    $barangList->push([
                        'nama_barang' => $topping['name'] ?? '',
                        'qty' => $topping['qty'] ?? 0,
                        'kategori' => 'Topping'
                    ]);
                }
            }
            
            // Tambahkan packagings
            if ($m->packagings) {
                foreach ($m->packagings as $packaging) {
                    $barangList->push([
                        'nama_barang' => $packaging['name'] ?? '',
                        'qty' => $packaging['qty'] ?? 0,
                        'kategori' => 'Packaging'
                    ]);
                }
            }
            
            return [
                'id' => $m->id,
                'jenis' => 'masuk',
                'judul' => $m->judul,
                'deskripsi' => $m->deskripsi,
                'tanggal' => $tgl->format('Y-m-d'),
                'waktu' => $tgl->toDateTimeString(),
                'barang' => $barangList->all(),
            ];
        });

        $keluars = StockKeluar::all()->map(function($k) {
            $tgl = Carbon::parse($k->tanggal)->timezone('Asia/Jakarta');
            
            // Gabungkan toppings dan packagings menjadi satu array barang
            $barangList = collect();
            
            // Tambahkan toppings
            if ($k->toppings) {
                foreach ($k->toppings as $topping) {
                    $barangList->push([
                        'nama_barang' => $topping['name'] ?? '',
                        'qty' => $topping['qty'] ?? 0,
                        'kategori' => 'Topping'
                    ]);
                }
            }
            
            // Tambahkan packagings
            if ($k->packagings) {
                foreach ($k->packagings as $packaging) {
                    $barangList->push([
                        'nama_barang' => $packaging['name'] ?? '',
                        'qty' => $packaging['qty'] ?? 0,
                        'kategori' => 'Packaging'
                    ]);
                }
            }
            
            return [
                'id' => $k->id,
                'jenis' => 'keluar',
                'judul' => $k->judul ?? '',
                'deskripsi' => $k->deskripsi ?? '',
                'tanggal' => $tgl->format('Y-m-d'),
                'waktu' => $tgl->toDateTimeString(),
                'barang' => $barangList->all(),
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