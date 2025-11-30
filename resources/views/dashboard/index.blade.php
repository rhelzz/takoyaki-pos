@extends('layouts.app')

@section('title', 'Dashboard - Takoyaki POS')

@section('content')
<div class="p-4 max-w-7xl mx-auto" x-data="dashboard()">
    <!-- Filter Kasir (hanya untuk Admin/Manager) -->
    @if(auth()->user()->role !== 'cashier')
        <form method="GET" class="mb-4">
            <div class="bg-white rounded-lg shadow p-4 flex flex-col lg:flex-row items-start lg:items-center gap-3">
                <label for="user_id" class="text-sm font-medium text-gray-700">
                    <i class="fas fa-filter mr-2 text-red-600"></i>Pilih Kasir:
                </label>
                <select name="user_id" 
                        id="user_id" 
                        class="flex-1 lg:flex-none px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 min-w-[200px]" 
                        onchange="this.form.submit()">
                    <option value="">📊 Semua Kasir</option>
                    @foreach($cashiers as $kasir)
                        <option value="{{ $kasir->id }}" {{ $userId == $kasir->id ? 'selected' : '' }}>
                            {{ $kasir->name }}
                        </option>
                    @endforeach
                </select>
                @if($userId)
                    <a href="{{ route('dashboard') }}" 
                       class="text-sm text-gray-600 hover:text-red-600 flex items-center">
                        <i class="fas fa-times mr-1"></i>Reset Filter
                    </a>
                @endif
            </div>
        </form>
    @else
        <!-- Info untuk Kasir -->
        <div class="mb-4 bg-gradient-to-r from-red-50 to-orange-50 rounded-lg shadow p-4 border border-red-100">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-red-500 rounded-full flex items-center justify-center text-white text-lg font-bold mr-4">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div>
                    <p class="text-sm text-gray-600">Data Dashboard untuk:</p>
                    <p class="text-lg font-bold text-gray-800">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-gray-500 mt-1">
                        <i class="fas fa-info-circle mr-1"></i>
                        Menampilkan statistik transaksi Anda hari ini
                    </p>
                </div>
            </div>
        </div>
    @endif
    
    <!-- Welcome Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-2">
            Selamat datang, {{ auth()->user()->name }}!
        </h1>
        <p class="text-gray-600">{{ now()->format('l, d F Y') }}</p>
    </div>

    <!-- Today's Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Total Transaksi -->
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-receipt text-blue-600"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-800">{{ $todayTransactions }}</p>
                    <p class="text-xs text-gray-600">Transaksi Hari Ini</p>
                </div>
            </div>
        </div>

        <!-- Revenue -->
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-dollar-sign text-green-600"></i>
                </div>
                <div>
                    <p class="text-lg font-bold text-gray-800">{{ number_format($todayRevenue, 0, ',', '.') }}</p>
                    <p class="text-xs text-gray-600">Pendapatan Hari Ini</p>
                </div>
            </div>
        </div>

        <!-- Gross Profit -->
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-chart-line text-yellow-600"></i>
                </div>
                <div>
                    <p class="text-lg font-bold text-gray-800">{{ number_format($todayGrossProfit, 0, ',', '.') }}</p>
                    <p class="text-xs text-gray-600">Laba Kotor</p>
                </div>
            </div>
        </div>

        <!-- Net Profit -->
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-coins text-purple-600"></i>
                </div>
                <div>
                    <p class="text-lg font-bold text-gray-800">{{ number_format($todayNetProfit, 0, ',', '.') }}</p>
                    <p class="text-xs text-gray-600">Laba Bersih</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Stats for Admin/Manager -->
    @if(auth()->user()->canViewReports())
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="font-medium text-gray-800">Total Users</h3>
                    <i class="fas fa-users text-gray-400"></i>
                </div>
                <p class="text-2xl font-bold text-gray-800">{{ $totalUsers ?? 0 }}</p>
            </div>

            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="font-medium text-gray-800">Total Produk</h3>
                    <i class="fas fa-box text-gray-400"></i>
                </div>
                <p class="text-2xl font-bold text-gray-800">{{ $totalProducts ?? 0 }}</p>
            </div>

            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="font-medium text-gray-800">Pendapatan Bulan Ini</h3>
                    <i class="fas fa-calendar text-gray-400"></i>
                </div>
                <p class="text-xl font-bold text-gray-800">{{ number_format($monthlyRevenue ?? 0, 0, ',', '.') }}</p>
            </div>
        </div>
    @endif

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Hourly Transactions -->
        <div class="bg-white rounded-lg shadow p-4">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Jam Tersibuk Hari Ini</h3>
            @if($hourlyTransactions->count() > 0)
                <div class="space-y-3">
                    @foreach($hourlyTransactions as $hour)
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">{{ $hour->hour }}:00 - {{ $hour->hour + 1 }}:00</span>
                            <div class="flex items-center space-x-2">
                                <div class="w-20 bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full" 
                                         style="width: {{ ($hour->count / $hourlyTransactions->max('count')) * 100 }}%"></div>
                                </div>
                                <span class="text-sm font-medium text-gray-800">{{ $hour->count }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-8">Belum ada transaksi hari ini</p>
            @endif
        </div>

        <!-- Top Products -->
        <div class="bg-white rounded-lg shadow p-4">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Produk Terlaris Hari Ini</h3>
            @if($topProducts->count() > 0)
                <div class="space-y-3">
                    @foreach($topProducts as $product)
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600 truncate">{{ $product->name }}</span>
                            <span class="text-sm font-medium text-gray-800 bg-gray-100 px-2 py-1 rounded">
                                {{ $product->total_sold }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-8">Belum ada penjualan hari ini</p>
            @endif
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Transaksi Terbaru</h3>
        </div>
        <div class="overflow-x-auto">
            @if($recentTransactions->count() > 0)
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waktu</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kasir</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Items</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($recentTransactions as $transaction)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm font-medium text-gray-800">
                                    {{ $transaction->transaction_code }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    {{ $transaction->created_at->format('H:i') }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    {{ $transaction->user->name }}
                                </td>
                                <td class="px-4 py-3 text-sm font-medium text-green-600">
                                    Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    {{ $transaction->items->sum('quantity') }} item
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="p-8 text-center text-gray-500">
                    <i class="fas fa-receipt text-4xl mb-4 text-gray-300"></i>
                    <p>Belum ada transaksi</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mt-6 grid grid-cols-2 lg:grid-cols-4 gap-4">
        @if(auth()->user()->canProcessTransactions())
            <a href="{{ route('cashier') }}" 
               class="bg-red-500 hover:bg-red-600 text-white p-4 rounded-lg text-center transition-colors">
                <i class="fas fa-cash-register text-2xl mb-2"></i>
                <p class="text-sm font-medium">Buka Kasir</p>
            </a>
        @endif

        @if(auth()->user()->canManageProducts())
            <a href="{{ route('products.create') }}" 
               class="bg-blue-500 hover:bg-blue-600 text-white p-4 rounded-lg text-center transition-colors">
                <i class="fas fa-plus text-2xl mb-2"></i>
                <p class="text-sm font-medium">Tambah Produk</p>
            </a>
        @endif

        @if(auth()->user()->canViewReports())
            <a href="{{ route('reports.daily') }}" 
               class="bg-green-500 hover:bg-green-600 text-white p-4 rounded-lg text-center transition-colors">
                <i class="fas fa-chart-bar text-2xl mb-2"></i>
                <p class="text-sm font-medium">Laporan Harian</p>
            </a>
        @endif

        @if(auth()->user()->canManageUsers())
            <a href="{{ route('users.create') }}" 
               class="bg-purple-500 hover:bg-purple-600 text-white p-4 rounded-lg text-center transition-colors">
                <i class="fas fa-user-plus text-2xl mb-2"></i>
                <p class="text-sm font-medium">Tambah User</p>
            </a>
        @endif
    </div>
</div>

@push('scripts')
<script>
function dashboard() {
    return {
        init() {
            // Auto refresh data setiap 5 menit
            setInterval(() => {
                window.location.reload();
            }, 300000);
        }
    }
}
</script>
@endpush
@endsection