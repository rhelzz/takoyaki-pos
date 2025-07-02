<?php

namespace App\Exports;

use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ExpenseReportExport implements FromCollection, WithHeadings, WithTitle, WithStyles, WithColumnWidths
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
        // Karena belum ada tabel expenses terpisah, kita ambil dari total_cost transactions
        $dailyExpenses = Transaction::whereMonth('created_at', $this->month)
            ->whereYear('created_at', $this->year)
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_cost) as daily_cost'),
                DB::raw('COUNT(*) as transaction_count')
            )
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();

        $data = collect();

        // Header
        $data->push([
            'LAPORAN PENGELUARAN BULANAN',
            '',
            '',
            ''
        ]);

        $data->push([
            'Periode: ' . date('F Y', mktime(0, 0, 0, $this->month, 1, $this->year)),
            '',
            '',
            ''
        ]);

        $data->push(['', '', '', '']);

        // Headers
        $data->push([
            'Tanggal',
            'Jumlah Transaksi',
            'Total Cost (Modal)',
            'Rata-rata Cost per Transaksi'
        ]);

        // Data harian
        foreach ($dailyExpenses as $expense) {
            $avgCostPerTransaction = $expense->transaction_count > 0 ? 
                $expense->daily_cost / $expense->transaction_count : 0;

            $data->push([
                date('d/m/Y', strtotime($expense->date)),
                number_format($expense->transaction_count),
                'Rp ' . number_format($expense->daily_cost, 0, ',', '.'),
                'Rp ' . number_format($avgCostPerTransaction, 0, ',', '.')
            ]);
        }

        // Summary
        $totalCost = $dailyExpenses->sum('daily_cost');
        $totalTransactions = $dailyExpenses->sum('transaction_count');
        $avgDailyCost = $dailyExpenses->count() > 0 ? $totalCost / $dailyExpenses->count() : 0;

        $data->push(['', '', '', '']);
        $data->push([
            'SUMMARY BULANAN',
            '',
            '',
            ''
        ]);

        $data->push([
            'Total Pengeluaran (Modal)',
            '',
            'Rp ' . number_format($totalCost, 0, ',', '.'),
            ''
        ]);

        $data->push([
            'Total Transaksi',
            number_format($totalTransactions),
            '',
            ''
        ]);

        $data->push([
            'Rata-rata Pengeluaran Harian',
            '',
            'Rp ' . number_format($avgDailyCost, 0, ',', '.'),
            ''
        ]);

        return $data;
    }

    public function headings(): array
    {
        return [];
    }

    public function title(): string
    {
        return 'Pengeluaran Bulanan';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 16]],
            2 => ['font' => ['bold' => true, 'size' => 12]],
            4 => ['font' => ['bold' => true], 'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'E2E8F0']]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15,
            'B' => 18,
            'C' => 20,
            'D' => 25,
        ];
    }
}