@extends('layouts.app')

@section('title', 'Laporan Harian - Takoyaki POS')

@section('content')
<div class="p-4 max-w-7xl mx-auto" x-data="dailyReport()">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center mb-4">
            <a href="{{ route('reports.index') }}" 
               class="mr-3 p-2 text-gray-600 hover:text-gray-800">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Laporan Harian</h1>
                <p class="text-gray-600">
                    {{ \Carbon\Carbon::parse($date)->format('l, d F Y') }}
                    @if($selectedUser)
                        - Kasir: <span class="font-semibold text-red-600">{{ $selectedUser->name }}</span>
                    @endif
                </p>
            </div>
        </div>

        <!-- Date and User Selector -->
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between bg-white rounded-lg shadow p-4">
            <form method="GET" class="flex flex-col lg:flex-row lg:items-center space-y-3 lg:space-y-0 lg:space-x-3">
                <div class="flex flex-col lg:flex-row lg:items-center space-y-3 lg:space-y-0 lg:space-x-3">
                    <!-- Date Filter -->
                    <div class="flex items-center space-x-2">
                        <label class="text-sm font-medium text-gray-700">Pilih Tanggal:</label>
                        <input type="date" 
                               name="date" 
                               value="{{ $date->format('Y-m-d') }}"
                               max="{{ today()->format('Y-m-d') }}"
                               class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                    </div>

                    <!-- User Filter (hanya untuk Admin/Manager) -->
                    @if(auth()->user()->role !== 'cashier')
                        <div class="flex items-center space-x-2">
                            <label class="text-sm font-medium text-gray-700">Kasir:</label>
                            <select name="user_id" 
                                    class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 min-w-[150px]">
                                <option value="">Semua Kasir</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ $userId == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @else
                        <!-- Hidden input untuk kasir (auto-fill dengan user_id kasir yang login) -->
                        <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
                        <div class="flex items-center space-x-2 bg-red-50 px-3 py-2 rounded-lg border border-red-200">
                            <i class="fas fa-user-circle text-red-600"></i>
                            <span class="text-sm font-medium text-gray-700">{{ auth()->user()->name }}</span>
                        </div>
                    @endif

                    <button type="submit" 
                            class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg">
                        <i class="fas fa-search mr-2"></i>Lihat
                    </button>
                </div>
            </form>

            <div class="flex space-x-2 mt-3 lg:mt-0">
                <a href="{{ route('reports.daily', array_merge(request()->all(), ['date' => $date->subDay()->format('Y-m-d')])) }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white px-3 py-2 rounded-lg text-sm">
                    <i class="fas fa-chevron-left mr-1"></i>Kemarin
                </a>
                @if($date->format('Y-m-d') < today()->format('Y-m-d'))
                    <a href="{{ route('reports.daily', array_merge(request()->all(), ['date' => $date->addDays(2)->format('Y-m-d')])) }}" 
                       class="bg-gray-500 hover:bg-gray-600 text-white px-3 py-2 rounded-lg text-sm">
                        Besok<i class="fas fa-chevron-right ml-1"></i>
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-receipt text-blue-600"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-800">{{ $summary['total_transactions'] }}</p>
                    <p class="text-xs text-gray-600">Total Transaksi</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-money-bill-wave text-green-600"></i>
                </div>
                <div>
                    <p class="text-lg font-bold text-gray-800">{{ number_format($summary['total_revenue'], 0, ',', '.') }}</p>
                    <p class="text-xs text-gray-600">Total Pendapatan</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-chart-line text-yellow-600"></i>
                </div>
                <div>
                    <p class="text-lg font-bold text-gray-800">{{ number_format($summary['gross_profit'], 0, ',', '.') }}</p>
                    <p class="text-xs text-gray-600">Laba Kotor</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-coins text-purple-600"></i>
                </div>
                <div>
                    <p class="text-lg font-bold text-gray-800">{{ number_format($summary['net_profit'], 0, ',', '.') }}</p>
                    <p class="text-xs text-gray-600">Laba Bersih</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Kasir Performance (jika ada filter kasir) -->
    @if($selectedUser)
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-user-tie mr-2 text-red-600"></i>
            Performa Kasir: {{ $selectedUser->name }}
        </h3>
        
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="text-center p-4 bg-blue-50 rounded-lg">
                <div class="text-2xl font-bold text-blue-600">{{ $summary['total_transactions'] }}</div>
                <div class="text-sm text-gray-600">Transaksi</div>
            </div>
            <div class="text-center p-4 bg-green-50 rounded-lg">
                <div class="text-lg font-bold text-green-600">Rp {{ number_format($summary['total_revenue'], 0, ',', '.') }}</div>
                <div class="text-sm text-gray-600">Total Penjualan</div>
            </div>
            <div class="text-center p-4 bg-yellow-50 rounded-lg">
                <div class="text-lg font-bold text-yellow-600">
                    Rp {{ $summary['total_transactions'] > 0 ? number_format($summary['total_revenue'] / $summary['total_transactions'], 0, ',', '.') : 0 }}
                </div>
                <div class="text-sm text-gray-600">Rata-rata/Transaksi</div>
            </div>
            <div class="text-center p-4 bg-purple-50 rounded-lg">
                <div class="text-lg font-bold text-purple-600">Rp {{ number_format($summary['net_profit'], 0, ',', '.') }}</div>
                <div class="text-sm text-gray-600">Kontribusi Profit</div>
            </div>
        </div>
    </div>
    @endif

    <!-- Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Hourly Chart -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                Transaksi per Jam
                @if($selectedUser)
                    <span class="text-sm font-normal text-gray-600">({{ $selectedUser->name }})</span>
                @endif
            </h3>
            @if($hourlyData->count() > 0)
                <div class="space-y-3">
                    @foreach($hourlyData as $hour)
                        @php
                            $maxCount = $hourlyData->max('transaction_count');
                            $percentage = $maxCount > 0 ? ($hour->transaction_count / $maxCount) * 100 : 0;
                        @endphp
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600 w-20">{{ sprintf('%02d:00', $hour->hour) }}</span>
                            <div class="flex-1 mx-3">
                                <div class="w-full bg-gray-200 rounded-full h-4">
                                    <div class="bg-blue-600 h-4 rounded-full transition-all duration-300" 
                                         style="width: {{ $percentage }}%"></div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-medium text-gray-800">{{ $hour->transaction_count }}</div>
                                <div class="text-xs text-gray-500">Rp {{ number_format($hour->revenue, 0, ',', '.') }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-chart-bar text-4xl mb-4 text-gray-300"></i>
                    <p>Belum ada transaksi pada tanggal ini</p>
                </div>
            @endif
        </div>

        <!-- Payment Methods -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                Metode Pembayaran
                @if($selectedUser)
                    <span class="text-sm font-normal text-gray-600">({{ $selectedUser->name }})</span>
                @endif
            </h3>
            @if($paymentMethods->count() > 0)
                <div class="space-y-4">
                    @foreach($paymentMethods as $method)
                        @php
                            $total = $paymentMethods->sum('total');
                            $percentage = $total > 0 ? ($method->total / $total) * 100 : 0;
                            $methodLabel = match($method->payment_method) {
                                'cash' => 'Tunai',
                                'card' => 'Kartu',
                                'digital' => 'Digital',
                                default => ucfirst($method->payment_method)
                            };
                            $methodIcon = match($method->payment_method) {
                                'cash' => 'fa-money-bill-wave',
                                'card' => 'fa-credit-card',
                                'digital' => 'fa-mobile-alt',
                                default => 'fa-question'
                            };
                        @endphp
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <i class="fas {{ $methodIcon }} text-gray-600 mr-2"></i>
                                <span class="text-sm text-gray-700">{{ $methodLabel }}</span>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-medium text-gray-800">{{ $method->count }} transaksi</div>
                                <div class="text-xs text-gray-500">Rp {{ number_format($method->total, 0, ',', '.') }}</div>
                                <div class="text-xs text-blue-600">{{ number_format($percentage, 1) }}%</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-credit-card text-4xl mb-4 text-gray-300"></i>
                    <p>Belum ada data pembayaran</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Transactions Table -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b border-gray-200">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                <h3 class="text-lg font-semibold text-gray-800">
                    Detail Transaksi
                    @if($selectedUser)
                        <span class="text-sm font-normal text-gray-600">({{ $selectedUser->name }})</span>
                    @endif
                </h3>
                <div class="mt-2 lg:mt-0 text-sm text-gray-600">
                    Total: {{ $transactions->count() }} transaksi
                </div>
            </div>
        </div>

        @if($transactions->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waktu</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                            @if(!$selectedUser)
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kasir</th>
                            @endif
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Items</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pembayaran</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Profit</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($transactions as $transaction)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $transaction->time_only }}
                                </td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-800">
                                    {{ $transaction->transaction_code }}
                                </td>
                                @if(!$selectedUser)
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $transaction->user->name }}
                                </td>
                                @endif
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $transaction->total_items }} item
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $transaction->payment_method_label }}
                                </td>
                                <td class="px-6 py-4 text-sm font-medium text-green-600">
                                    {{ $transaction->formatted_total_amount }}
                                </td>
                                <td class="px-6 py-4 text-sm font-medium text-blue-600">
                                    {{ $transaction->formatted_profit }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-8 text-center text-gray-500">
                <i class="fas fa-receipt text-4xl mb-4 text-gray-300"></i>
                <p>Belum ada transaksi pada tanggal {{ $date->format('d/m/Y') }}
                    @if($selectedUser) oleh {{ $selectedUser->name }} @endif
                </p>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function dailyReport() {
    return {
        // Add any interactive functionality here
        init() {
            console.log('Daily report initialized');
        }
    }
}
</script>
@endpush
@endsection