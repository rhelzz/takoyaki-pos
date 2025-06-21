@extends('layouts.app')

@section('title', 'Kelola Produk - Takoyaki POS')

@section('content')
<div class="p-4 max-w-7xl mx-auto" x-data="products()">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-6">
        <div class="mb-4 lg:mb-0">
            <h1 class="text-2xl font-bold text-gray-800">Kelola Produk</h1>
            <p class="text-gray-600">Atur dan kelola produk takoyaki</p>
        </div>
        <a href="{{ route('products.create') }}" 
           class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg inline-flex items-center justify-center">
            <i class="fas fa-plus mr-2"></i>Tambah Produk
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow mb-6 p-4">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
            <!-- Search -->
            <div>
                <input type="text" 
                       x-model="searchQuery"
                       placeholder="Cari produk..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
            </div>

            <!-- Category Filter -->
            <div>
                <select x-model="selectedCategory" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                    <option value="">Semua Kategori</option>
                    @foreach($categories ?? [] as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Status Filter -->
            <div>
                <select x-model="selectedStatus" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                    <option value="">Semua Status</option>
                    <option value="1">Aktif</option>
                    <option value="0">Tidak Aktif</option>
                </select>
            </div>

            <!-- Sort -->
            <div>
                <select x-model="sortBy" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                    <option value="name">Nama</option>
                    <option value="price">Harga</option>
                    <option value="created_at">Terbaru</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Products Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
        @foreach($products as $product)
            <div class="bg-white rounded-lg shadow-sm border hover:shadow-md transition-shadow">
                <!-- Product Image -->
                <div class="aspect-video bg-gray-100 rounded-t-lg overflow-hidden">
                    @if($product->image)
                        <img src="{{ $product->image_url }}" 
                             alt="{{ $product->name }}"
                             class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center">
                            <i class="fas fa-image text-gray-300 text-4xl"></i>
                        </div>
                    @endif
                </div>

                <!-- Product Info -->
                <div class="p-4">
                    <div class="flex items-start justify-between mb-2">
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-800 mb-1">{{ $product->name }}</h3>
                            <p class="text-sm text-gray-500">{{ $product->category->name }}</p>
                        </div>
                        <div class="flex items-center space-x-2">
                            @if($product->is_active)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    Nonaktif
                                </span>
                            @endif
                        </div>
                    </div>

                    @if($product->description)
                        <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ $product->description }}</p>
                    @endif

                    <!-- Pricing -->
                    <div class="mb-3 space-y-1">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Harga Modal:</span>
                            <span class="font-medium">{{ $product->formatted_cost_price }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Harga Jual:</span>
                            <span class="font-medium text-green-600">{{ $product->formatted_selling_price }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Keuntungan:</span>
                            <span class="font-medium text-blue-600">{{ $product->formatted_profit }} ({{ number_format($product->profit_margin, 1) }}%)</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Per Porsi:</span>
                            <span class="font-medium">{{ $product->quantity_per_serving }} pcs</span>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex space-x-2">
                        <a href="{{ route('products.show', $product) }}" 
                           class="flex-1 bg-blue-500 hover:bg-blue-600 text-white py-2 px-3 rounded text-sm text-center">
                            <i class="fas fa-eye mr-1"></i>Detail
                        </a>
                        <a href="{{ route('products.edit', $product) }}" 
                           class="flex-1 bg-yellow-500 hover:bg-yellow-600 text-white py-2 px-3 rounded text-sm text-center">
                            <i class="fas fa-edit mr-1"></i>Edit
                        </a>
                        <button @click="toggleStatus({{ $product->id }}, {{ $product->is_active ? 'false' : 'true' }})"
                                class="px-3 py-2 rounded text-sm {{ $product->is_active ? 'bg-red-500 hover:bg-red-600' : 'bg-green-500 hover:bg-green-600' }} text-white">
                            <i class="fas {{ $product->is_active ? 'fa-times' : 'fa-check' }}"></i>
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Pagination -->
    @if($products->hasPages())
        <div class="mt-6">
            {{ $products->links() }}
        </div>
    @endif

    <!-- Empty State -->
    @if($products->count() === 0)
        <div class="text-center py-12">
            <i class="fas fa-box text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada produk</h3>
            <p class="text-gray-500 mb-4">Mulai dengan menambahkan produk pertama Anda</p>
            <a href="{{ route('products.create') }}" 
               class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg inline-flex items-center">
                <i class="fas fa-plus mr-2"></i>Tambah Produk
            </a>
        </div>
    @endif
</div>

@push('scripts')
<script>
function products() {
    return {
        searchQuery: '',
        selectedCategory: '',
        selectedStatus: '',
        sortBy: 'name',

        async toggleStatus(productId, newStatus) {
            if (!confirm('Yakin ingin mengubah status produk?')) return;

            try {
                const response = await fetch(`/products/${productId}/toggle-status`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                if (response.ok) {
                    location.reload();
                } else {
                    showToast('Gagal mengubah status produk', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showToast('Terjadi kesalahan sistem', 'error');
            }
        }
    }
}
</script>
@endpush
@endsection