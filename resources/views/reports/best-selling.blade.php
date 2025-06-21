@extends('layouts.app')

@section('title', 'Produk Terlaris - Takoyaki POS')

@section('content')
<div class="p-4 max-w-7xl mx-auto" x-data="bestSellingReport()">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center mb-4">
            <a href="{{ route('reports.index') }}" 
               class="mr-3 p-2 text-gray-600 hover:text-gray-800">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Produk Terlaris</h1>
                <p class="text-gray-600">Analisis penjualan produk dalam periode tertentu</p>
            </div>
        </div>

        <!-- Period Selector -->
        <div class="bg-white rounded-lg shadow p-4">
            <form method="GET" class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                <div class="flex flex-col lg:flex-row lg:items-center space-y-3 lg:space-y-0 lg:space-x-3">
                    <label class="text-sm font-medium text-gray-700">Periode:</label>
                    <select name="period" 
                            onchange="this.form.submit()"
                            class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                        <option value="month" {{ $period === 'month' ? 'selected' : '' }}>1 Bulan Terakhir</option>
                        <option value="3months" {{ $period === '3months' ? 'selected' : '' }}>3 Bulan Terakhir</option>
                        <option value="6months" {{ $period === '6months' ? 'selected' : '' }}>6 Bulan Terakhir</option>
                        <option value="year" {{ $period === 'year' ? 'selected' : '' }}>1 Tahun Terakhir</option>
                    </select>
                </div>

                <div class="mt-3 lg:mt-0 text-sm text-gray-600">
                    Periode: {{ $startDate->format('d/m/Y') }} - {{ now()->format('d/m/Y') }}
                </div>
            </form>
        </div>
    </div>

    <!-- Top Categories -->
    <div class="mb-6 bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Kategori Terlaris</h2>
        @if($topCategories->count() > 0)
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                @foreach($topCategories->take(3) as $index => $category)
                    <div class="text-center p-4 rounded-lg {{ $index === 0 ? 'bg-yellow-50 border-2 border-yellow-200' : ($index === 1 ? 'bg-gray-50 border-2 border-gray-200' : 'bg-orange-50 border-2 border-orange-200') }}">
                        <div class="text-3xl mb-2">
                            @if($index === 0)
                                🥇
                            @elseif($index === 1)
                                🥈
                            @else
                                🥉
                            @endif
                        </div>
                        <h3 class="font-semibold text-gray-800 mb-1">{{ $category->name }}</h3>
                        <p class="text-lg font-bold text-blue-600">{{ $category->total_sold }} pcs</p>
                        <p class="text-sm text-gray-600">Rp {{ number_format($category->total_revenue, 0, ',', '.') }}</p>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8 text-gray-500">
                <i class="fas fa-chart-bar text-4xl mb-4 text-gray-300"></i>
                <p>Belum ada data penjualan dalam periode ini</p>
            </div>
        @endif
    </div>

    <!-- Best Selling Products -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Top 20 Produk Terlaris</h2>
        </div>

        @if($bestSelling->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rank</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produk</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Terjual</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pendapatan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Profit</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Transaksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($bestSelling as $index => $item)
                            <tr class="hover:bg-gray-50 {{ $index < 3 ? 'bg-yellow-50' : '' }}">
                                <td class="px-6 py-4 text-sm font-medium text-gray-800">
                                    <div class="flex items-center">
                                        @if($index === 0)
                                            <span class="text-2xl mr-2">🥇</span>
                                        @elseif($index === 1)
                                            <span class="text-2xl mr-2">🥈</span>
                                        @elseif($index === 2)
                                            <span class="text-2xl mr-2">🥉</span>
                                        @else
                                            <span class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center text-sm font-medium mr-2">
                                                {{ $index + 1 }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        @if($item->product->image)
                                            <img src="{{ $item->product->image_url }}" 
                                                 alt="{{ $item->product->name }}"
                                                 class="w-10 h-10 rounded-lg object-cover mr-3">
                                        @else
                                            <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center mr-3">
                                                <i class="fas fa-image text-gray-400"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <p class="font-medium text-gray-800">{{ $item->product->name }}</p>
                                            <p class="text-sm text-gray-500">{{ $item->product->quantity_per_serving }} pcs</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $item->product->category->name }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm">
                                        <p class="font-bold text-lg text-blue-600">{{ $item->total_sold }}</p>
                                        <p class="text-gray-500">pcs</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm font-medium text-green-600">
                                    Rp {{ number_format($item->total_revenue, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-sm font-medium text-purple-600">
                                    Rp {{ number_format($item->total_profit, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $item->transaction_count }} transaksi
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Summary -->
            <div class="p-6 bg-gray-50 border-t">
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 text-center">
                    <div>
                        <p class="text-2xl font-bold text-blue-600">{{ $bestSelling->sum('total_sold') }}</p>
                        <p class="text-sm text-gray-600">Total Terjual</p>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-green-600">{{ number_format($bestSelling->sum('total_revenue'), 0, ',', '.') }}</p>
                        <p class="text-sm text-gray-600">Total Pendapatan</p>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-purple-600">{{ number_format($bestSelling->sum('total_profit'), 0, ',', '.') }}</p>
                        <p class="text-sm text-gray-600">Total Profit</p>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-600">{{ $bestSelling->sum('transaction_count') }}</p>
                        <p class="text-sm text-gray-600">Total Transaksi</p>
                    </div>
                </div>
            </div>
        @else
            <div class="p-8 text-center text-gray-500">
                <i class="fas fa-trophy text-4xl mb-4 text-gray-300"></i>
                <p>Belum ada data penjualan dalam periode ini</p>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function bestSellingReport() {
    return {
        init() {
            console.log('Best selling report initialized');
        }
    }
}
</script>
@endpush
@endsection