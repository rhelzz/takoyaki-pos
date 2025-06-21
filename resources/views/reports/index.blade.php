@extends('layouts.app')

@section('title', 'Laporan - Takoyaki POS')

@section('content')
<div class="p-4 max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Laporan & Analisis</h1>
        <p class="text-gray-600">Pantau performa bisnis takoyaki Anda</p>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Today Stats -->
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-calendar-day text-blue-600"></i>
                </div>
                <div>
                    <p class="text-lg font-bold text-gray-800">{{ $todayStats['transactions'] ?? 0 }}</p>
                    <p class="text-xs text-gray-600">Transaksi Hari Ini</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-money-bill-wave text-green-600"></i>
                </div>
                <div>
                    <p class="text-sm font-bold text-gray-800">Rp {{ number_format($todayStats['revenue'] ?? 0, 0, ',', '.') }}</p>
                    <p class="text-xs text-gray-600">Pendapatan Hari Ini</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-chart-line text-yellow-600"></i>
                </div>
                <div>
                    <p class="text-sm font-bold text-gray-800">Rp {{ number_format($todayStats['profit'] ?? 0, 0, ',', '.') }}</p>
                    <p class="text-xs text-gray-600">Profit Hari Ini</p>
                </div>
            </div>
        </div>

        <!-- Month Stats -->
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-calendar-alt text-purple-600"></i>
                </div>
                <div>
                    <p class="text-lg font-bold text-gray-800">{{ $monthStats['transactions'] ?? 0 }}</p>
                    <p class="text-xs text-gray-600">Transaksi Bulan Ini</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Categories -->
    <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-4 gap-6">
        <!-- Daily Report -->
        <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-calendar-day text-blue-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">Laporan Harian</h3>
                        <p class="text-sm text-gray-600">Detail transaksi per hari</p>
                    </div>
                </div>
                
                <div class="mb-4">
                    <div class="text-2xl font-bold text-blue-600 mb-1">
                        Rp {{ number_format($todayStats['revenue'] ?? 0, 0, ',', '.') }}
                    </div>
                    <div class="text-sm text-gray-500">Pendapatan hari ini</div>
                </div>

                <a href="{{ route('reports.daily') }}" 
                   class="w-full bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-lg text-center block transition-colors">
                    <i class="fas fa-chart-bar mr-2"></i>Lihat Laporan
                </a>
            </div>
        </div>

        <!-- Busiest Hours -->
        <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-clock text-green-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">Jam Tersibuk</h3>
                        <p class="text-sm text-gray-600">Analisis waktu ramai</p>
                    </div>
                </div>
                
                <div class="mb-4">
                    <div class="text-2xl font-bold text-green-600 mb-1">
                        {{ $todayStats['transactions'] ?? 0 }}
                    </div>
                    <div class="text-sm text-gray-500">Transaksi hari ini</div>
                </div>

                <a href="{{ route('reports.busiest-hours') }}" 
                   class="w-full bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded-lg text-center block transition-colors">
                    <i class="fas fa-chart-line mr-2"></i>Lihat Analisis
                </a>
            </div>
        </div>

        <!-- Best Selling -->
        <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-trophy text-yellow-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">Produk Terlaris</h3>
                        <p class="text-sm text-gray-600">Analisis penjualan produk</p>
                    </div>
                </div>
                
                <div class="mb-4">
                    <div class="text-2xl font-bold text-yellow-600 mb-1">
                        Top 10
                    </div>
                    <div class="text-sm text-gray-500">Produk terlaris</div>
                </div>

                <a href="{{ route('reports.best-selling') }}" 
                   class="w-full bg-yellow-500 hover:bg-yellow-600 text-white py-2 px-4 rounded-lg text-center block transition-colors">
                    <i class="fas fa-star mr-2"></i>Lihat Ranking
                </a>
            </div>
        </div>

        <!-- Financial Report -->
        <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-chart-pie text-purple-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">Laporan Keuangan</h3>
                        <p class="text-sm text-gray-600">Analisis profit & loss</p>
                    </div>
                </div>
                
                <div class="mb-4">
                    <div class="text-2xl font-bold text-purple-600 mb-1">
                        Rp {{ number_format($monthStats['revenue'] ?? 0, 0, ',', '.') }}
                    </div>
                    <div class="text-sm text-gray-500">Pendapatan bulan ini</div>
                </div>

                <a href="{{ route('reports.financial') }}" 
                   class="w-full bg-purple-500 hover:bg-purple-600 text-white py-2 px-4 rounded-lg text-center block transition-colors">
                    <i class="fas fa-calculator mr-2"></i>Lihat Keuangan
                </a>
            </div>
        </div>
    </div>

    <!-- Quick Access -->
    <div class="mt-8 bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Akses Cepat</h2>
        
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <a href="{{ route('reports.daily', ['date' => now()->format('Y-m-d')]) }}" 
               class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 text-center transition-colors">
                <i class="fas fa-calendar-day text-blue-500 text-2xl mb-2"></i>
                <p class="text-sm font-medium text-gray-800">Laporan Hari Ini</p>
            </a>

            <a href="{{ route('reports.daily', ['date' => now()->subDay()->format('Y-m-d')]) }}" 
               class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 text-center transition-colors">
                <i class="fas fa-calendar-minus text-gray-500 text-2xl mb-2"></i>
                <p class="text-sm font-medium text-gray-800">Laporan Kemarin</p>
            </a>

            <a href="{{ route('reports.best-selling', ['period' => 'month']) }}" 
               class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 text-center transition-colors">
                <i class="fas fa-chart-bar text-green-500 text-2xl mb-2"></i>
                <p class="text-sm font-medium text-gray-800">Terlaris Bulan Ini</p>
            </a>

            <a href="{{ route('reports.financial', ['start_date' => now()->startOfMonth()->format('Y-m-d'), 'end_date' => now()->endOfMonth()->format('Y-m-d')]) }}" 
               class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 text-center transition-colors">
                <i class="fas fa-money-check-alt text-purple-500 text-2xl mb-2"></i>
                <p class="text-sm font-medium text-gray-800">Keuangan Bulan Ini</p>
            </a>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="mt-8 bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-800">Aktivitas Terbaru</h2>
            <a href="{{ route('transactions.index') }}" class="text-blue-500 hover:text-blue-600 text-sm">
                Lihat Semua →
            </a>
        </div>
        
        <div class="space-y-3">
            @if(isset($recentTransactions) && $recentTransactions->count() > 0)
                @foreach($recentTransactions->take(5) as $transaction)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-receipt text-green-600 text-sm"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-800">{{ $transaction->transaction_code }}</p>
                                <p class="text-xs text-gray-500">{{ $transaction->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-semibold text-gray-800">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</p>
                            <p class="text-xs text-gray-500">{{ ucfirst($transaction->payment_method) }}</p>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-chart-line text-4xl mb-4 text-gray-300"></i>
                    <p>Belum ada transaksi hari ini</p>
                    <p class="text-sm mt-1">Mulai berjualan untuk melihat aktivitas</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection