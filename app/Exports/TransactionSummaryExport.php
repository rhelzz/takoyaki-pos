<?php

namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
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
        $transactions = Transaction::with(['user','items.product'])
            ->whereMonth('created_at', $this->month)
            ->whereYear('created_at', $this->year)
            ->orderBy('created_at')
            ->get();

        $rows = collect();

        foreach($transactions as $trx) {
            $metode = $trx->payment_method;
            $tipe = $trx->tax_amount > 0 ? 'Online' : 'Offline';

            $order_detail = $trx->items->map(function($item){
                return $item->product->name.' ('.$item->quantity.')';
            })->implode(', ');

            $rows->push([
                $trx->created_at->format('d/m/Y H:i'),
                $trx->transaction_code,
                optional($trx->user)->name ?? '-',
                is_null($trx->customer_money) ? '-' : 'Rp '.number_format($trx->customer_money, 0, ',', '.'),
                'Rp '.number_format($trx->change_amount, 0, ',', '.'),
                'Rp '.number_format($trx->total_amount, 0, ',', '.'),
                'Rp '.number_format($trx->tax_amount, 0, ',', '.'),
                'Rp '.number_format($trx->total_cost, 0, ',', '.'),
                'Rp '.number_format($trx->net_profit, 0, ',', '.'),
                ucfirst($metode),
                $tipe,
                $order_detail,
            ]);
        }
        return $rows;
    }

    public function headings(): array
    {
        return [
            ['DAFTAR TRANSAKSI PER BULAN'],
            ['Periode: ' . date('F Y', mktime(0,0,0,$this->month,1,$this->year))],
            [''],
            [
                'Tanggal',
                'Kode',
                'Kasir',
                'Uang Pelanggan',
                'Kembalian',
                'Uang Masuk',
                'Pajak',
                'Modal',
                'Net Profit',
                'Metode Pembayaran',
                'Online/Offline',
                'Menu & Qty'
            ]
        ];
    }

    public function title(): string
    {
        return 'List Transaksi';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font'=>['bold'=>true,'size'=>16]],
            2 => ['font'=>['bold'=>true,'size'=>12]],
            4 => ['font'=>['bold'=>true], 'fill'=>['fillType'=>Fill::FILL_SOLID, 'color'=>['rgb'=>'E2E8F0']]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A'=>18,'B'=>15,'C'=>18,'D'=>18,'E'=>12,'F'=>16,'G'=>12,'H'=>15,'I'=>16,'J'=>16,'K'=>15,'L'=>40
        ];
    }
}