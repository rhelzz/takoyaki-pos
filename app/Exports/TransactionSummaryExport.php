<?php

namespace App\Exports;

use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

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
        $data = collect();
        
        // Header info
        $data->push([
            'LAPORAN DETAIL TRANSAKSI BULANAN',
            '',
            '',
            '',
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
            '',
            '',
            '',
            ''
        ]);

        $data->push(['', '', '', '', '', '', '', '', '']);

        // SECTION 1: SUMMARY METRICS
        $transactions = Transaction::with('items.product')
                                 ->whereMonth('created_at', $this->month)
                                 ->whereYear('created_at', $this->year)
                                 ->orderBy('created_at', 'desc')
                                 ->get();

        $totalTransactions = $transactions->count();
        $offlineTransactions = $transactions->where('payment_method', 'cash')->count();
        $onlineTransactions = $transactions->whereNotIn('payment_method', ['cash'])->count();
        $totalRevenue = $transactions->sum('total_amount');
        $totalCost = $transactions->sum('total_cost');
        $totalProfit = $transactions->sum('net_profit');
        $totalTax = $transactions->sum('tax_amount');
        $totalCustomerMoney = $transactions->sum('customer_money');
        $totalChange = $transactions->sum('change_amount');

        $data->push([
            'RINGKASAN TRANSAKSI',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            ''
        ]);

        $data->push([
            'Metrik',
            'Nilai',
            'Keterangan',
            '',
            '',
            '',
            '',
            '',
            ''
        ]);

        $data->push([
            'Total Transaksi',
            number_format($totalTransactions),
            'Semua transaksi dalam bulan ini',
            '',
            '',
            '',
            '',
            '',
            ''
        ]);

        $data->push([
            'Transaksi Offline (Tunai)',
            number_format($offlineTransactions),
            'Pembayaran tunai',
            '',
            '',
            '',
            '',
            '',
            ''
        ]);

        $data->push([
            'Transaksi Online',
            number_format($onlineTransactions),
            'Card, DANA, OVO, GoPay, dll',
            '',
            '',
            '',
            '',
            '',
            ''
        ]);

        $data->push([
            'Total Pendapatan',
            'Rp ' . number_format($totalRevenue, 0, ',', '.'),
            'Gross revenue',
            '',
            '',
            '',
            '',
            '',
            ''
        ]);

        $data->push([
            'Total Modal',
            'Rp ' . number_format($totalCost, 0, ',', '.'),
            'Total cost of goods sold',
            '',
            '',
            '',
            '',
            '',
            ''
        ]);

        $data->push([
            'Total Keuntungan',
            'Rp ' . number_format($totalProfit, 0, ',', '.'),
            'Net profit setelah dikurangi pajak',
            '',
            '',
            '',
            '',
            '',
            ''
        ]);

        $data->push([
            'Total Pajak',
            'Rp ' . number_format($totalTax, 0, ',', '.'),
            'Total pajak yang dikumpulkan',
            '',
            '',
            '',
            '',
            '',
            ''
        ]);

        $data->push([
            'Total Uang Masuk',
            'Rp ' . number_format($totalCustomerMoney, 0, ',', '.'),
            'Total uang yang dibayar customer',
            '',
            '',
            '',
            '',
            '',
            ''
        ]);

        $data->push([
            'Total Kembalian',
            'Rp ' . number_format($totalChange, 0, ',', '.'),
            'Total kembalian yang diberikan',
            '',
            '',
            '',
            '',
            '',
            ''
        ]);

        $data->push(['', '', '', '', '', '', '', '', '']);

        // SECTION 2: DETAIL TRANSAKSI
        $data->push([
            'DETAIL TRANSAKSI',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            ''
        ]);

        $data->push([
            'Kode Transaksi',
            'Tanggal',
            'Waktu',
            'Metode Bayar',
            'Total Amount',
            'Modal',
            'Profit',
            'Pajak',
            'Uang Bayar'
        ]);

        $data->push([
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            'Kembalian'
        ]);

        foreach ($transactions as $transaction) {
            $paymentLabel = match($transaction->payment_method) {
                'cash' => 'Tunai',
                'card' => 'Kartu',
                'dana' => 'DANA',
                'gopay' => 'GoPay',
                'ovo' => 'OVO',
                default => ucfirst($transaction->payment_method)
            };

            $taxLabel = $transaction->tax_amount > 0 ? 'Ya' : 'Tidak';

            $data->push([
                $transaction->transaction_code,
                $transaction->created_at->format('d/m/Y'),
                $transaction->created_at->format('H:i'),
                $paymentLabel,
                'Rp ' . number_format($transaction->total_amount, 0, ',', '.'),
                'Rp ' . number_format($transaction->total_cost, 0, ',', '.'),
                'Rp ' . number_format($transaction->net_profit, 0, ',', '.'),
                $taxLabel . ($transaction->tax_amount > 0 ? ' (Rp ' . number_format($transaction->tax_amount, 0, ',', '.') . ')' : ''),
                $transaction->customer_money ? 'Rp ' . number_format($transaction->customer_money, 0, ',', '.') : '-'
            ]);

            // Show change amount in the next column
            $changeAmount = $transaction->change_amount > 0 ? 'Rp ' . number_format($transaction->change_amount, 0, ',', '.') : '-';
            $data->push([
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                $changeAmount
            ]);

            // Show transaction items
            foreach ($transaction->items as $item) {
                $data->push([
                    '- ' . $item->product->name,
                    'Qty: ' . $item->quantity,
                    '@Rp ' . number_format($item->unit_price, 0, ',', '.'),
                    'Total: Rp ' . number_format($item->total_price, 0, ',', '.'),
                    '',
                    '',
                    '',
                    '',
                    ''
                ]);
            }

            $data->push(['', '', '', '', '', '', '', '', '']);
        }

        return $data;
    }

    public function headings(): array
    {
        return [];
    }

    public function title(): string
    {
        return 'Detail Transaksi';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => '1F2937']], 
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'E3F2FD']],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
            ],
            2 => [
                'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '374151']], 
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'F8F9FA']]
            ],
            5 => [
                'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '1F2937']], 
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'FEF3C7']],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
            ],
            6 => [
                'font' => ['bold' => true, 'size' => 11], 
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'E2E8F0']],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
            ],
            // Add borders to summary section (rows 7-16)
            '7:16' => [
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
            ]
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'B' => 15,
            'C' => 15,
            'D' => 18,
            'E' => 18,
            'F' => 15,
            'G' => 15,
            'H' => 20,
            'I' => 18,
        ];
    }
}