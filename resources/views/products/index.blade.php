@extends('layouts.app')

@section('title', 'Kelola Produk - Takoyaki POS')

@section('content')
<div class="min-h-screen bg-gray-50" x-data="products()">
    <!-- Mobile Header -->
    <div class="bg-white border-b border-gray-200 sticky top-0 z-10">
        <div class="px-4 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-xl md:text-2xl font-bold text-gray-800">Produk</h1>
                    <p class="text-sm text-gray-600 hidden sm:block">Atur dan kelola produk takoyaki</p>
                </div>
                <div class="flex items-center space-x-2">
                    <!-- Mobile Filter Toggle -->
                    <button @click="showFilters = !showFilters" 
                            class="lg:hidden bg-gray-500 text-white p-2 rounded-lg">
                        <i class="fas fa-filter text-sm"></i>
                    </button>
                    <!-- Add Product Button -->
                    <a href="{{ route('products.create') }}" 
                       class="bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded-lg text-sm">
                        <i class="fas fa-plus mr-1"></i>
                        <span class="hidden sm:inline">Tambah</span>
                    </a>
                </div>
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
            <form method="GET" action="{{ route('products.index') }}" class="p-4">
                <div class="space-y-3 lg:space-y-0 lg:grid lg:grid-cols-4 lg:gap-4">
                    <!-- Search -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1 lg:hidden">Cari Produk</label>
                        <input type="text" 
                               name="search"
                               value="{{ request('search') }}"
                               placeholder="Cari produk..."
                               class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1 lg:hidden">Status</label>
                        <select name="status" 
                                class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                            <option value="">Semua Status</option>
                            <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Tidak Aktif</option>
                        </select>
                    </div>

                    <!-- Sort -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1 lg:hidden">Urutkan</label>
                        <select name="sort" 
                                class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                            <option value="name" {{ request('sort', 'name') === 'name' ? 'selected' : '' }}>Nama A-Z</option>
                            <option value="name_desc" {{ request('sort') === 'name_desc' ? 'selected' : '' }}>Nama Z-A</option>
                            <option value="price" {{ request('sort') === 'price' ? 'selected' : '' }}>Harga Terendah</option>
                            <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Harga Tertinggi</option>
                            <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>Terbaru</option>
                            <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Terlama</option>
                        </select>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex space-x-2">
                        <button type="submit" 
                                class="flex-1 bg-red-500 hover:bg-red-600 text-white px-4 py-2.5 rounded-lg text-sm font-medium">
                            <i class="fas fa-search mr-2"></i>Filter
                        </button>
                        <a href="{{ route('products.index') }}" 
                           class="px-4 py-2.5 bg-gray-500 hover:bg-gray-600 text-white rounded-lg">
                            <i class="fas fa-times text-sm"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Products List - Fixed Layout -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            @if($products->count() > 0)
                <!-- Desktop Table -->
                <div class="hidden lg:block overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Produk</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Kategori</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Harga</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Profit</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($products as $product)
                                <tr class="hover:bg-gray-50 transition-colors" id="product-row-{{ $product->id }}">
                                    <td class="px-4 py-4">
                                        <a href="{{ route('products.show', $product) }}" class="flex items-center hover:text-red-600 transition-colors">
                                            @if($product->image)
                                                <img src="{{ $product->image_url }}" 
                                                     alt="{{ $product->name }}"
                                                     class="w-12 h-12 rounded-lg object-cover mr-3 flex-shrink-0">
                                            @else
                                                <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center mr-3 flex-shrink-0">
                                                    <i class="fas fa-image text-gray-400"></i>
                                                </div>
                                            @endif
                                            <div class="min-w-0 flex-1">
                                                <h3 class="font-medium text-gray-800">{{ $product->name }}</h3>
                                                @if(!$product->is_active)
                                                    <span class="text-xs text-red-500">Nonaktif</span>
                                                @endif
                                            </div>
                                        </a>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-600">{{ $product->category->name }}</td>
                                    <td class="px-4 py-4 text-sm font-medium text-green-600">{{ $product->formatted_selling_price }}</td>
                                    <td class="px-4 py-4 text-sm font-medium text-blue-600">{{ $product->formatted_profit }}</td>
                                    <td class="px-4 py-4">
                                        <div class="flex items-center justify-center space-x-3">
                                            <a href="{{ route('products.edit', $product) }}" 
                                               class="text-yellow-600 hover:text-yellow-800 p-1" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button @click="deleteProduct({{ $product->id }}, '{{ addslashes($product->name) }}')"
                                                    class="text-red-600 hover:text-red-800 p-1 delete-btn" 
                                                    title="Hapus"
                                                    data-product-id="{{ $product->id }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Mobile List - Fixed Layout -->
                <div class="lg:hidden divide-y divide-gray-100">
                    @foreach($products as $product)
                        <div class="p-4" id="product-mobile-{{ $product->id }}">
                            <!-- Clickable Product Area -->
                            <a href="{{ route('products.show', $product) }}" class="block hover:opacity-75 transition-opacity">
                                <div class="flex items-center space-x-3">
                                    <!-- Product Image -->
                                    @if($product->image)
                                        <img src="{{ $product->image_url }}" 
                                             alt="{{ $product->name }}"
                                             class="w-14 h-14 rounded-lg object-cover flex-shrink-0">
                                    @else
                                        <div class="w-14 h-14 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-image text-gray-400"></i>
                                        </div>
                                    @endif
                                    
                                    <!-- Product Info - Full Width -->
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-start justify-between">
                                            <div class="flex-1 min-w-0 pr-4">
                                                <h3 class="font-medium text-gray-800 truncate">{{ $product->name }}</h3>
                                                <p class="text-xs text-gray-500 mt-0.5">{{ $product->category->name }}</p>
                                                @if(!$product->is_active)
                                                    <span class="inline-block text-xs text-red-500 mt-1">Nonaktif</span>
                                                @endif
                                            </div>
                                            <div class="text-right flex-shrink-0">
                                                <div class="text-sm font-medium text-green-600">{{ $product->formatted_selling_price }}</div>
                                                <div class="text-xs text-blue-600">{{ $product->formatted_profit }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                            
                            <!-- Action Buttons - Below Product Info -->
                            <div class="flex justify-end space-x-3 mt-3 pt-3 border-t border-gray-100">
                                <a href="{{ route('products.edit', $product) }}" 
                                   class="text-yellow-600 hover:text-yellow-800 text-sm font-medium"
                                   title="Edit">
                                    <i class="fas fa-edit mr-1"></i>Edit
                                </a>
                                <button @click="deleteProduct({{ $product->id }}, '{{ addslashes($product->name) }}')"
                                        class="text-red-600 hover:text-red-800 text-sm font-medium delete-btn"
                                        title="Hapus"
                                        data-product-id="{{ $product->id }}">
                                    <i class="fas fa-trash mr-1"></i>Hapus
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($products->hasPages())
                    <div class="p-4 border-t border-gray-200 bg-gray-50">
                        {{ $products->appends(request()->query())->links() }}
                    </div>
                @endif
            @else
                <!-- Empty State -->
                <div class="p-8 text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-box text-2xl text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-800 mb-2">Belum ada produk</h3>
                    <p class="text-gray-500 mb-6">Mulai dengan menambahkan produk pertama Anda</p>
                    <a href="{{ route('products.create') }}" 
                       class="inline-flex items-center px-6 py-3 bg-red-500 hover:bg-red-600 text-white rounded-lg font-medium">
                        <i class="fas fa-plus mr-2"></i>Tambah Produk
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Loading Overlay -->
    <div x-show="isDeleting" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
         style="display: none;">
        <div class="bg-white rounded-lg p-6 text-center">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-red-500 mx-auto mb-4"></div>
            <p class="text-gray-600">Menghapus produk...</p>
        </div>
    </div>
</div>

@push('scripts')
<script>
function products() {
    return {
        showFilters: false,
        isDeleting: false,
        deletingProducts: new Set(),

        init() {
            // Auto-show filters on mobile if there are active filters
            if (window.innerWidth < 1024) {
                const hasActiveFilters = new URLSearchParams(window.location.search).toString() !== '';
                this.showFilters = hasActiveFilters;
            }

            // Add keyboard shortcut for refresh (Ctrl+R or Cmd+R)
            document.addEventListener('keydown', (e) => {
                if ((e.ctrlKey || e.metaKey) && e.key === 'r') {
                    e.preventDefault();
                    this.refreshPage();
                }
            });
        },

        /**
         * Enhanced delete product function with proper error handling
         */
        async deleteProduct(productId, productName) {
            // Prevent multiple deletions of the same product
            if (this.deletingProducts.has(productId)) {
                return;
            }

            // Confirm deletion
            if (!confirm(`Yakin ingin menghapus produk "${productName}"?\n\nAksi ini tidak dapat dibatalkan.`)) {
                return;
            }

            // Mark product as being deleted
            this.deletingProducts.add(productId);
            this.isDeleting = true;

            // Get button elements
            const deleteButtons = document.querySelectorAll(`[data-product-id="${productId}"]`);
            const originalContents = new Map();

            try {
                // Validate CSRF token
                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                if (!csrfToken || !csrfToken.content) {
                    throw new Error('CSRF token tidak ditemukan. Harap refresh halaman dan coba lagi.');
                }

                // Update button states to loading
                deleteButtons.forEach(button => {
                    originalContents.set(button, button.innerHTML);
                    button.disabled = true;
                    button.classList.add('opacity-50', 'cursor-not-allowed');
                    
                    const icon = button.querySelector('i');
                    if (icon) {
                        icon.className = 'fas fa-spinner fa-spin mr-1';
                    }
                    
                    const text = button.textContent.trim();
                    if (text.includes('Hapus')) {
                        button.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Menghapus...';
                    }
                });

                // Make DELETE request
                const response = await fetch(`/products/${productId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken.content,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    // Add timeout
                    signal: AbortSignal.timeout(30000) // 30 seconds
                });

                // Parse response
                let data;
                try {
                    data = await response.json();
                } catch (parseError) {
                    console.error('JSON parse error:', parseError);
                    throw new Error('Respons server tidak valid. Silakan coba lagi.');
                }

                if (response.ok && data.success) {
                    // Success: Remove from DOM with animation
                    this.removeProductFromDOM(productId, productName);
                    
                    // Show success message
                    this.showToast(data.message || 'Produk berhasil dihapus', 'success');
                    
                } else {
                    // Handle various error cases
                    let errorMessage = data.message || 'Gagal menghapus produk';
                    
                    if (data.code === 'PRODUCT_IN_USE') {
                        errorMessage = 'Produk tidak dapat dihapus karena sudah digunakan dalam transaksi';
                    } else if (response.status === 404) {
                        errorMessage = 'Produk tidak ditemukan';
                    } else if (response.status === 422) {
                        errorMessage = data.message || 'Data tidak valid';
                    } else if (response.status >= 500) {
                        errorMessage = 'Terjadi kesalahan server. Silakan coba lagi nanti.';
                    }
                    
                    this.showToast(errorMessage, 'error');
                    this.restoreButtons(deleteButtons, originalContents);
                }

            } catch (error) {
                console.error('Delete product error:', error);
                
                let errorMessage = 'Terjadi kesalahan sistem';
                
                if (error.name === 'AbortError') {
                    errorMessage = 'Request timeout. Silakan coba lagi.';
                } else if (error.message.includes('CSRF')) {
                    errorMessage = error.message;
                } else if (error.message.includes('network') || error.message.includes('fetch')) {
                    errorMessage = 'Koneksi bermasalah. Periksa koneksi internet Anda.';
                } else if (error.message) {
                    errorMessage = error.message;
                }
                
                this.showToast(errorMessage, 'error');
                this.restoreButtons(deleteButtons, originalContents);
                
            } finally {
                // Clean up
                this.deletingProducts.delete(productId);
                this.isDeleting = this.deletingProducts.size > 0;
            }
        },

        /**
         * Remove product from DOM with smooth animation
         */
        removeProductFromDOM(productId, productName) {
            const desktopRow = document.getElementById(`product-row-${productId}`);
            const mobileItem = document.getElementById(`product-mobile-${productId}`);
            
            const elementsToRemove = [desktopRow, mobileItem].filter(el => el);
            
            if (elementsToRemove.length === 0) {
                console.warn(`Product elements not found for ID: ${productId}`);
                return;
            }

            elementsToRemove.forEach(element => {
                if (element) {
                    // Add transition classes
                    element.style.transition = 'all 0.3s ease';
                    element.style.opacity = '0';
                    element.style.transform = 'translateX(-20px) scale(0.95)';
                    
                    // Remove from DOM after animation
                    setTimeout(() => {
                        if (element && element.parentNode) {
                            element.parentNode.removeChild(element);
                        }
                    }, 300);
                }
            });

            // Check if this was the last product
            setTimeout(() => {
                const remainingProducts = document.querySelectorAll('[id^="product-row-"], [id^="product-mobile-"]');
                if (remainingProducts.length === 0) {
                    // Reload page to show empty state
                    setTimeout(() => {
                        window.location.reload();
                    }, 500);
                }
            }, 350);
        },

        /**
         * Restore button states after error
         */
        restoreButtons(buttons, originalContents) {
            buttons.forEach(button => {
                const originalContent = originalContents.get(button);
                if (originalContent) {
                    button.innerHTML = originalContent;
                }
                button.disabled = false;
                button.classList.remove('opacity-50', 'cursor-not-allowed');
            });
        },

        /**
         * Show toast notification
         */
        showToast(message, type = 'info') {
            // Check if global showToast function exists
            if (typeof window.showToast === 'function') {
                window.showToast(message, type);
                return;
            }

            // Fallback: simple alert
            if (type === 'error') {
                alert('❌ ' + message);
            } else if (type === 'success') {
                alert('✅ ' + message);
            } else {
                alert(message);
            }
        },

        /**
         * Refresh page with current filters
         */
        refreshPage() {
            window.location.reload();
        }
    }
}

// Global error handler for unhandled promise rejections
window.addEventListener('unhandledrejection', event => {
    console.error('Unhandled promise rejection:', event.reason);
    // Prevent the default browser behavior
    event.preventDefault();
});
</script>
@endpush
@endsection