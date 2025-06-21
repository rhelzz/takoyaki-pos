@extends('layouts.app')

@section('title', 'Jam Tersibuk - Takoyaki POS')

@section('content')
<div class="p-4 max-w-7xl mx-auto" x-data="busiestHoursReport()">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center mb-4">
            <a href="{{ route('reports.index') }}" 
               class="mr-3 p-2 text-gray-600 hover:text-gray-800">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Analisis Jam Tersibuk</h1>
                <p class="text-gray-600">Pola aktivitas pelanggan berdasarkan waktu</p>
            </div>
        </div>

        <!-- Date Range Selector -->
        <div class="bg-white rounded-lg shadow p-4">
            <form method="GET" class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-3 lg:space-y-0">
                <div class="flex flex-col lg:flex-row lg:items-center space-y-3 lg:space-y-0 lg:space-x-3">
                    <label class="text-sm font-medium text-gray-700">Periode Analisis:</label>
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
                        <i class="fas fa-search mr-2"></i>Analisis
                    </button>
                </div>

                <div class="flex space-x-2">
                    <a href="{{ route('reports.busiest-hours', ['start_date' => now()->subDays(7)->format('Y-m-d'), 'end_date' => now()->format('Y-m-d')]) }}" 
                       class="bg-gray-500 hover:bg-gray-600 text-white px-3 py-2 rounded-lg text-sm">
                        7 Hari
                    </a>
                    <a href="{{ route('reports.busiest-hours', ['start_date' => now()->startOfMonth()->format('Y-m-d'), 'end_date' => now()->endOfMonth()->format('Y-m-d')]) }}" 
                       class="bg-gray-500 hover:bg-gray-600 text-white px-3 py-2 rounded-lg text-sm">
                        Bulan Ini
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Top 3 Busiest Hours -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
        @foreach($busiestHours->take(3) as $index => $hour)
            <div class="bg-white rounded-lg shadow p-6 text-center {{ $index === 0 ? 'ring-2 ring-yellow-400' : '' }}">
                <div class="text-4xl mb-2">
                    @if($index === 0)
                        🥇
                    @elseif($index === 1)
                        🥈
                    @else
                        🥉
                    @endif
                </div>
                <h3 class="text-2xl font-bold text-gray-800 mb-1">
                    {{ sprintf('%02d:00 - %02d:00', $hour->hour, $hour->hour + 1) }}
                </h3>
                <div class="space-y-2">
                    <div>
                        <p class="text-lg font-semibold text-blue-600">{{ $hour->transaction_count }}</p>
                        <p class="text-sm text-gray-600">Transaksi</p>
                    </div>
                    <div>
                        <p class="text-lg font-semibold text-green-600">Rp {{ number_format($hour->revenue, 0, ',', '.') }}</p>
                        <p class="text-sm text-gray-600">Pendapatan</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-purple-600">Rp {{ number_format($hour->avg_transaction, 0, ',', '.') }}</p>
                        <p class="text-sm text-gray-600">Rata-rata per transaksi</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Hourly Analysis Chart -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Analisis Per Jam (24 Jam)</h2>
        @if($busiestHours->count() > 0)
            <div class="space-y-3">
                @php
                    $maxTransactions = $busiestHours->max('transaction_count');
                    $maxRevenue = $busiestHours->max('revenue');
                @endphp
                
                @for($h = 0; $h < 24; $h++)
                    @php
                        $hourData = $busiestHours->where('hour', $h)->first();
                        $transactions = $hourData ? $hourData->transaction_count : 0;
                        $revenue = $hourData ? $hourData->revenue : 0;
                        $profit = $hourData ? $hourData->profit : 0;
                        
                        $transactionPercentage = $maxTransactions > 0 ? ($transactions / $maxTransactions) * 100 : 0;
                        $revenuePercentage = $maxRevenue > 0 ? ($revenue / $maxRevenue) * 100 : 0;
                        
                        // Tentukan level keramaian
                        $busyLevel = '';
                        $barColor = 'bg-gray-300';
                        if ($transactionPercentage >= 80) {
                            $busyLevel = 'Sangat Ramai';
                            $barColor = 'bg-red-500';
                        } elseif ($transactionPercentage >= 60) {
                            $busyLevel = 'Ramai';
                            $barColor = 'bg-orange-500';
                        } elseif ($transactionPercentage >= 40) {
                            $busyLevel = 'Sedang';
                            $barColor = 'bg-yellow-500';
                        } elseif ($transactionPercentage >= 20) {
                            $busyLevel = 'Sepi';
                            $barColor = 'bg-blue-500';
                        } else {
                            $busyLevel = 'Sangat Sepi';
                            $barColor = 'bg-gray-300';
                        }
                    @endphp
                    
                    <div class="flex items-center space-x-4 py-2 hover:bg-gray-50 rounded-lg px-2">
                        <div class="w-16 text-sm font-medium text-gray-700">
                            {{ sprintf('%02d:00', $h) }}
                        </div>
                        
                        <div class="flex-1">
                            <div class="flex items-center space-x-2 mb-1">
                                <div class="w-32 bg-gray-200 rounded-full h-4">
                                    <div class="{{ $barColor }} h-4 rounded-full transition-all duration-300" 
                                         style="width: {{ $transactionPercentage }}%"></div>
                                </div>
                                <span class="text-xs font-medium text-gray-600 w-20">{{ $busyLevel }}</span>
                            </div>
                        </div>
                        
                        <div class="text-right space-x-4 flex">
                            <div class="w-16">
                                <p class="text-sm font-medium text-blue-600">{{ $transactions }}</p>
                                <p class="text-xs text-gray-500">transaksi</p>
                            </div>
                            <div class="w-20">
                                <p class="text-sm font-medium text-green-600">{{ number_format($revenue, 0, ',', '.') }}</p>
                                <p class="text-xs text-gray-500">pendapatan</p>
                            </div>
                            <div class="w-20">
                                <p class="text-sm font-medium text-purple-600">{{ number_format($profit, 0, ',', '.') }}</p>
                                <p class="text-xs text-gray-500">profit</p>
                            </div>
                        </div>
                    </div>
                @endfor
            </div>
        @else
            <div class="text-center py-8 text-gray-500">
                <i class="fas fa-clock text-4xl mb-4 text-gray-300"></i>
                <p>Belum ada data dalam periode ini</p>
            </div>
        @endif
    </div>

    <!-- Daily Pattern -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Pola Mingguan</h2>
        @if($dailyPattern->count() > 0)
            <div class="grid grid-cols-1 lg:grid-cols-7 gap-4">
                @php
                    $maxDailyTransactions = $dailyPattern->max('transaction_count');
                    $days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                @endphp
                
                @foreach($dailyPattern as $day)
                    @php
                        $percentage = $maxDailyTransactions > 0 ? ($day->transaction_count / $maxDailyTransactions) * 100 : 0;
                        $dayName = $days[$day->day_number - 1] ?? 'Unknown';
                    @endphp
                    
                    <div class="text-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                        <h3 class="font-medium text-gray-800 mb-2">{{ $dayName }}</h3>
                        
                        <div class="mb-3">
                            <div class="w-full bg-gray-200 rounded-full h-3 mb-2">
                                <div class="bg-blue-600 h-3 rounded-full transition-all duration-300" 
                                     style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                        
                        <div class="space-y-1 text-sm">
                            <div>
                                <p class="font-semibold text-blue-600">{{ $day->transaction_count }}</p>
                                <p class="text-gray-500">transaksi</p>
                            </div>
                            <div>
                                <p class="font-semibold text-green-600">{{ number_format($day->revenue, 0, ',', '.') }}</p>
                                <p class="text-gray-500">pendapatan</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8 text-gray-500">
                <i class="fas fa-calendar-week text-4xl mb-4 text-gray-300"></i>
                <p>Belum ada data pola mingguan</p>
            </div>
        @endif
    </div>

    <!-- Business Insights -->
    @if($busiestHours->count() > 0)
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-blue-800 mb-3">
                <i class="fas fa-lightbulb mr-2"></i>Insights Bisnis
            </h3>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 text-sm">
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
                        <li>Pertimbangkan promo khusus pada jam sepi untuk meningkatkan penjualan</li>
                        <li>Optimalkan jadwal shift berdasarkan pola keramaian</li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="font-medium text-blue-800 mb-2">Analisis Performa:</h4>
                    <ul class="list-disc list-inside space-y-1 text-blue-700">
                        <li>Jam tersibuk: {{ sprintf('%02d:00', $busiestHours->first()->hour) }} ({{ $busiestHours->first()->transaction_count }} transaksi)</li>
                        <li>Total jam operasional aktif: {{ $busiestHours->where('transaction_count', '>', 0)->count() }} jam</li>
                        <li>Rata-rata transaksi per jam aktif: {{ number_format($busiestHours->where('transaction_count', '>', 0)->avg('transaction_count'), 1) }}</li>
                        <li>Potensi optimalisasi pada {{ 24 - $busiestHours->where('transaction_count', '>', 0)->count() }} jam sepi</li>
                    </ul>
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
function busiestHoursReport() {
    return {
        init() {
            console.log('Busiest hours report initialized');
        }
    }
}
</script>
@endpush
@endsection