<?php

namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class TransactionSummaryExport implements FromCollection, WithHeadings, WithTitle, WithStyles, WithColumnWidths
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
        $transactions = Transaction::whereMonth('created_at', $this->month)
                                 ->whereYear('created_at', $this->year)
                                 ->get();

        $totalTransactions = $transactions->count();
        $onlineTransactions = $transactions->where('tax_amount', '>', 0)->count();
        $totalRevenue = $transactions->sum('total_amount');
        $totalCost = $transactions->sum('total_cost');
        $totalProfit = $transactions->sum('net_profit');
        $totalTax = $transactions->sum('tax_amount');

        return collect([
            [
                'Metrik',
                'Nilai',
                'Keterangan'
            ],
            [
                'Total Transaksi',
                number_format($totalTransactions),
                'Semua transaksi dalam bulan ini'
            ],
            [
                'Transaksi Online (Berpajak)',
                number_format($onlineTransactions),
                'Transaksi yang dikenakan pajak'
            ],
            [
                'Total Pendapatan',
                'Rp ' . number_format($totalRevenue, 0, ',', '.'),
                'Gross revenue'
            ],
            [
                'Total Modal',
                'Rp ' . number_format($totalCost, 0, ',', '.'),
                'Total cost of goods sold'
            ],
            [
                'Total Keuntungan',
                'Rp ' . number_format($totalProfit, 0, ',', '.'),
                'Net profit setelah dikurangi pajak'
            ],
            [
                'Total Pajak',
                'Rp ' . number_format($totalTax, 0, ',', '.'),
                'Total pajak yang dikumpulkan'
            ],
        ]);
    }

    public function headings(): array
    {
        return [
            ['LAPORAN SUMMARY TRANSAKSI BULANAN'],
            ['Periode: ' . date('F Y', mktime(0, 0, 0, $this->month, 1, $this->year))],
            ['Generated: ' . now()->format('d/m/Y H:i:s')],
            [''],
        ];
    }

    public function title(): string
    {
        return 'Summary Transaksi';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 16]],
            2 => ['font' => ['bold' => true, 'size' => 12]],
            3 => ['font' => ['italic' => true]],
            5 => ['font' => ['bold' => true], 'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'E2E8F0']]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 25,
            'B' => 20,
            'C' => 35,
        ];
    }
}