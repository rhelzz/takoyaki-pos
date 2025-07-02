@extends('layouts.app')

@section('title', 'Stock Keluar - Takoyaki POS')

@section('content')
<div class="min-h-screen bg-gray-50" x-data="stockKeluar()">
    <!-- Header - Mobile Optimized -->
    <div class="bg-white border-b border-gray-200 px-4 py-4 sticky top-0 z-10">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl md:text-2xl font-bold text-gray-800">Stock Keluar</h1>
                <p class="text-sm text-gray-600 hidden sm:block">Kelola barang keluar</p>
            </div>
            <!-- Mobile Quick Actions -->
            <div class="flex space-x-2">
                <button @click="showFilters = !showFilters" 
                        class="lg:hidden bg-orange-500 text-white p-2 rounded-lg">
                    <i class="fas fa-filter text-sm"></i>
                </button>
                <a href="{{ route('stock-keluar.create') }}" 
                   class="bg-orange-500 hover:bg-orange-600 text-white px-3 py-2 rounded-lg text-sm">
                    <i class="fas fa-plus mr-1"></i>
                    <span class="hidden sm:inline">Tambah</span>
                </a>
            </div>
        </div>
    </div>

    <div class="p-4 max-w-7xl mx-auto space-y-4">
        <!-- Filters - Mobile Optimized -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100" 
             x-show="showFilters || window.innerWidth >= 1024" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 transform -translate-y-2"
             x-transition:enter-end="opacity-100 transform translate-y-0">
            <form method="GET" class="p-4">
                <!-- Mobile: Stack filters vertically -->
                <div class="space-y-3 lg:space-y-0 lg:grid lg:grid-cols-4 lg:gap-4">
                    <!-- Search -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1 lg:hidden">Cari Barang</label>
                        <input type="text" 
                               name="search"
                               value="{{ request('search') }}"
                               placeholder="Cari nama barang..."
                               class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                    </div>

                    <!-- Date Filter -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1 lg:hidden">Tanggal</label>
                        <input type="date" 
                               name="date"
                               value="{{ request('date') }}"
                               class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                    </div>

                    <!-- Submit Buttons -->
                    <div class="lg:col-span-2">
                        <div class="flex space-x-2 lg:pt-0">
                            <button type="submit" 
                                    class="flex-1 bg-orange-500 hover:bg-orange-600 text-white px-4 py-2.5 rounded-lg text-sm font-medium">
                                <i class="fas fa-search mr-2"></i>Filter
                            </button>
                            <a href="{{ route('stock-keluar.index') }}" 
                               class="px-4 py-2.5 bg-gray-500 hover:bg-gray-600 text-white rounded-lg">
                                <i class="fas fa-times text-sm"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Stock List -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            @if($stockKeluar->count() > 0)
                <!-- Desktop Table -->
                <div class="hidden lg:block overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Barang</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($stockKeluar as $stock)
                                <tr class="hover:bg-gray-50 transition-colors" id="stock-row-{{ $stock->id }}">
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ $stock->formatted_tanggal }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-800">{{ $stock->nama_barang }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-orange-600 font-medium">
                                        -{{ $stock->qty }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex space-x-3">
                                            <a href="{{ route('stock-keluar.show', $stock) }}" 
                                               class="text-blue-600 hover:text-blue-800 text-sm">
                                                <i class="fas fa-eye mr-1"></i>Detail
                                            </a>
                                            <a href="{{ route('stock-keluar.edit', $stock) }}" 
                                               class="text-yellow-600 hover:text-yellow-800 text-sm">
                                                <i class="fas fa-edit mr-1"></i>Edit
                                            </a>
                                            <button @click="deleteStock('{{ $stock->id }}', '{{ $stock->nama_barang }}')"
                                                    class="text-red-600 hover:text-red-800 text-sm">
                                                <i class="fas fa-trash mr-1"></i>Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Cards -->
                <div class="lg:hidden">
                    @foreach($stockKeluar as $stock)
                        <div class="border-b border-gray-100 last:border-b-0" id="stock-mobile-{{ $stock->id }}">
                            <!-- Card Header -->
                            <div class="p-4 bg-gray-50 border-b border-gray-100">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-orange-500 rounded-full flex items-center justify-center text-white text-sm">
                                            <i class="fas fa-arrow-down"></i>
                                        </div>
                                        <div>
                                            <h3 class="font-semibold text-gray-800">{{ $stock->nama_barang }}</h3>
                                            <p class="text-xs text-gray-500">{{ $stock->formatted_tanggal }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-bold text-orange-600">-{{ $stock->qty }}</div>
                                        <div class="text-xs text-gray-500">Stock Keluar</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Card Body -->
                            <div class="p-4 space-y-3">
                                <!-- Action Buttons -->
                                <div class="flex space-x-2">
                                    <a href="{{ route('stock-keluar.show', $stock) }}" 
                                       class="flex-1 text-center bg-blue-500 hover:bg-blue-600 text-white py-2 px-3 rounded-lg text-sm">
                                        <i class="fas fa-eye mr-1"></i>Detail
                                    </a>
                                    <a href="{{ route('stock-keluar.edit', $stock) }}" 
                                       class="flex-1 text-center bg-yellow-500 hover:bg-yellow-600 text-white py-2 px-3 rounded-lg text-sm">
                                        <i class="fas fa-edit mr-1"></i>Edit
                                    </a>
                                    <button @click="deleteStock('{{ $stock->id }}', '{{ $stock->nama_barang }}')"
                                            class="flex-1 bg-red-500 hover:bg-red-600 text-white py-2 px-3 rounded-lg text-sm">
                                        <i class="fas fa-trash mr-1"></i>Hapus
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($stockKeluar->hasPages())
                    <div class="p-4 border-t border-gray-200 bg-gray-50">
                        {{ $stockKeluar->appends(request()->query())->links() }}
                    </div>
                @endif
            @else
                <!-- Empty State - Mobile Optimized -->
                <div class="p-8 text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-arrow-down text-2xl text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-800 mb-2">Belum ada stock keluar</h3>
                    <p class="text-gray-500 mb-6">Mulai catat barang yang keluar</p>
                    <a href="{{ route('stock-keluar.create') }}" 
                       class="inline-flex items-center px-6 py-3 bg-orange-500 hover:bg-orange-600 text-white rounded-lg font-medium">
                        <i class="fas fa-plus mr-2"></i>Tambah Stock Keluar
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
function stockKeluar() {
    return {
        showFilters: false,

        deleteStock(id, name) {
            if (confirm(`Apakah Anda yakin ingin menghapus stock keluar "${name}"?`)) {
                fetch(`/stock-keluar/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast(data.message, 'success');
                        // Remove from DOM with animation
                        this.removeStockFromDOM(id);
                    } else {
                        showToast(data.message || 'Terjadi kesalahan', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('Gagal menghapus stock', 'error');
                });
            }
        },

        removeStockFromDOM(stockId) {
            const elementsToRemove = [
                document.getElementById(`stock-row-${stockId}`),
                document.getElementById(`stock-mobile-${stockId}`)
            ];

            elementsToRemove.forEach(element => {
                if (element) {
                    element.style.transition = 'all 0.3s ease';
                    element.style.opacity = '0';
                    element.style.transform = 'translateX(-20px) scale(0.95)';
                    
                    setTimeout(() => {
                        if (element && element.parentNode) {
                            element.parentNode.removeChild(element);
                        }
                    }, 300);
                }
            });
        }
    }
}
</script>
@endpush
@endsection