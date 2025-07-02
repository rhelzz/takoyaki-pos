<?php

namespace App\Exports;

use App\Models\TransactionItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class StockMovementExport implements FromCollection, WithHeadings, WithTitle, WithStyles, WithColumnWidths
{
    protected $month;
    protected $year;

    public function __construct($month, $year)
    {
        $this->month = $month;
        $this->year = $year;
    }

    public function collection()
    {
        $data = collect();

        // Header info
        $data->push([
            'LAPORAN PERGERAKAN STOCK',
            '',
            '',
            '',
            '',
            ''
        ]);

        $data->push([
            'Periode: ' . date('F Y', mktime(0, 0, 0, $this->month, 1, $this->year)),
            '',
            '',
            '',
            '',
            ''
        ]);

        $data->push(['', '', '', '', '', '']);

        // SECTION 1: STOCK MASUK
        $data->push([
            'STOCK MASUK',
            '',
            '',
            '',
            '',
            ''
        ]);

        $data->push([
            'Tanggal',
            'Nama Barang',
            'Quantity Masuk',
            'Keterangan',
            '',
            ''
        ]);

        // Ambil data stock masuk dari tabel stock_masuk jika ada
        $stockMasuk = DB::table('stock_masuk')
            ->whereMonth('tanggal', $this->month)
            ->whereYear('tanggal', $this->year)
            ->orderBy('tanggal', 'desc')
            ->get();

        if ($stockMasuk->count() > 0) {
            foreach ($stockMasuk as $item) {
                $data->push([
                    date('d/m/Y', strtotime($item->tanggal)),
                    $item->nama_barang,
                    number_format($item->qty),
                    'Stock Masuk',
                    '',
                    ''
                ]);
            }
        } else {
            $data->push([
                'Tidak ada data stock masuk',
                '',
                '',
                '',
                '',
                ''
            ]);
        }

        $data->push(['', '', '', '', '', '']);

        // SECTION 2: STOCK KELUAR
        $data->push([
            'STOCK KELUAR',
            '',
            '',
            '',
            '',
            ''
        ]);

        $data->push([
            'Tanggal',
            'Nama Barang',
            'Quantity Keluar',
            'Keterangan',
            '',
            ''
        ]);

        // Ambil data stock keluar dari tabel stock_keluar
        $stockKeluar = DB::table('stock_keluar')
            ->whereMonth('tanggal', $this->month)
            ->whereYear('tanggal', $this->year)
            ->orderBy('tanggal', 'desc')
            ->get();

        if ($stockKeluar->count() > 0) {
            foreach ($stockKeluar as $item) {
                $data->push([
                    date('d/m/Y', strtotime($item->tanggal)),
                    $item->nama_barang,
                    number_format($item->qty),
                    'Stock Keluar',
                    '',
                    ''
                ]);
            }
        } else {
            $data->push([
                'Tidak ada data stock keluar',
                '',
                '',
                '',
                '',
                ''
            ]);
        }

        $data->push(['', '', '', '', '', '']);

        // SECTION 3: STOCK KELUAR DARI PENJUALAN
        $data->push([
            'STOCK KELUAR DARI PENJUALAN',
            '',
            '',
            '',
            '',
            ''
        ]);

        $data->push([
            'Nama Menu',
            'Total Terjual',
            'Satuan',
            'Estimasi Stock Keluar',
            '',
            ''
        ]);

        // Stock keluar dari penjualan (transaction_items)
        $stockOutSales = TransactionItem::join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
            ->join('products', 'transaction_items.product_id', '=', 'products.id')
            ->whereMonth('transactions.created_at', $this->month)
            ->whereYear('transactions.created_at', $this->year)
            ->select(
                'products.name as product_name',
                'products.quantity_per_serving',
                DB::raw('SUM(transaction_items.quantity) as total_sold')
            )
            ->groupBy('products.id', 'products.name', 'products.quantity_per_serving')
            ->orderBy('total_sold', 'desc')
            ->get();

        if ($stockOutSales->count() > 0) {
            foreach ($stockOutSales as $item) {
                $estimatedStockOut = $item->total_sold * $item->quantity_per_serving;
                
                $data->push([
                    $item->product_name,
                    number_format($item->total_sold),
                    $item->quantity_per_serving . ' per porsi',
                    number_format($estimatedStockOut) . ' unit',
                    '',
                    ''
                ]);
            }
        } else {
            $data->push([
                'Tidak ada penjualan bulan ini',
                '',
                '',
                '',
                '',
                ''
            ]);
        }

        $data->push(['', '', '', '', '', '']);

        // SUMMARY
        $data->push([
            'SUMMARY',
            '',
            '',
            '',
            '',
            ''
        ]);

        $totalStockMasuk = $stockMasuk->sum('qty');
        $totalStockKeluar = $stockKeluar->sum('qty');
        $totalStockOutSales = $stockOutSales->sum(function($item) {
            return $item->total_sold * $item->quantity_per_serving;
        });

        $data->push([
            'Total Stock Masuk',
            number_format($totalStockMasuk),
            'unit',
            '',
            '',
            ''
        ]);

        $data->push([
            'Total Stock Keluar Manual',
            number_format($totalStockKeluar),
            'unit',
            '',
            '',
            ''
        ]);

        $data->push([
            'Total Stock Keluar dari Penjualan',
            number_format($totalStockOutSales),
            'unit (estimasi)',
            '',
            '',
            ''
        ]);

        $data->push([
            'Net Movement',
            number_format($totalStockMasuk - $totalStockKeluar - $totalStockOutSales),
            'unit',
            $totalStockMasuk > ($totalStockKeluar + $totalStockOutSales) ? 'Surplus' : 'Defisit',
            '',
            ''
        ]);

        $data->push(['', '', '', '', '', '']);

        // SECTION 4: STOCK SAAT INI (ESTIMASI)
        $data->push([
            'STOCK SAAT INI (ESTIMASI BERDASARKAN PERGERAKAN)',
            '',
            '',
            '',
            '',
            ''
        ]);

        $data->push([
            'Nama Barang',
            'Stock Masuk',
            'Stock Keluar Manual',
            'Stock Keluar Sales',
            'Stock Tersisa',
            'Status'
        ]);

        // Calculate current stock per item
        $allItems = collect();
        
        // Add items from stock masuk
        foreach ($stockMasuk as $item) {
            $existing = $allItems->firstWhere('nama_barang', $item->nama_barang);
            if ($existing) {
                $existing['stock_masuk'] += $item->qty;
            } else {
                $allItems->push([
                    'nama_barang' => $item->nama_barang,
                    'stock_masuk' => $item->qty,
                    'stock_keluar' => 0,
                    'stock_sales' => 0
                ]);
            }
        }

        // Add items from stock keluar
        foreach ($stockKeluar as $item) {
            $existing = $allItems->firstWhere('nama_barang', $item->nama_barang);
            if ($existing) {
                $existing['stock_keluar'] += $item->qty;
            } else {
                $allItems->push([
                    'nama_barang' => $item->nama_barang,
                    'stock_masuk' => 0,
                    'stock_keluar' => $item->qty,
                    'stock_sales' => 0
                ]);
            }
        }

        // Add estimated stock used from sales (this is more complex as we need to match product names to material names)
        // For now, we'll show this as a separate calculation

        if ($allItems->count() > 0) {
            foreach ($allItems as $item) {
                $currentStock = $item['stock_masuk'] - $item['stock_keluar'] - $item['stock_sales'];
                $status = $currentStock > 0 ? 'Tersedia' : ($currentStock == 0 ? 'Habis' : 'Kekurangan');
                
                $data->push([
                    $item['nama_barang'],
                    number_format($item['stock_masuk']),
                    number_format($item['stock_keluar']),
                    number_format($item['stock_sales']),
                    number_format($currentStock),
                    $status
                ]);
            }
        } else {
            $data->push([
                'Tidak ada data untuk menghitung stock saat ini',
                '',
                '',
                '',
                '',
                ''
            ]);
        }

        return $data;
    }

    public function headings(): array
    {
        return [];
    }

    public function title(): string
    {
        return 'Stock Movement';
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A:F')->getAlignment()->setVertical(Alignment::VERTICAL_TOP);
        
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => '1F2937']], 
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'DBEAFE']],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
            ],
            2 => [
                'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '374151']], 
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'F8F9FA']]
            ],
            5 => [
                'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '1F2937']], 
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'D1FAE5']],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
            ],
            6 => [
                'font' => ['bold' => true, 'size' => 11], 
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'E2E8F0']],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
            ]
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'B' => 25,
            'C' => 18,
            'D' => 25,
            'E' => 15,
            'F' => 15,
        ];
    }
}