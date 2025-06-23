@extends('layouts.app')

@section('title', 'Jam Tersibuk - Takoyaki POS')

@section('content')
<div class="min-h-screen bg-gray-50 pb-6" x-data="busiestHoursReport()">
    <!-- Mobile Header -->
    <div class="bg-white shadow-sm sticky top-0 z-10">
        <div class="px-4 py-3">
            <div class="flex items-center">
                <a href="{{ route('reports.index') }}" 
                   class="mr-3 p-2 -ml-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-lg">
                    <i class="fas fa-arrow-left text-lg"></i>
                </a>
                <div class="flex-1">
                    <h1 class="text-lg lg:text-2xl font-bold text-gray-800">Jam Tersibuk</h1>
                    <p class="text-sm text-gray-600 hidden sm:block">Pola aktivitas pelanggan berdasarkan waktu</p>
                </div>
            </div>
        </div>
    </div>

    <div class="px-4 space-y-4">
        <!-- Mobile Date Range Selector -->
        <div class="bg-white rounded-lg shadow-sm p-4 mt-4">
            <form method="GET" class="space-y-3">
                <!-- Period Label -->
                <div class="text-sm font-medium text-gray-700 mb-3">Periode Analisis</div>
                
                <!-- Date Inputs -->
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs text-gray-500 mb-1 block">Dari</label>
                        <input type="date" 
                               name="start_date" 
                               value="{{ $startDate->format('Y-m-d') }}"
                               class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 mb-1 block">Sampai</label>
                        <input type="date" 
                               name="end_date" 
                               value="{{ $endDate->format('Y-m-d') }}"
                               max="{{ today()->format('Y-m-d') }}"
                               class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-2">
                    <button type="submit" 
                            class="flex-1 bg-red-500 hover:bg-red-600 text-white px-4 py-2.5 rounded-lg text-sm font-medium">
                        <i class="fas fa-search mr-2"></i>Analisis
                    </button>
                    
                    <!-- Quick Period Buttons -->
                    <div class="flex gap-2">
                        <a href="{{ route('reports.busiest-hours', [
                            'start_date' => now('Asia/Jakarta')->subDays(6)->format('Y-m-d'), 
                            'end_date' => now('Asia/Jakarta')->format('Y-m-d')
                        ]) }}" 
                        class="flex-1 sm:flex-none bg-gray-500 hover:bg-gray-600 text-white px-3 py-2.5 rounded-lg text-xs font-medium text-center">
                            7 Hari ({{ now('Asia/Jakarta')->subDays(6)->format('d/m') }}-{{ now('Asia/Jakarta')->format('d/m') }})
                        </a>
                        <a href="{{ route('reports.busiest-hours', [
                            'start_date' => now('Asia/Jakarta')->startOfMonth()->format('Y-m-d'), 
                            'end_date' => now('Asia/Jakarta')->format('Y-m-d')
                        ]) }}" 
                        class="flex-1 sm:flex-none bg-gray-500 hover:bg-gray-600 text-white px-3 py-2.5 rounded-lg text-xs font-medium text-center">
                            Bulan Ini
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Top 3 Busiest Hours - Mobile Optimized -->
        <div class="space-y-3 lg:grid lg:grid-cols-3 lg:gap-4 lg:space-y-0">
            @foreach($busiestHours->take(3) as $index => $hour)
                <div class="bg-white rounded-lg shadow-sm p-4 {{ $index === 0 ? 'ring-2 ring-yellow-400 bg-yellow-50' : '' }}">
                    <div class="flex items-center justify-between">
                        <!-- Medal and Time -->
                        <div class="flex items-center space-x-3">
                            <div class="text-2xl lg:text-3xl">
                                @if($index === 0)
                                    🥇
                                @elseif($index === 1)
                                    🥈
                                @else
                                    🥉
                                @endif
                            </div>
                            <div>
                                <h3 class="text-lg lg:text-xl font-bold text-gray-800">
                                    {{ sprintf('%02d:00-%02d:00', $hour->hour, $hour->hour + 1) }}
                                </h3>
                                <p class="text-xs text-gray-500">
                                    @if($index === 0) Jam Tersibuk @elseif($index === 1) Peringkat 2 @else Peringkat 3 @endif
                                </p>
                            </div>
                        </div>
                        
                        <!-- Stats -->
                        <div class="text-right">
                            <div class="text-lg font-bold text-blue-600">{{ $hour->transaction_count }}</div>
                            <div class="text-xs text-gray-500">transaksi</div>
                        </div>
                    </div>
                    
                    <!-- Revenue Row -->
                    <div class="mt-3 pt-3 border-t border-gray-100 flex justify-between items-center">
                        <div>
                            <div class="text-sm font-semibold text-green-600">Rp {{ number_format($hour->revenue, 0, ',', '.') }}</div>
                            <div class="text-xs text-gray-500">Pendapatan</div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-medium text-purple-600">Rp {{ number_format($hour->avg_transaction, 0, ',', '.') }}</div>
                            <div class="text-xs text-gray-500">Rata-rata</div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Mobile Hourly Analysis -->
        <div class="bg-white rounded-lg shadow-sm p-4">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-800">Analisis 24 Jam</h2>
                <button @click="showDetailedView = !showDetailedView" 
                        class="text-sm text-red-600 hover:text-red-700">
                    <span x-text="showDetailedView ? 'Sembunyikan Detail' : 'Lihat Detail'"></span>
                </button>
            </div>
            
            @if($busiestHours->count() > 0)
                @php
                    $maxTransactions = $busiestHours->max('transaction_count');
                    $maxRevenue = $busiestHours->max('revenue');
                @endphp
                
                <div class="space-y-2">
                    @for($h = 0; $h < 24; $h++)
                        @php
                            $hourData = $busiestHours->where('hour', $h)->first();
                            $transactions = $hourData ? $hourData->transaction_count : 0;
                            $revenue = $hourData ? $hourData->revenue : 0;
                            $profit = $hourData ? $hourData->profit : 0;
                            
                            $transactionPercentage = $maxTransactions > 0 ? ($transactions / $maxTransactions) * 100 : 0;
                            
                            // Busy level colors
                            $busyLevel = '';
                            $barColor = 'bg-gray-300';
                            $textColor = 'text-gray-600';
                            if ($transactionPercentage >= 80) {
                                $busyLevel = 'Sangat Ramai';
                                $barColor = 'bg-red-500';
                                $textColor = 'text-red-600';
                            } elseif ($transactionPercentage >= 60) {
                                $busyLevel = 'Ramai';
                                $barColor = 'bg-orange-500';
                                $textColor = 'text-orange-600';
                            } elseif ($transactionPercentage >= 40) {
                                $busyLevel = 'Sedang';
                                $barColor = 'bg-yellow-500';
                                $textColor = 'text-yellow-600';
                            } elseif ($transactionPercentage >= 20) {
                                $busyLevel = 'Sepi';
                                $barColor = 'bg-blue-500';
                                $textColor = 'text-blue-600';
                            } else {
                                $busyLevel = 'Sangat Sepi';
                                $barColor = 'bg-gray-300';
                                $textColor = 'text-gray-500';
                            }
                        @endphp
                        
                        <div class="py-2 hover:bg-gray-50 rounded-lg px-2 transition-colors">
                            <!-- Mobile Layout -->
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center space-x-3">
                                    <div class="text-sm font-medium text-gray-700 w-12">
                                        {{ sprintf('%02d:00', $h) }}
                                    </div>
                                    <div class="text-xs {{ $textColor }} font-medium">
                                        {{ $busyLevel }}
                                    </div>
                                </div>
                                <div class="flex items-center space-x-3 text-right">
                                    <div>
                                        <div class="text-sm font-medium text-blue-600">{{ $transactions }}</div>
                                        <div class="text-xs text-gray-400">transaksi</div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Progress Bar -->
                            <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
                                <div class="{{ $barColor }} h-2 rounded-full transition-all duration-300" 
                                     style="width: {{ $transactionPercentage }}%"></div>
                            </div>
                            
                            <!-- Detailed Info (Expandable) -->
                            <div x-show="showDetailedView" 
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 max-h-0"
                                 x-transition:enter-end="opacity-100 max-h-20"
                                 class="flex justify-between text-xs text-gray-600 pt-2 border-t border-gray-100">
                                <div>
                                    <div class="font-medium text-green-600">Rp {{ number_format($revenue, 0, ',', '.') }}</div>
                                    <div>Pendapatan</div>
                                </div>
                                <div class="text-right">
                                    <div class="font-medium text-purple-600">Rp {{ number_format($profit, 0, ',', '.') }}</div>
                                    <div>Profit</div>
                                </div>
                            </div>
                        </div>
                    @endfor
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-clock text-3xl mb-3 text-gray-300"></i>
                    <p class="text-sm">Belum ada data dalam periode ini</p>
                </div>
            @endif
        </div>

        <!-- Mobile Daily Pattern -->
        <div class="bg-white rounded-lg shadow-sm p-4">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Pola Mingguan</h2>
            @if($dailyPattern->count() > 0)
                @php
                    $maxDailyTransactions = $dailyPattern->max('transaction_count');
                    $days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                @endphp
                
                <!-- Mobile: Horizontal Scroll for Days -->
                <div class="flex space-x-3 overflow-x-auto pb-2 lg:grid lg:grid-cols-7 lg:gap-4 lg:space-x-0">
                    @foreach($dailyPattern as $day)
                        @php
                            $percentage = $maxDailyTransactions > 0 ? ($day->transaction_count / $maxDailyTransactions) * 100 : 0;
                            $dayName = $days[$day->day_number - 1] ?? 'Unknown';
                        @endphp
                        
                        <div class="flex-shrink-0 w-32 lg:w-auto bg-gray-50 border border-gray-200 rounded-lg p-3 text-center hover:bg-gray-100 transition-colors">
                            <h3 class="font-medium text-gray-800 mb-2 text-sm">{{ $dayName }}</h3>
                            
                            <!-- Progress Bar -->
                            <div class="mb-3">
                                <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
                                    <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" 
                                         style="width: {{ $percentage }}%"></div>
                                </div>
                            </div>
                            
                            <!-- Stats -->
                            <div class="space-y-2 text-xs">
                                <div>
                                    <div class="font-semibold text-blue-600">{{ $day->transaction_count }}</div>
                                    <div class="text-gray-500">transaksi</div>
                                </div>
                                <div>
                                    <div class="font-semibold text-green-600">{{ number_format($day->revenue / 1000, 0) }}K</div>
                                    <div class="text-gray-500">pendapatan</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-calendar-week text-3xl mb-3 text-gray-300"></i>
                    <p class="text-sm">Belum ada data pola mingguan</p>
                </div>
            @endif
        </div>

        <!-- Mobile Business Insights -->
        @if($busiestHours->count() > 0)
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h3 class="text-base font-semibold text-blue-800 mb-3 flex items-center">
                    <i class="fas fa-lightbulb mr-2"></i>Insights Bisnis
                </h3>
                
                <!-- Expandable Content -->
                <div x-data="{ expanded: false }">
                    <button @click="expanded = !expanded" 
                            class="w-full text-left text-sm text-blue-700 hover:text-blue-800 mb-3">
                        <span x-text="expanded ? 'Sembunyikan insights' : 'Lihat insights lengkap'"></span>
                        <i class="fas fa-chevron-down ml-1 transform transition-transform" 
                           :class="expanded ? 'rotate-180' : ''"></i>
                    </button>
                    
                    <!-- Key Stats (Always Visible) -->
                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <div class="bg-white rounded-lg p-3 text-center">
                            <div class="text-lg font-bold text-blue-600">{{ sprintf('%02d:00', $busiestHours->first()->hour) }}</div>
                            <div class="text-xs text-blue-700">Jam Tersibuk</div>
                        </div>
                        <div class="bg-white rounded-lg p-3 text-center">
                            <div class="text-lg font-bold text-green-600">{{ $busiestHours->where('transaction_count', '>', 0)->count() }}</div>
                            <div class="text-xs text-blue-700">Jam Aktif</div>
                        </div>
                    </div>
                    
                    <!-- Expanded Content -->
                    <div x-show="expanded" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 max-h-0"
                         x-transition:enter-end="opacity-100 max-h-96"
                         class="space-y-4 text-sm">
                        
                        <div>
                            <h4 class="font-medium text-blue-800 mb-2">Rekomendasi Operasional:</h4>
                            <ul class="list-disc list-inside space-y-1 text-blue-700">
                                @if($busiestHours->first()->hour >= 11 && $busiestHours->first()->hour <= 13)
                                    <li>Jam makan siang (11:00-14:00) adalah waktu tersibuk</li>
                                    <li>Pastikan stok produk cukup pada jam makan siang</li>
                                @elseif($busiestHours->first()->hour >= 17 && $busiestHours->first()->hour <= 19)
                                    <li>Jam makan malam (17:00-20:00) adalah waktu tersibuk</li>
                                    <li>Siapkan staff tambahan pada jam makan malam</li>
                                @endif
                                <li>Pertimbangkan promo khusus pada jam sepi</li>
                                <li>Optimalkan jadwal shift berdasarkan pola keramaian</li>
                            </ul>
                        </div>
                        
                        <div>
                            <h4 class="font-medium text-blue-800 mb-2">Analisis Performa:</h4>
                            <ul class="list-disc list-inside space-y-1 text-blue-700">
                                <li>Total transaksi terbanyak: {{ $busiestHours->first()->transaction_count }} transaksi</li>
                                <li>Rata-rata transaksi per jam aktif: {{ number_format($busiestHours->where('transaction_count', '>', 0)->avg('transaction_count'), 1) }}</li>
                                <li>Potensi optimalisasi: {{ 24 - $busiestHours->where('transaction_count', '>', 0)->count() }} jam sepi</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function busiestHoursReport() {
    return {
        showDetailedView: false,
        
        init() {
            console.log('Busiest hours report initialized');
            
            // Auto-show detailed view on larger screens
            if (window.innerWidth >= 1024) {
                this.showDetailedView = true;
            }
        }
    }
}
</script>
@endpush

@push('styles')
<style>
/* Mobile optimizations */
@media (max-width: 768px) {
    /* Prevent horizontal scroll */
    body {
        overflow-x: hidden;
    }
    
    /* Better touch targets */
    button, .touch-target {
        min-height: 44px;
        min-width: 44px;
    }
    
    /* Improve readability on small screens */
    .text-xs {
        font-size: 0.75rem;
        line-height: 1rem;
    }
    
    /* Better spacing for mobile */
    .space-y-4 > * + * {
        margin-top: 1rem;
    }
}

/* Smooth transitions */
.transition-colors {
    transition-property: background-color, border-color, color, fill, stroke;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 150ms;
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

/* Progress bar animations */
@keyframes progressFill {
    from { width: 0%; }
    to { width: var(--progress-width); }
}

.progress-animate {
    animation: progressFill 0.8s ease-out;
}
</style>
@endpush
@endsection