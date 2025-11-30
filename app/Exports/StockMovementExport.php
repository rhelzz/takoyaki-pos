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
            // Process toppings
            if ($record->toppings) {
                foreach ($record->toppings as $item) {
                    $quantity = $item['qty'] ?? 0;
                    $itemName = $item['name'] ?? '';
                    
                    if ($quantity > 0 && $itemName) {
                        $transactions->push([
                            'tanggal' => Carbon::parse($record->tanggal)->format('Y-m-d'),
                            'nama_bahan' => $itemName,
                            'kategori' => 'Topping',
                            'masuk' => (int)$quantity,
                            'keluar' => 0,
                        ]);
                    }
                }
            }
            
            // Process packagings
            if ($record->packagings) {
                foreach ($record->packagings as $item) {
                    $quantity = $item['qty'] ?? 0;
                    $itemName = $item['name'] ?? '';
                    
                    if ($quantity > 0 && $itemName) {
                        $transactions->push([
                            'tanggal' => Carbon::parse($record->tanggal)->format('Y-m-d'),
                            'nama_bahan' => $itemName,
                            'kategori' => 'Packaging',
                            'masuk' => (int)$quantity,
                            'keluar' => 0,
                        ]);
                    }
                }
            }
        }

        // Proses catatan stok keluar
        foreach ($keluarRecords as $record) {
            // Process toppings
            if ($record->toppings) {
                foreach ($record->toppings as $item) {
                    $quantity = $item['qty'] ?? 0;
                    $itemName = $item['name'] ?? '';
                    
                    if ($quantity > 0 && $itemName) {
                        $transactions->push([
                            'tanggal' => Carbon::parse($record->tanggal)->format('Y-m-d'),
                            'nama_bahan' => $itemName,
                            'kategori' => 'Topping',
                            'masuk' => 0,
                            'keluar' => (int)$quantity,
                        ]);
                    }
                }
            }
            
            // Process packagings
            if ($record->packagings) {
                foreach ($record->packagings as $item) {
                    $quantity = $item['qty'] ?? 0;
                    $itemName = $item['name'] ?? '';
                    
                    if ($quantity > 0 && $itemName) {
                        $transactions->push([
                            'tanggal' => Carbon::parse($record->tanggal)->format('Y-m-d'),
                            'nama_bahan' => $itemName,
                            'kategori' => 'Packaging',
                            'masuk' => 0,
                            'keluar' => (int)$quantity,
                        ]);
                    }
                }
            }
        }

        // Kelompokkan berdasarkan tanggal, nama bahan, dan kategori
        $grouped = $transactions->groupBy(['tanggal', 'nama_bahan', 'kategori'])->map(function ($dateGroup) {
            return $dateGroup->map(function ($itemGroup) {
                return $itemGroup->map(function ($kategoriGroup) {
                    return [
                        'masuk' => $kategoriGroup->sum('masuk'),
                        'keluar' => $kategoriGroup->sum('keluar'),
                        'kategori' => $kategoriGroup->first()['kategori'] ?? '',
                    ];
                });
            });
        });

        // Ubah menjadi format daftar datar dan urutkan
        $processedData = collect();
        foreach ($grouped as $tanggal => $items) {
            foreach ($items as $nama_bahan => $kategoriData) {
                foreach ($kategoriData as $kategori => $data) {
                    $processedData->push([
                        'tanggal' => $tanggal,
                        'nama_bahan' => $nama_bahan,
                        'kategori' => $data['kategori'],
                        'masuk' => $data['masuk'],
                        'keluar' => $data['keluar'],
                    ]);
                }
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
        $kategori = $row['kategori'] ?? '';
        
        // Inisialisasi saldo jika belum ada
        if (!isset($this->dailyBalances[$itemName])) {
            $this->dailyBalances[$itemName] = 0;
        }

        // Hitung stok akhir untuk hari ini
        $stockAkhir = $this->dailyBalances[$itemName] + $row['masuk'] - $row['keluar'];
        $this->dailyBalances[$itemName] = $stockAkhir; // Perbarui saldo

        return [
            Carbon::parse($row['tanggal'])->format('d/m/Y'),
            $kategori,
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
            ['Tanggal', 'Kategori', 'Nama Bahan', 'Stock Masuk', 'Stock Keluar', 'Stock Akhir'],
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
        $sheet->mergeCells('A1:F1');
        $sheet->mergeCells('A2:F2');
        $sheet->getStyle('A1:A2')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal('center');
        
        $sheet->getStyle('A4:F4')->getFont()->setBold(true);
        $sheet->getStyle('A4:F4')->getFill()
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
            'A' => 15,  // Tanggal
            'B' => 15,  // Kategori
            'C' => 30,  // Nama Bahan
            'D' => 18,  // Stock Masuk
            'E' => 18,  // Stock Keluar
            'F' => 18,  // Stock Akhir
        ];
    }
}