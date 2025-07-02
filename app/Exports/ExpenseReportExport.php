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
        $expenses = DB::table('daily_expenses')
            ->whereMonth('tanggal', $this->month)
            ->whereYear('tanggal', $this->year)
            ->orderBy('tanggal')
            ->get();

        $rows = collect();
        foreach($expenses as $exp) {
            $rows->push([
                date('d/m/Y', strtotime($exp->tanggal)),
                $exp->nama_pengeluaran,
                $exp->deskripsi,
                'Rp '.number_format($exp->total, 0, ',', '.'),
                ''
            ]);
            // Breakdown per item
            $items = DB::table('expense_items')->where('daily_expense_id', $exp->id)->get();
            foreach($items as $itm) {
                $rows->push([
                    '',
                    '- '.$itm->nama_bahan,
                    $itm->qty.' x Rp '.number_format($itm->harga_satuan,0,',','.'),
                    'Rp '.number_format($itm->subtotal,0,',','.'),
                    ''
                ]);
            }
        }

        // Summary
        $total = $expenses->sum('total');
        $rows->push(['','','TOTAL', 'Rp '.number_format($total,0,',','.'), '']);
        return $rows;
    }

    public function headings(): array
    {
        return [
            ['LAPORAN PENGELUARAN'],
            ['Periode: ' . date('F Y', mktime(0,0,0,$this->month,1,$this->year))],
            [''],
            ['Tanggal','Nama Pengeluaran','Deskripsi/Detail','Total','']
        ];
    }

    public function title(): string
    {
        return 'Pengeluaran';
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
            'A'=>15, 'B'=>28, 'C'=>25, 'D'=>18, 'E'=>8
        ];
    }
}