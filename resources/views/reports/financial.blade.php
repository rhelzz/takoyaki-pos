@extends('layouts.app')

@section('title', 'Laporan Keuangan - Takoyaki POS')

@section('content')
<div class="p-4 max-w-7xl mx-auto" x-data="financialReport()">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center mb-4">
            <a href="{{ route('reports.index') }}" 
               class="mr-3 p-2 text-gray-600 hover:text-gray-800">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Laporan Keuangan</h1>
                <p class="text-gray-600">Analisis profit & loss dan kinerja keuangan</p>
            </div>
        </div>

        <!-- Date Range Selector -->
        <div class="bg-white rounded-lg shadow p-4">
            <form method="GET" class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-3 lg:space-y-0">
                <div class="flex flex-col lg:flex-row lg:items-center space-y-3 lg:space-y-0 lg:space-x-3">
                    <label class="text-sm font-medium text-gray-700">Periode:</label>
                    <input type="date" 
                           name="start_date" 
                           value="{{ $startDate->format('Y-m-d') }}"
                           class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                    <span class="text-gray-500">s/d</span>
                    <input type="date" 
                           name="end_date" 
                           value="{{ $endDate->format('Y-m-d') }}"
                           max="{{ today()->format('Y-m-d') }}"
                           class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                    <button type="submit" 
                            class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg">
                        <i class="fas fa-search mr-2"></i>Lihat
                    </button>
                </div>

                <div class="flex space-x-2">
                    <a href="{{ route('reports.financial', ['start_date' => now()->startOfMonth()->format('Y-m-d'), 'end_date' => now()->endOfMonth()->format('Y-m-d')]) }}" 
                       class="bg-gray-500 hover:bg-gray-600 text-white px-3 py-2 rounded-lg text-sm">
                        Bulan Ini
                    </a>
                    <a href="{{ route('reports.financial', ['start_date' => now()->subMonth()->startOfMonth()->format('Y-m-d'), 'end_date' => now()->subMonth()->endOfMonth()->format('Y-m-d')]) }}" 
                       class="bg-gray-500 hover:bg-gray-600 text-white px-3 py-2 rounded-lg text-sm">
                        Bulan Lalu
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Financial Summary -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 mb-6">
        <!-- Total Revenue -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium text-gray-600">Total Pendapatan</h3>
                <i class="fas fa-dollar-sign text-green-500"></i>
            </div>
            <div class="text-2xl font-bold text-green-600 mb-1">
                Rp {{ number_format($summary['total_revenue'], 0, ',', '.') }}
            </div>
            <div class="text-sm text-gray-500">
                {{ $summary['transaction_count'] }} transaksi
            </div>
        </div>

        <!-- Total Cost -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium text-gray-600">Total Biaya Modal</h3>
                <i class="fas fa-money-bill text-red-500"></i>
            </div>
            <div class="text-2xl font-bold text-red-600 mb-1">
                Rp {{ number_format($summary['total_cost'], 0, ',', '.') }}
            </div>
            <div class="text-sm text-gray-500">
                Biaya produksi
            </div>
        </div>

        <!-- Gross Profit -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium text-gray-600">Laba Kotor</h3>
                <i class="fas fa-chart-line text-blue-500"></i>
            </div>
            <div class="text-2xl font-bold text-blue-600 mb-1">
                Rp {{ number_format($summary['gross_profit'], 0, ',', '.') }}
            </div>
            <div class="text-sm text-gray-500">
                Sebelum pajak
            </div>
        </div>

        <!-- Net Profit -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium text-gray-600">Laba Bersih</h3>
                <i class="fas fa-coins text-purple-500"></i>
            </div>
            <div class="text-2xl font-bold text-purple-600 mb-1">
                Rp {{ number_format($summary['net_profit'], 0, ',', '.') }}
            </div>
            <div class="text-sm text-gray-500">
                Margin: {{ number_format($summary['profit_margin'], 1) }}%
            </div>
        </div>
    </div>

    <!-- Additional Metrics -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
        <!-- Tax & Discount -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Pajak & Diskon</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Total Pajak:</span>
                    <span class="font-medium text-orange-600">Rp {{ number_format($summary['total_tax'], 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Total Diskon:</span>
                    <span class="font-medium text-red-600">Rp {{ number_format($summary['total_discount'], 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Transaksi dengan Diskon:</span>
                    <span class="font-medium">{{ $taxDiscountAnalysis['transactions_with_discount'] ?? 0 }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Transaksi dengan Pajak:</span>
                    <span class="font-medium">{{ $taxDiscountAnalysis['transactions_with_tax'] ?? 0 }}</span>
                </div>
            </div>
        </div>

        <!-- Average Metrics -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Rata-rata</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Per Transaksi:</span>
                    <span class="font-medium text-blue-600">Rp {{ number_format($summary['avg_transaction'], 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Per Hari:</span>
                    <span class="font-medium text-green-600">
                        Rp {{ number_format($summary['total_revenue'] / max(1, $startDate->diffInDays($endDate) + 1), 0, ',', '.') }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Diskon (%):</span>
                    <span class="font-medium text-red-600">{{ number_format($taxDiscountAnalysis['avg_discount_percentage'] ?? 0, 1) }}%</span>
                </div>
            </div>
        </div>

        <!-- Payment Methods -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Metode Pembayaran</h3>
            <div class="space-y-3">
                @foreach($paymentAnalysis as $payment)
                    @php
                        $methodLabel = match($payment->payment_method) {
                            'cash' => 'Tunai',
                            'card' => 'Kartu',
                            'digital' => 'Digital',
                            default => ucfirst($payment->payment_method)
                        };
                        $percentage = $summary['total_revenue'] > 0 ? ($payment->total / $summary['total_revenue']) * 100 : 0;
                    @endphp
                    <div class="flex justify-between items-center">
                        <div>
                            <span class="text-gray-700">{{ $methodLabel }}</span>
                            <div class="text-xs text-gray-500">{{ $payment->count }} transaksi</div>
                        </div>
                        <div class="text-right">
                            <div class="font-medium">Rp {{ number_format($payment->total, 0, ',', '.') }}</div>
                            <div class="text-xs text-blue-600">{{ number_format($percentage, 1) }}%</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Daily Breakdown Chart -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Tren Harian</h3>
        @if($dailyData->count() > 0)
            <div class="overflow-x-auto">
                <div class="min-w-full">
                    <div class="space-y-2">
                        @foreach($dailyData as $day)
                            @php
                                $maxRevenue = $dailyData->max('revenue');
                                $revenuePercentage = $maxRevenue > 0 ? ($day->revenue / $maxRevenue) * 100 : 0;
                                $maxProfit = $dailyData->max('net_profit');
                                $profitPercentage = $maxProfit > 0 ? ($day->net_profit / $maxProfit) * 100 : 0;
                            @endphp
                            <div class="flex items-center space-x-4 py-2">
                                <div class="w-20 text-sm text-gray-600">
                                    {{ \Carbon\Carbon::parse($day->date)->format('d/m') }}
                                </div>
                                <div class="flex-1">
                                    <div class="grid grid-cols-2 gap-2">
                                        <!-- Revenue Bar -->
                                        <div>
                                            <div class="flex items-center justify-between mb-1">
                                                <span class="text-xs text-gray-500">Pendapatan</span>
                                                <span class="text-xs font-medium">Rp {{ number_format($day->revenue, 0, ',', '.') }}</span>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-2">
                                                <div class="bg-green-600 h-2 rounded-full transition-all duration-300" 
                                                     style="width: {{ $revenuePercentage }}%"></div>
                                            </div>
                                        </div>
                                        <!-- Profit Bar -->
                                        <div>
                                            <div class="flex items-center justify-between mb-1">
                                                <span class="text-xs text-gray-500">Profit</span>
                                                <span class="text-xs font-medium">Rp {{ number_format($day->net_profit, 0, ',', '.') }}</span>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-2">
                                                <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" 
                                                     style="width: {{ $profitPercentage }}%"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="w-16 text-right text-sm text-gray-600">
                                    {{ $day->transaction_count }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-8 text-gray-500">
                <i class="fas fa-chart-line text-4xl mb-4 text-gray-300"></i>
                <p>Belum ada data dalam periode ini</p>
            </div>
        @endif
    </div>

    <!-- Export & Actions -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Export Laporan</h3>
                <p class="text-gray-600">Download laporan keuangan dalam berbagai format</p>
            </div>
            <div class="flex space-x-3 mt-4 lg:mt-0">
                <button @click="exportToExcel()" 
                        class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg">
                    <i class="fas fa-file-excel mr-2"></i>Excel
                </button>
                <button @click="exportToPDF()" 
                        class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg">
                    <i class="fas fa-file-pdf mr-2"></i>PDF
                </button>
                <button @click="printReport()" 
                        class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                    <i class="fas fa-print mr-2"></i>Print
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function financialReport() {
    return {
        init() {
            console.log('Financial report initialized');
        },

        exportToExcel() {
            // Implementasi export Excel
            showToast('Fitur export Excel akan segera tersedia', 'info');
        },

        exportToPDF() {
            // Implementasi export PDF
            showToast('Fitur export PDF akan segera tersedia', 'info');
        },

        printReport() {
            window.print();
        }
    }
}
</script>
@endpush
@endsection