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
        // Ambil semua nama bahan mentah dari stock_masuk dan stock_keluar
        $bahanMasuk = DB::table('stock_masuk')
            ->select('nama_barang')->distinct();
        $bahanKeluar = DB::table('stock_keluar')
            ->select('nama_barang')->distinct();
        $allBahan = $bahanMasuk->union($bahanKeluar)->pluck('nama_barang')->unique();

        $result = collect();

        foreach ($allBahan as $bahan) {
            $masuk = DB::table('stock_masuk')
                ->where('nama_barang', $bahan)
                ->whereMonth('tanggal', $this->month)
                ->whereYear('tanggal', $this->year)
                ->sum('qty');
            $keluar = DB::table('stock_keluar')
                ->where('nama_barang', $bahan)
                ->whereMonth('tanggal', $this->month)
                ->whereYear('tanggal', $this->year)
                ->sum('qty');
            // Stok akhir = total masuk - total keluar (khusus bahan mentah)
            $stock_akhir = $masuk - $keluar;

            $result->push([
                $bahan,
                number_format($masuk),
                number_format($keluar),
                number_format($stock_akhir),
            ]);
        }

        return $result;
    }

    public function headings(): array
    {
        return [
            ['REKAPITULASI STOCK BAHAN MENTAH'],
            ['Periode: ' . date('F Y', mktime(0,0,0,$this->month,1,$this->year))],
            [''],
            ['Nama Bahan','Stock Masuk','Stock Keluar','Stock Akhir'],
        ];
    }

    public function title(): string
    {
        return 'Stock Bahan Mentah';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold'=>true, 'size'=>16]],
            2 => ['font' => ['bold'=>true, 'size'=>12]],
            4 => ['font' => ['bold'=>true], 'fill'=>['fillType'=>Fill::FILL_SOLID, 'color'=>['rgb'=>'E2E8F0']]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A'=>24,'B'=>16,'C'=>18,'D'=>15
        ];
    }
}