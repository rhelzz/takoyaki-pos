<?php

namespace App\Exports;

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
        $data = collect();

        // Header
        $data->push([
            'LAPORAN PENGELUARAN BULANAN',
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
            ''
        ]);

        $data->push(['', '', '', '', '']);

        // Get daily expenses data from daily_expenses table
        $dailyExpenses = DB::table('daily_expenses')
            ->whereMonth('tanggal', $this->month)
            ->whereYear('tanggal', $this->year)
            ->orderBy('tanggal', 'desc')
            ->get();

        // SECTION 1: PENGELUARAN HARIAN
        $data->push([
            'PENGELUARAN HARIAN',
            '',
            '',
            '',
            ''
        ]);

        $data->push([
            'Tanggal',
            'Nama Pengeluaran',
            'Deskripsi',
            'Total',
            'Items'
        ]);

        $grandTotal = 0;
        
        if ($dailyExpenses->count() > 0) {
            foreach ($dailyExpenses as $expense) {
                $data->push([
                    date('d/m/Y', strtotime($expense->tanggal)),
                    $expense->nama_pengeluaran,
                    $expense->deskripsi ?? '-',
                    'Rp ' . number_format($expense->total, 0, ',', '.'),
                    ''
                ]);

                // Get expense items for this daily expense
                $expenseItems = DB::table('expense_items')
                    ->where('daily_expense_id', $expense->id)
                    ->get();

                if ($expenseItems->count() > 0) {
                    foreach ($expenseItems as $item) {
                        $data->push([
                            '',
                            '- ' . $item->nama_bahan,
                            'Qty: ' . $item->qty . ' @ Rp ' . number_format($item->harga_satuan, 0, ',', '.'),
                            'Rp ' . number_format($item->subtotal, 0, ',', '.'),
                            'Detail'
                        ]);
                    }
                }

                $grandTotal += $expense->total;
                $data->push(['', '', '', '', '']); // Empty row for spacing
            }
        } else {
            $data->push([
                'Tidak ada data pengeluaran',
                '',
                '',
                '',
                ''
            ]);
        }

        $data->push(['', '', '', '', '']);

        // SECTION 2: RINGKASAN KATEGORI PENGELUARAN
        $data->push([
            'RINGKASAN KATEGORI PENGELUARAN',
            '',
            '',
            '',
            ''
        ]);

        $data->push([
            'Kategori',
            'Jumlah Transaksi',
            'Total Pengeluaran',
            'Rata-rata',
            'Persentase'
        ]);

        // Group expenses by category (nama_pengeluaran)
        $categoryExpenses = DB::table('daily_expenses')
            ->whereMonth('tanggal', $this->month)
            ->whereYear('tanggal', $this->year)
            ->select('nama_pengeluaran')
            ->selectRaw('COUNT(*) as count')
            ->selectRaw('SUM(total) as total_amount')
            ->selectRaw('AVG(total) as avg_amount')
            ->groupBy('nama_pengeluaran')
            ->orderBy('total_amount', 'desc')
            ->get();

        foreach ($categoryExpenses as $category) {
            $percentage = $grandTotal > 0 ? round(($category->total_amount / $grandTotal) * 100, 2) : 0;
            
            $data->push([
                $category->nama_pengeluaran,
                number_format($category->count),
                'Rp ' . number_format($category->total_amount, 0, ',', '.'),
                'Rp ' . number_format($category->avg_amount, 0, ',', '.'),
                $percentage . '%'
            ]);
        }

        $data->push(['', '', '', '', '']);

        // SECTION 3: SUMMARY BULANAN
        $data->push([
            'SUMMARY BULANAN',
            '',
            '',
            '',
            ''
        ]);

        $totalTransactions = $dailyExpenses->count();
        $avgDailyExpense = $totalTransactions > 0 ? $grandTotal / $totalTransactions : 0;
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $this->month, $this->year);
        $avgExpensePerDay = $daysInMonth > 0 ? $grandTotal / $daysInMonth : 0;

        $data->push([
            'Total Pengeluaran',
            'Rp ' . number_format($grandTotal, 0, ',', '.'),
            '',
            '',
            ''
        ]);

        $data->push([
            'Jumlah Transaksi Pengeluaran',
            number_format($totalTransactions),
            '',
            '',
            ''
        ]);

        $data->push([
            'Rata-rata per Transaksi',
            'Rp ' . number_format($avgDailyExpense, 0, ',', '.'),
            '',
            '',
            ''
        ]);

        $data->push([
            'Rata-rata per Hari',
            'Rp ' . number_format($avgExpensePerDay, 0, ',', '.'),
            'Dalam ' . $daysInMonth . ' hari',
            '',
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
        return 'Laporan Pengeluaran';
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A:E')->getAlignment()->setVertical(Alignment::VERTICAL_TOP);
        
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => '1F2937']], 
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'FEE2E2']],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
            ],
            2 => [
                'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '374151']], 
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'F8F9FA']]
            ],
            4 => [
                'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '1F2937']], 
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'FBBF24']],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
            ],
            5 => [
                'font' => ['bold' => true, 'size' => 11], 
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'E2E8F0']],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
            ]
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15,
            'B' => 25,
            'C' => 30,
            'D' => 20,
            'E' => 15,
        ];
    }
}