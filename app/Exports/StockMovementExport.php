<?php

namespace App\Exports;

use App\Models\StockMasuk;
use App\Models\StockKeluar;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Carbon\Carbon;

class StockMovementExport implements FromCollection, WithHeadings, WithTitle, WithStyles, WithColumnWidths, WithMapping
{
    protected $month;
    protected $year;
    private $dailyBalances = [];

    public function __construct($month, $year)
    {
        $this->month = $month;
        $this->year = $year;
    }

    /**
     * Mengambil dan memproses data stok dari database.
     *
     * @return Collection
     */
    public function collection(): Collection
    {
        // Ambil semua data stok masuk dan keluar untuk periode yang dipilih
        $masukRecords = StockMasuk::whereMonth('tanggal', $this->month)
            ->whereYear('tanggal', $this->year)
            ->get();

        $keluarRecords = StockKeluar::whereMonth('tanggal', $this->month)
            ->whereYear('tanggal', $this->year)
            ->get();

        $transactions = collect();

        // Proses catatan stok masuk
        foreach ($masukRecords as $record) {
            foreach ($record->items as $itemName => $quantity) {
                if ($quantity > 0) {
                    $transactions->push([
                        'tanggal' => Carbon::parse($record->tanggal)->format('Y-m-d'),
                        'nama_bahan' => $itemName,
                        'masuk' => (int)$quantity,
                        'keluar' => 0,
                    ]);
                }
            }
        }

        // Proses catatan stok keluar
        foreach ($keluarRecords as $record) {
            foreach ($record->items as $itemName => $quantity) {
                if ($quantity > 0) {
                    $transactions->push([
                        'tanggal' => Carbon::parse($record->tanggal)->format('Y-m-d'),
                        'nama_bahan' => $itemName,
                        'masuk' => 0,
                        'keluar' => (int)$quantity,
                    ]);
                }
            }
        }

        // Kelompokkan berdasarkan tanggal dan nama bahan
        $grouped = $transactions->groupBy(['tanggal', 'nama_bahan'])->map(function ($dateGroup) {
            return $dateGroup->map(function ($itemGroup) {
                return [
                    'masuk' => $itemGroup->sum('masuk'),
                    'keluar' => $itemGroup->sum('keluar'),
                ];
            });
        });

        // Ubah menjadi format daftar datar dan urutkan
        $processedData = collect();
        foreach ($grouped as $tanggal => $items) {
            foreach ($items as $nama_bahan => $data) {
                $processedData->push([
                    'tanggal' => $tanggal,
                    'nama_bahan' => $nama_bahan,
                    'masuk' => $data['masuk'],
                    'keluar' => $data['keluar'],
                ]);
            }
        }

        return $processedData->sortBy('tanggal')->values();
    }
    
    /**
     * Memetakan data untuk setiap baris.
     *
     * @param mixed $row
     * @return array
     */
    public function map($row): array
    {
        $itemName = $row['nama_bahan'];
        
        // Inisialisasi saldo jika belum ada
        if (!isset($this->dailyBalances[$itemName])) {
            $this->dailyBalances[$itemName] = 0;
        }

        // Hitung stok akhir untuk hari ini
        $stockAkhir = $this->dailyBalances[$itemName] + $row['masuk'] - $row['keluar'];
        $this->dailyBalances[$itemName] = $stockAkhir; // Perbarui saldo

        return [
            Carbon::parse($row['tanggal'])->format('d/m/Y'),
            $row['nama_bahan'],
            number_format($row['masuk']),
            number_format($row['keluar']),
            number_format($stockAkhir),
        ];
    }

    /**
     * Mendefinisikan heading untuk sheet Excel.
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            ['LAPORAN HARIAN PERGERAKAN STOCK'],
            ['Periode: ' . date('F Y', mktime(0, 0, 0, $this->month, 1, $this->year))],
            [''],
            ['Tanggal', 'Nama Bahan', 'Stock Masuk', 'Stock Keluar', 'Stock Akhir'],
        ];
    }

    /**
     * Memberikan judul untuk sheet.
     *
     * @return string
     */
    public function title(): string
    {
        return 'Stock Harian';
    }

    /**
     * Memberikan style untuk sheet.
     *
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet): array
    {
        $sheet->mergeCells('A1:E1');
        $sheet->mergeCells('A2:E2');
        $sheet->getStyle('A1:A2')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal('center');
        
        $sheet->getStyle('A4:E4')->getFont()->setBold(true);
        $sheet->getStyle('A4:E4')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('E2E8F0');
            
        return [];
    }

    /**
     * Mengatur lebar kolom.
     *
     * @return array
     */
    public function columnWidths(): array
    {
        return [
            'A' => 15,
            'B' => 30,
            'C' => 18,
            'D' => 18,
            'E' => 18,
        ];
    }
}