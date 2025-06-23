@extends('layouts.app')

@section('title', 'Laporan Keuangan - Takoyaki POS')

@section('content')
<div class="min-h-screen bg-gray-50" x-data="financialReport()">
    <!-- Header with improved mobile design -->
    <div class="bg-white shadow-sm sticky top-0 z-10">
        <div class="px-4 py-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <a href="{{ route('reports.index') }}" 
                       class="p-2 -ml-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-lg transition-colors">
                        <i class="fas fa-arrow-left text-lg"></i>
                    </a>
                    <div>
                        <h1 class="text-lg sm:text-xl font-bold text-gray-800">Laporan Keuangan</h1>
                        <p class="text-xs sm:text-sm text-gray-600 hidden sm:block">Analisis profit & loss dan kinerja keuangan</p>
                    </div>
                </div>
                
                <!-- Mobile menu toggle for date range -->
                <button @click="showDateFilter = !showDateFilter" 
                        class="p-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-lg sm:hidden">
                    <i class="fas fa-calendar-alt"></i>
                </button>
            </div>
        </div>
    </div>

    <div class="px-4 py-4 space-y-4">
        <!-- Mobile-optimized Date Range Selector -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100" 
             :class="{'block': showDateFilter, 'hidden sm:block': !showDateFilter}">
            <div class="p-4">
                <div class="flex items-center justify-between mb-3 sm:hidden">
                    <h3 class="font-medium text-gray-800">Filter Periode</h3>
                    <button @click="showDateFilter = false" class="text-gray-500">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <form method="GET" class="space-y-3">
                    <!-- Date inputs - stacked on mobile -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="space-y-1">
                            <label class="text-xs font-medium text-gray-700 uppercase tracking-wide">Dari Tanggal</label>
                            <input type="date" 
                                   name="start_date" 
                                   value="{{ $startDate->format('Y-m-d') }}"
                                   class="w-full px-3 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 text-sm">
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-medium text-gray-700 uppercase tracking-wide">Sampai Tanggal</label>
                            <input type="date" 
                                   name="end_date" 
                                   value="{{ $endDate->format('Y-m-d') }}"
                                   max="{{ today()->format('Y-m-d') }}"
                                   class="w-full px-3 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 text-sm">
                        </div>
                    </div>

                    <!-- Action buttons -->
                    <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2">
                        <button type="submit" 
                                class="flex-1 bg-red-500 hover:bg-red-600 text-white px-4 py-2.5 rounded-lg font-medium transition-colors">
                            <i class="fas fa-search mr-2"></i>Lihat Laporan
                        </button>
                    </div>

                    <!-- Quick filters - horizontal scroll on mobile -->
                    <div class="flex space-x-2 overflow-x-auto pb-2 -mx-1 px-1">
                        <a href="{{ route('reports.financial', [
                            'start_date' => now('Asia/Jakarta')->startOfMonth()->format('Y-m-d'), 
                            'end_date' => now('Asia/Jakarta')->endOfMonth()->format('Y-m-d')
                        ]) }}" 
                        class="flex-shrink-0 bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                            Bulan Ini ({{ now('Asia/Jakarta')->format('M Y') }})
                        </a>
                        <a href="{{ route('reports.financial', [
                            'start_date' => now('Asia/Jakarta')->subMonth()->startOfMonth()->format('Y-m-d'), 
                            'end_date' => now('Asia/Jakarta')->subMonth()->endOfMonth()->format('Y-m-d')
                        ]) }}" 
                        class="flex-shrink-0 bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                            Bulan Lalu ({{ now('Asia/Jakarta')->subMonth()->format('M Y') }})
                        </a>
                        <a href="{{ route('reports.financial', [
                            'start_date' => now('Asia/Jakarta')->startOfYear()->format('Y-m-d'), 
                            'end_date' => now('Asia/Jakarta')->endOfYear()->format('Y-m-d')
                        ]) }}" 
                        class="flex-shrink-0 bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                            Tahun Ini ({{ now('Asia/Jakarta')->format('Y') }})
                        </a>
                        <a href="{{ route('reports.financial', [
                            'start_date' => now('Asia/Jakarta')->subDays(6)->format('Y-m-d'), 
                            'end_date' => now('Asia/Jakarta')->format('Y-m-d')
                        ]) }}" 
                        class="flex-shrink-0 bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                            7 Hari Terakhir
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Financial Summary Cards - Responsive Grid -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            <!-- Total Revenue -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6">
                <div class="flex items-start justify-between mb-2">
                    <div class="flex-1 min-w-0">
                        <h3 class="text-xs sm:text-sm font-medium text-gray-600 truncate">Total Pendapatan</h3>
                    </div>
                    <div class="flex-shrink-0 ml-2">
                        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-dollar-sign text-green-600 text-sm"></i>
                        </div>
                    </div>
                </div>
                <div class="text-lg sm:text-2xl font-bold text-green-600 mb-1 truncate">
                    Rp {{ number_format($summary['total_revenue'], 0, ',', '.') }}
                </div>
                <div class="text-xs text-gray-500">
                    {{ $summary['transaction_count'] }} transaksi
                </div>
            </div>

            <!-- Total Cost -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6">
                <div class="flex items-start justify-between mb-2">
                    <div class="flex-1 min-w-0">
                        <h3 class="text-xs sm:text-sm font-medium text-gray-600 truncate">Total Biaya</h3>
                    </div>
                    <div class="flex-shrink-0 ml-2">
                        <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-money-bill text-red-600 text-sm"></i>
                        </div>
                    </div>
                </div>
                <div class="text-lg sm:text-2xl font-bold text-red-600 mb-1 truncate">
                    Rp {{ number_format($summary['total_cost'], 0, ',', '.') }}
                </div>
                <div class="text-xs text-gray-500">
                    Biaya produksi
                </div>
            </div>

            <!-- Gross Profit -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6">
                <div class="flex items-start justify-between mb-2">
                    <div class="flex-1 min-w-0">
                        <h3 class="text-xs sm:text-sm font-medium text-gray-600 truncate">Laba Kotor</h3>
                    </div>
                    <div class="flex-shrink-0 ml-2">
                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-chart-line text-blue-600 text-sm"></i>
                        </div>
                    </div>
                </div>
                <div class="text-lg sm:text-2xl font-bold text-blue-600 mb-1 truncate">
                    Rp {{ number_format($summary['gross_profit'], 0, ',', '.') }}
                </div>
                <div class="text-xs text-gray-500">
                    Sebelum pajak
                </div>
            </div>

            <!-- Net Profit -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6">
                <div class="flex items-start justify-between mb-2">
                    <div class="flex-1 min-w-0">
                        <h3 class="text-xs sm:text-sm font-medium text-gray-600 truncate">Laba Bersih</h3>
                    </div>
                    <div class="flex-shrink-0 ml-2">
                        <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-coins text-purple-600 text-sm"></i>
                        </div>
                    </div>
                </div>
                <div class="text-lg sm:text-2xl font-bold text-purple-600 mb-1 truncate">
                    Rp {{ number_format($summary['net_profit'], 0, ',', '.') }}
                </div>
                <div class="text-xs text-gray-500">
                    Margin: {{ number_format($summary['profit_margin'], 1) }}%
                </div>
            </div>
        </div>

        <!-- Detailed Analytics - Mobile Tabs -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <!-- Mobile Tabs -->
            <div class="flex border-b border-gray-100 overflow-x-auto sm:hidden">
                <button @click="activeTab = 'tax'" 
                        :class="activeTab === 'tax' ? 'border-red-500 text-red-600' : 'border-transparent text-gray-500'"
                        class="flex-shrink-0 px-4 py-3 border-b-2 text-sm font-medium transition-colors">
                    Pajak & Diskon
                </button>
                <button @click="activeTab = 'avg'" 
                        :class="activeTab === 'avg' ? 'border-red-500 text-red-600' : 'border-transparent text-gray-500'"
                        class="flex-shrink-0 px-4 py-3 border-b-2 text-sm font-medium transition-colors">
                    Rata-rata
                </button>
                <button @click="activeTab = 'payment'" 
                        :class="activeTab === 'payment' ? 'border-red-500 text-red-600' : 'border-transparent text-gray-500'"
                        class="flex-shrink-0 px-4 py-3 border-b-2 text-sm font-medium transition-colors">
                    Pembayaran
                </button>
            </div>

            <!-- Desktop Grid / Mobile Tabs Content -->
            <div class="sm:grid sm:grid-cols-3 sm:divide-x sm:divide-gray-100">
                <!-- Tax & Discount -->
                <div class="p-4 sm:p-6" :class="{'hidden sm:block': activeTab !== 'tax'}" x-show="activeTab === 'tax' || window.innerWidth >= 640">
                    <div class="flex items-center space-x-2 mb-4">
                        <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-percentage text-orange-600 text-sm"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800">Pajak & Diskon</h3>
                    </div>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center py-2 border-b border-gray-50">
                            <span class="text-gray-600 text-sm">Total Pajak:</span>
                            <span class="font-semibold text-orange-600 text-sm">Rp {{ number_format($summary['total_tax'], 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-50">
                            <span class="text-gray-600 text-sm">Total Diskon:</span>
                            <span class="font-semibold text-red-600 text-sm">Rp {{ number_format($summary['total_discount'], 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-50">
                            <span class="text-gray-600 text-sm">Transaksi Diskon:</span>
                            <span class="font-semibold text-gray-800 text-sm">{{ $taxDiscountAnalysis['transactions_with_discount'] ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2">
                            <span class="text-gray-600 text-sm">Transaksi Pajak:</span>
                            <span class="font-semibold text-gray-800 text-sm">{{ $taxDiscountAnalysis['transactions_with_tax'] ?? 0 }}</span>
                        </div>
                    </div>
                </div>

                <!-- Average Metrics -->
                <div class="p-4 sm:p-6" :class="{'hidden sm:block': activeTab !== 'avg'}" x-show="activeTab === 'avg' || window.innerWidth >= 640">
                    <div class="flex items-center space-x-2 mb-4">
                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-calculator text-blue-600 text-sm"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800">Rata-rata</h3>
                    </div>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center py-2 border-b border-gray-50">
                            <span class="text-gray-600 text-sm">Per Transaksi:</span>
                            <span class="font-semibold text-blue-600 text-sm">Rp {{ number_format($summary['avg_transaction'], 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-50">
                            <span class="text-gray-600 text-sm">Per Hari:</span>
                            <span class="font-semibold text-green-600 text-sm">
                                Rp {{ number_format($summary['total_revenue'] / max(1, $startDate->diffInDays($endDate) + 1), 0, ',', '.') }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center py-2">
                            <span class="text-gray-600 text-sm">Diskon (%):</span>
                            <span class="font-semibold text-red-600 text-sm">{{ number_format($taxDiscountAnalysis['avg_discount_percentage'] ?? 0, 1) }}%</span>
                        </div>
                    </div>
                </div>

                <!-- Payment Methods -->
                <div class="p-4 sm:p-6" :class="{'hidden sm:block': activeTab !== 'payment'}" x-show="activeTab === 'payment' || window.innerWidth >= 640">
                    <div class="flex items-center space-x-2 mb-4">
                        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-credit-card text-green-600 text-sm"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800">Metode Pembayaran</h3>
                    </div>
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
                            <div class="flex justify-between items-center py-2 border-b border-gray-50 last:border-0">
                                <div class="flex-1 min-w-0">
                                    <div class="font-medium text-gray-800 text-sm">{{ $methodLabel }}</div>
                                    <div class="text-xs text-gray-500">{{ $payment->count }} transaksi</div>
                                </div>
                                <div class="text-right ml-3">
                                    <div class="font-semibold text-gray-800 text-sm">Rp {{ number_format($payment->total, 0, ',', '.') }}</div>
                                    <div class="text-xs text-blue-600">{{ number_format($percentage, 1) }}%</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Daily Trend Chart - Mobile Optimized -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-chart-area text-indigo-600 text-sm"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800">Tren Harian</h3>
                </div>
                
                <!-- Toggle for mobile view -->
                <button @click="showDailyDetails = !showDailyDetails" 
                        class="sm:hidden p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg">
                    <i class="fas" :class="showDailyDetails ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                </button>
            </div>

            @if($dailyData->count() > 0)
                <div class="space-y-2" :class="{'hidden sm:block': !showDailyDetails}">
                    @foreach($dailyData as $day)
                        @php
                            $maxRevenue = $dailyData->max('revenue');
                            $revenuePercentage = $maxRevenue > 0 ? ($day->revenue / $maxRevenue) * 100 : 0;
                            $maxProfit = $dailyData->max('net_profit');
                            $profitPercentage = $maxProfit > 0 ? ($day->net_profit / $maxProfit) * 100 : 0;
                        @endphp
                        <div class="p-3 border border-gray-100 rounded-lg hover:bg-gray-50 transition-colors">
                            <div class="flex items-center justify-between mb-2">
                                <div class="font-medium text-gray-800 text-sm">
                                    {{ \Carbon\Carbon::parse($day->date)->format('d M Y') }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $day->transaction_count }} transaksi
                                </div>
                            </div>
                            
                            <div class="space-y-2">
                                <!-- Revenue -->
                                <div>
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-xs text-gray-500">Pendapatan</span>
                                        <span class="text-xs font-medium text-green-600">
                                            Rp {{ number_format($day->revenue, 0, ',', '.') }}
                                        </span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-green-500 h-2 rounded-full transition-all duration-500" 
                                             style="width: {{ $revenuePercentage }}%"></div>
                                    </div>
                                </div>
                                
                                <!-- Profit -->
                                <div>
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-xs text-gray-500">Laba Bersih</span>
                                        <span class="text-xs font-medium text-blue-600">
                                            Rp {{ number_format($day->net_profit, 0, ',', '.') }}
                                        </span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-blue-500 h-2 rounded-full transition-all duration-500" 
                                             style="width: {{ $profitPercentage }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12 text-gray-500">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-chart-line text-2xl text-gray-300"></i>
                    </div>
                    <p class="text-sm">Belum ada data dalam periode ini</p>
                </div>
            @endif
        </div>

        <!-- Export Actions - Mobile Optimized -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6">
            <div class="flex flex-col space-y-4">
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-download text-gray-600 text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">Export Laporan</h3>
                        <p class="text-sm text-gray-600">Download laporan dalam berbagai format</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <button @click="exportToExcel()" 
                            class="flex items-center justify-center space-x-2 bg-green-500 hover:bg-green-600 text-white px-4 py-3 rounded-lg font-medium transition-colors">
                        <i class="fas fa-file-excel"></i>
                        <span>Excel</span>
                    </button>
                    <button @click="exportToPDF()" 
                            class="flex items-center justify-center space-x-2 bg-red-500 hover:bg-red-600 text-white px-4 py-3 rounded-lg font-medium transition-colors">
                        <i class="fas fa-file-pdf"></i>
                        <span>PDF</span>
                    </button>
                    <button @click="printReport()" 
                            class="flex items-center justify-center space-x-2 bg-blue-500 hover:bg-blue-600 text-white px-4 py-3 rounded-lg font-medium transition-colors">
                        <i class="fas fa-print"></i>
                        <span>Print</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function financialReport() {
    return {
        showDateFilter: false,
        activeTab: 'tax',
        showDailyDetails: false,

        init() {
            console.log('Financial report initialized');
            
            // Set default active tab on mobile
            if (window.innerWidth < 640) {
                this.activeTab = 'tax';
            }
            
            // Handle window resize
            window.addEventListener('resize', () => {
                if (window.innerWidth >= 640) {
                    this.showDateFilter = true;
                    this.showDailyDetails = true;
                } else {
                    this.showDateFilter = false;
                }
            });
        },

        exportToExcel() {
            showToast('Fitur export Excel akan segera tersedia', 'info');
        },

        exportToPDF() {
            showToast('Fitur export PDF akan segera tersedia', 'info');
        },

        printReport() {
            window.print();
        }
    }
}

// Toast notification function
function showToast(message, type = 'info') {
    // Implementation for toast notifications
    console.log(`${type}: ${message}`);
}
</script>
@endpush

@push('styles')
<style>
@media print {
    .no-print {
        display: none !important;
    }
    
    body {
        background: white !important;
    }
}

/* Custom scrollbar for horizontal scroll */
.overflow-x-auto::-webkit-scrollbar {
    height: 4px;
}

.overflow-x-auto::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 2px;
}

.overflow-x-auto::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 2px;
}

.overflow-x-auto::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>
@endpush
@endsection