<?php

namespace App\Exports;

use App\Models\TransactionItem;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class MenuSalesExport implements FromCollection, WithHeadings, WithTitle, WithStyles, WithColumnWidths
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
        $menuSales = TransactionItem::join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
            ->join('products', 'transaction_items.product_id', '=', 'products.id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->whereMonth('transactions.created_at', $this->month)
            ->whereYear('transactions.created_at', $this->year)
            ->select(
                'products.name as menu_name',
                'categories.name as category',
                'products.cost_price',
                'products.selling_price',
                DB::raw('SUM(transaction_items.quantity) as total_quantity'),
                DB::raw('SUM(transaction_items.total_price) as total_revenue'),
                DB::raw('SUM(transaction_items.total_cost) as total_cost'),
                DB::raw('SUM(transaction_items.total_price - transaction_items.total_cost) as total_profit'),
                DB::raw('AVG(transaction_items.unit_price) as avg_price')
            )
            ->groupBy(
                'products.id',
                'products.name',
                'categories.name',
                'products.cost_price',
                'products.selling_price'
            )
            ->orderBy('total_quantity', 'desc')
            ->get();

        $totalRow = [
            'TOTAL',
            '',
            $menuSales->sum('total_quantity'),
            'Rp ' . number_format($menuSales->sum('total_revenue'), 0, ',', '.'),
            'Rp ' . number_format($menuSales->sum('total_cost'), 0, ',', '.'),
            'Rp ' . number_format($menuSales->sum('total_profit'), 0, ',', '.'),
            '',
            '', '', ''
        ];

        $data = $menuSales->map(function ($item) {
            $profitMargin = $item->total_revenue > 0 ?
                round(($item->total_profit / $item->total_revenue) * 100, 2) : 0;

            return [
                $item->menu_name,
                $item->category ?? 'Tidak ada kategori',
                number_format($item->total_quantity),
                'Rp ' . number_format($item->total_revenue, 0, ',', '.'),
                'Rp ' . number_format($item->total_cost, 0, ',', '.'),
                'Rp ' . number_format($item->total_profit, 0, ',', '.'),
                'Rp ' . number_format($item->avg_price, 0, ',', '.'),
                $profitMargin . '%',
                'Rp ' . number_format($item->cost_price, 0, ',', '.'),
                'Rp ' . number_format($item->selling_price, 0, ',', '.'),
            ];
        });

        // Tambahkan baris total di akhir data
        $data->push($totalRow);

        return $data;
    }

    public function headings(): array
    {
        return [
            ['LAPORAN PENJUALAN PER MENU'],
            ['Periode: ' . date('F Y', mktime(0, 0, 0, $this->month, 1, $this->year))],
            [''],
            [
                'Nama Menu',
                'Kategori',
                'Total Terjual',
                'Total Pendapatan',
                'Total Modal',
                'Total Keuntungan',
                'Harga Rata-rata',
                'Margin (%)',
                'Harga Modal/Unit',
                'Harga Jual/Unit',
            ]
        ];
    }

    public function title(): string
    {
        return 'Penjualan Menu';
    }

    public function styles(Worksheet $sheet)
    {
        // Styling header
        $styleArray = [
            1 => ['font' => ['bold' => true, 'size' => 16]],
            2 => ['font' => ['bold' => true, 'size' => 12]],
            4 => ['font' => ['bold' => true], 'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'E2E8F0']]],
        ];

        // Styling baris total (baris terakhir)
        $lastRow = 4 + $sheet->getHighestRow() - 3;
        $styleArray[$lastRow] = [
            'font' => ['bold' => true, 'color' => ['rgb' => '0A0A0A']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'FFF9C4']],
        ];

        return $styleArray;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 25,
            'B' => 15,
            'C' => 12,
            'D' => 18,
            'E' => 15,
            'F' => 18,
            'G' => 15,
            'H' => 12,
            'I' => 15,
            'J' => 15,
        ];
    }
}