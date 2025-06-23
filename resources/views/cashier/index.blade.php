@extends('layouts.app')

@section('title', 'Kasir - Takoyaki POS')

@section('content')
<div class="h-screen pt-10 pb-20 lg:pb-0 bg-gray-50" x-data="cashier()">
    <!-- Product Selection (Full Width) -->
    <div class="h-full overflow-y-auto">
        <div class="p-3 lg:p-4">
            <!-- Header Section -->
            <div class="mb-4 lg:mb-6">
                <h1 class="text-xl lg:text-2xl font-bold text-gray-800 mb-1 lg:mb-2">Kasir POS</h1>
                <p class="text-sm lg:text-base text-gray-600">Pilih produk untuk memulai transaksi</p>
            </div>

            <!-- Search Bar -->
            <div class="mb-3 lg:mb-4">
                <div class="relative">
                    <input type="text" 
                           x-model="searchQuery"
                           placeholder="Cari produk..."
                           class="w-full pl-10 pr-4 py-2.5 lg:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 text-sm lg:text-base">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
            </div>

            <!-- Category Filter -->
            <div class="mb-4 lg:mb-6 flex space-x-2 overflow-x-auto pb-2">
                <button @click="selectedCategory = null"
                        :class="selectedCategory === null ? 'bg-red-500 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'"
                        class="px-3 lg:px-4 py-2 rounded-lg text-xs lg:text-sm font-medium whitespace-nowrap border transition-colors flex-shrink-0">
                    Semua
                </button>
                @foreach($categories as $category)
                    <button @click="selectedCategory = {{ $category->id }}"
                            :class="selectedCategory === {{ $category->id }} ? 'bg-red-500 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'"
                            class="px-3 lg:px-4 py-2 rounded-lg text-xs lg:text-sm font-medium whitespace-nowrap border transition-colors flex-shrink-0">
                        {{ $category->name }}
                    </button>
                @endforeach
            </div>

            <!-- Products Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3 lg:gap-4 pb-24 lg:pb-6">
                @foreach($products as $product)
                    <div class="bg-white rounded-xl shadow-sm border hover:shadow-lg transition-all duration-300 cursor-pointer relative overflow-hidden"
                         x-show="filterProduct({{ $product->id }}, '{{ strtolower($product->name) }}', {{ $product->category_id ?? 'null' }})"
                         @click="addToCart({{ $product->id }})">
                        
                        <!-- Product Image -->
                        <div class="aspect-square bg-gray-100 overflow-hidden">
                            @if($product->image)
                                <img src="{{ $product->image_url }}" 
                                     alt="{{ $product->name }}"
                                     class="w-full h-full object-cover hover:scale-105 transition-transform duration-300"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div class="w-full h-full flex items-center justify-center" style="display: none;">
                                    <i class="fas fa-image text-gray-300 text-2xl lg:text-3xl"></i>
                                </div>
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <i class="fas fa-image text-gray-300 text-2xl lg:text-3xl"></i>
                                </div>
                            @endif
                        </div>

                        <!-- Product Info -->
                        <div class="p-2 lg:p-3">
                            <h3 class="font-semibold text-gray-800 text-xs lg:text-sm mb-1 line-clamp-2">{{ $product->name }}</h3>
                            <p class="text-xs text-gray-500 mb-2">{{ $product->category->name ?? 'No Category' }}</p>
                            <div class="flex items-center justify-between">
                                <span class="text-xs lg:text-sm font-bold text-red-600">
                                    Rp {{ number_format($product->selling_price, 0, ',', '.') }}
                                </span>
                                <div class="w-6 h-6 lg:w-8 lg:h-8 bg-red-500 rounded-full flex items-center justify-center text-white hover:bg-red-600 transition-colors">
                                    <i class="fas fa-plus text-xs"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Add Animation -->
                        <div class="absolute inset-0 bg-red-500 bg-opacity-20 rounded-xl opacity-0 flex items-center justify-center transition-opacity duration-200"
                             :class="{ 'opacity-100': addingProduct === {{ $product->id }} }">
                            <div class="bg-white rounded-full p-2">
                                <i class="fas fa-check text-red-500 text-lg lg:text-xl"></i>
                            </div>
                        </div>
                    </div>
                @endforeach

                <!-- No Products Message -->
                @if($products->count() === 0)
                    <div class="col-span-full text-center py-12">
                        <i class="fas fa-box text-4xl lg:text-6xl text-gray-300 mb-4"></i>
                        <h3 class="text-base lg:text-lg font-medium text-gray-900 mb-2">Belum ada produk</h3>
                        <p class="text-sm lg:text-base text-gray-500 mb-4">Tambahkan produk terlebih dahulu</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Floating Cart Button (Mobile Only) -->
    <div class="fixed bottom-20 lg:bottom-6 right-4 lg:right-6 z-50">
        <button @click="showCartModal = true"
                class="relative bg-red-500 hover:bg-red-600 text-white rounded-full w-12 h-12 lg:w-14 lg:h-14 flex items-center justify-center shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
            <i class="fas fa-shopping-cart text-lg lg:text-xl"></i>
            
            <!-- Cart Counter Badge -->
            <div x-show="cart.length > 0" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-0"
                 x-transition:enter-end="opacity-100 scale-100"
                 class="absolute -top-1 -right-1 lg:-top-2 lg:-right-2 bg-yellow-400 text-red-800 rounded-full w-5 h-5 lg:w-6 lg:h-6 flex items-center justify-center text-xs font-bold"
                 x-text="cart.length">
            </div>
            
            <!-- Pulse Animation when items added -->
            <div x-show="cartPulse" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:leave="transition ease-in duration-300"
                 class="absolute inset-0 bg-red-400 rounded-full animate-ping">
            </div>
        </button>
    </div>

    <!-- Cart Modal -->
    <div x-show="showCartModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-end lg:items-center justify-center p-0 lg:p-4"
         style="display: none;"
         @click.self="showCartModal = false">
        
        <!-- Modal Content - Full screen on mobile, centered on desktop -->
        <div class="bg-white w-full h-full lg:w-96 lg:max-w-md lg:h-auto lg:max-h-[90vh] lg:rounded-xl overflow-hidden flex flex-col"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="transform translate-y-full lg:translate-y-0 lg:scale-95 opacity-0"
             x-transition:enter-end="transform translate-y-0 lg:scale-100 opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="transform translate-y-0 lg:scale-100 opacity-100"
             x-transition:leave-end="transform translate-y-full lg:translate-y-0 lg:scale-95 opacity-0">
            
            <!-- Modal Header -->
            <div class="p-4 border-b border-gray-200 bg-red-500 text-white flex-shrink-0">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold">Keranjang Belanja</h3>
                    <button @click="showCartModal = false" 
                            class="p-1 hover:bg-red-600 rounded-full transition-colors">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
                <p class="text-red-100 text-sm mt-1" x-text="cart.length + ' item dalam keranjang'"></p>
            </div>

            <!-- Cart Items - Scrollable Area -->
            <div class="flex-1 overflow-y-auto min-h-0">
                <!-- Empty Cart -->
                <div x-show="cart.length === 0" class="p-8 text-center text-gray-500 h-full flex flex-col justify-center">
                    <i class="fas fa-shopping-cart text-4xl mb-4 text-gray-300"></i>
                    <p class="font-medium mb-2">Keranjang masih kosong</p>
                    <p class="text-sm">Pilih produk untuk memulai belanja</p>
                    <button @click="showCartModal = false" 
                            class="mt-4 bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm mx-auto">
                        Mulai Belanja
                    </button>
                </div>

                <!-- Cart Items List -->
                <div x-show="cart.length > 0" class="p-4 space-y-3">
                    <template x-for="(item, index) in cart" :key="'cart-' + index">
                        <div class="bg-gray-50 rounded-lg p-3 border border-gray-200"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 transform scale-95"
                             x-transition:enter-end="opacity-100 transform scale-100">
                            
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1 pr-3">
                                    <h4 class="font-medium text-gray-800 text-sm mb-1" x-text="item.name"></h4>
                                    <p class="text-xs text-gray-500" x-text="'Rp ' + formatNumber(item.price) + ' per item'"></p>
                                </div>
                                <button @click="removeFromCart(index)" 
                                        class="text-red-500 hover:text-red-700 p-1 hover:bg-red-50 rounded transition-colors">
                                    <i class="fas fa-trash text-sm"></i>
                                </button>
                            </div>
                            
                            <!-- Quantity Controls -->
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3 bg-white rounded-lg border border-gray-300">
                                    <button @click="updateQuantity(index, item.quantity - 1)"
                                            class="p-2 hover:bg-gray-50 text-gray-600 transition-colors touch-manipulation">
                                        <i class="fas fa-minus text-xs"></i>
                                    </button>
                                    <span class="text-sm font-medium px-2 min-w-[2rem] text-center" x-text="item.quantity"></span>
                                    <button @click="updateQuantity(index, item.quantity + 1)"
                                            class="p-2 hover:bg-gray-50 text-gray-600 transition-colors touch-manipulation">
                                        <i class="fas fa-plus text-xs"></i>
                                    </button>
                                </div>
                                <div class="text-right">
                                    <div class="font-semibold text-gray-800 text-sm" x-text="'Rp ' + formatNumber(item.total)"></div>
                                    <div class="text-xs text-gray-500" x-text="item.quantity + ' x Rp ' + formatNumber(item.price)"></div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Cart Footer - Fixed at bottom -->
            <div x-show="cart.length > 0" class="border-t border-gray-200 p-4 bg-gray-50 flex-shrink-0">
                
                <!-- Discount Selection -->
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Diskon:</label>
                    <select x-model="selectedDiscount" 
                            @change="calculateTotals()"
                            class="w-full p-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500">
                        <option value="">Pilih Diskon</option>
                        @foreach($discountTemplates as $discount)
                            <option value="{{ $discount->percentage }}">{{ $discount->display_name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Tax Selection -->
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pajak:</label>
                    <select x-model="selectedTax" 
                            @change="calculateTotals()"
                            class="w-full p-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500">
                        @foreach($taxTemplates as $tax)
                            <option value="{{ $tax->percentage }}" {{ $tax->percentage == 0 ? 'selected' : '' }}>
                                {{ $tax->display_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Payment Method Selection -->
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Metode Pembayaran:</label>
                    <div class="grid grid-cols-3 gap-2">
                        <button @click="paymentMethod = 'cash'"
                                :class="paymentMethod === 'cash' ? 'bg-red-500 text-white border-red-500' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'"
                                class="p-2.5 rounded-lg text-xs font-medium transition-colors border touch-manipulation">
                            <i class="fas fa-money-bill-wave mb-1 block"></i>
                            Tunai
                        </button>
                        <button @click="paymentMethod = 'card'"
                                :class="paymentMethod === 'card' ? 'bg-red-500 text-white border-red-500' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'"
                                class="p-2.5 rounded-lg text-xs font-medium transition-colors border touch-manipulation">
                            <i class="fas fa-credit-card mb-1 block"></i>
                            Kartu
                        </button>
                        <button @click="paymentMethod = 'digital'"
                                :class="paymentMethod === 'digital' ? 'bg-red-500 text-white border-red-500' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'"
                                class="p-2.5 rounded-lg text-xs font-medium transition-colors border touch-manipulation">
                            <i class="fas fa-mobile-alt mb-1 block"></i>
                            E-Wallet
                        </button>
                    </div>
                </div>

                <!-- Total Breakdown -->
                <div class="space-y-2 mb-3 p-3 bg-white rounded-lg border">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Subtotal:</span>
                        <span x-text="'Rp ' + formatNumber(subtotal)"></span>
                    </div>
                    <div x-show="discountAmount > 0" class="flex justify-between text-sm text-green-600">
                        <span>Diskon (<span x-text="selectedDiscount + '%'"></span>):</span>
                        <span x-text="'- Rp ' + formatNumber(discountAmount)"></span>
                    </div>
                    <div x-show="taxAmount > 0" class="flex justify-between text-sm text-blue-600">
                        <span>Pajak (<span x-text="selectedTax + '%'"></span>):</span>
                        <span x-text="'+ Rp ' + formatNumber(taxAmount)"></span>
                    </div>
                    <hr class="border-gray-200">
                    <div class="flex justify-between items-center font-bold">
                        <span class="text-gray-800">Total:</span>
                        <span class="text-red-600 text-lg" x-text="'Rp ' + formatNumber(finalTotal)"></span>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="space-y-2">
                    <button @click="processPayment()"
                            :disabled="processing"
                            class="w-full bg-red-500 hover:bg-red-600 disabled:bg-gray-400 disabled:cursor-not-allowed text-white font-semibold py-3 rounded-lg transition-colors touch-manipulation">
                        <span x-show="!processing">
                            <i class="fas fa-credit-card mr-2"></i>Proses Pembayaran
                        </span>
                        <span x-show="processing">
                            <i class="fas fa-spinner fa-spin mr-2"></i>Memproses...
                        </span>
                    </button>

                    <button @click="clearCart()"
                            x-show="cart.length > 0 && !processing"
                            class="w-full bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 rounded-lg transition-colors text-sm touch-manipulation">
                        <i class="fas fa-trash mr-2"></i>Kosongkan Keranjang
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div x-show="showSuccessModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
         style="display: none;">
        
        <div class="bg-white rounded-xl p-6 max-w-sm w-full text-center"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="transform scale-95 opacity-0"
             x-transition:enter-end="transform scale-100 opacity-100">
            
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-check text-green-500 text-2xl"></i>
            </div>
            
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Pembayaran Berhasil!</h3>
            <p class="text-gray-600 text-sm mb-4" x-text="successMessage"></p>
            
            <button @click="closeSuccessModal()"
                    class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-lg font-medium touch-manipulation">
                Selesai
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
function cashier() {
    return {
        // Data
        cart: [],
        products: @json($products->values()),
        searchQuery: '',
        selectedCategory: null,
        paymentMethod: 'cash',
        processing: false,
        
        // Tax & Discount
        selectedDiscount: '',
        selectedTax: '0', // Default no tax
        subtotal: 0,
        discountAmount: 0,
        taxAmount: 0,
        finalTotal: 0,
        
        // UI States
        showCartModal: false,
        showSuccessModal: false,
        addingProduct: null,
        cartPulse: false,
        successMessage: '',

        // Computed values
        get total() {
            return this.cart.reduce((sum, item) => {
                const itemTotal = parseFloat(item.total) || 0;
                return sum + itemTotal;
            }, 0);
        },

        // Methods
        init() {
            console.log('Cashier initialized with', this.products.length, 'products');
            
            // Validate product data
            this.products = this.products.map(product => ({
                ...product,
                selling_price: parseFloat(product.selling_price) || 0
            }));
            
            this.calculateTotals();
            
            if (this.products.length > 0) {
                showToast(`${this.products.length} produk siap`, 'success');
            }

            // Prevent body scroll when modal is open on mobile
            this.$watch('showCartModal', (value) => {
                if (value) {
                    document.body.style.overflow = 'hidden';
                } else {
                    document.body.style.overflow = '';
                }
            });

            // Watch cart changes and recalculate
            this.$watch('cart', () => {
                this.calculateTotals();
            }, { deep: true });
        },

        calculateTotals() {
            // Ensure subtotal is a valid number
            this.subtotal = this.total || 0;
            
            // Calculate discount with safety checks
            const discountPercent = parseFloat(this.selectedDiscount) || 0;
            this.discountAmount = (this.subtotal * discountPercent) / 100;
            
            const afterDiscount = this.subtotal - this.discountAmount;
            
            // Calculate tax with safety checks
            const taxPercent = parseFloat(this.selectedTax) || 0;
            this.taxAmount = (afterDiscount * taxPercent) / 100;
            
            this.finalTotal = afterDiscount + this.taxAmount;

            // Ensure all values are numbers, not NaN
            this.subtotal = isNaN(this.subtotal) ? 0 : this.subtotal;
            this.discountAmount = isNaN(this.discountAmount) ? 0 : this.discountAmount;
            this.taxAmount = isNaN(this.taxAmount) ? 0 : this.taxAmount;
            this.finalTotal = isNaN(this.finalTotal) ? 0 : this.finalTotal;

            console.log('Totals calculated:', {
                subtotal: this.subtotal,
                discountAmount: this.discountAmount,
                taxAmount: this.taxAmount,
                finalTotal: this.finalTotal
            });
        },

        filterProduct(productId, productName, categoryId) {
            const matchesSearch = !this.searchQuery || 
                productName.includes(this.searchQuery.toLowerCase());
            const matchesCategory = this.selectedCategory === null || 
                categoryId === this.selectedCategory;
            
            return matchesSearch && matchesCategory;
        },

        addToCart(productId) {
            const product = this.products.find(p => p.id == productId);
            if (!product) {
                showToast('Produk tidak ditemukan!', 'error');
                return;
            }

            // Ensure product price is a valid number
            const price = parseFloat(product.selling_price) || 0;
            if (price <= 0) {
                showToast('Harga produk tidak valid!', 'error');
                return;
            }

            // Add animation effect
            this.addingProduct = productId;
            setTimeout(() => {
                this.addingProduct = null;
            }, 600);

            // Cart pulse effect
            this.cartPulse = true;
            setTimeout(() => {
                this.cartPulse = false;
            }, 300);

            const existingItem = this.cart.find(item => item.product_id == productId);
            
            if (existingItem) {
                existingItem.quantity = parseInt(existingItem.quantity) + 1;
                existingItem.total = existingItem.quantity * parseFloat(existingItem.price);
                showToast(`${product.name} +1`, 'success');
            } else {
                this.cart.push({
                    product_id: productId,
                    name: product.name,
                    price: price,
                    quantity: 1,
                    total: price
                });
                showToast(`${product.name} ditambahkan`, 'success');
            }
            
            console.log('Cart after adding:', this.cart);
        },

        removeFromCart(index) {
            const item = this.cart[index];
            this.cart.splice(index, 1);
            showToast(`${item.name} dihapus`, 'info');
        },

        updateQuantity(index, newQuantity) {
            newQuantity = parseInt(newQuantity) || 0;
            
            if (newQuantity <= 0) {
                this.removeFromCart(index);
                return;
            }
            
            const item = this.cart[index];
            const price = parseFloat(item.price) || 0;
            
            item.quantity = newQuantity;
            item.total = newQuantity * price;
            
            console.log('Updated item:', item);
        },

        clearCart() {
            if (this.cart.length === 0) return;
            
            if (confirm('Yakin ingin mengosongkan keranjang?')) {
                this.cart = [];
                this.selectedDiscount = '';
                this.selectedTax = '0';
                showToast('Keranjang dikosongkan', 'info');
            }
        },

        async processPayment() {
            if (this.cart.length === 0) {
                showToast('Keranjang masih kosong', 'error');
                return;
            }

            this.processing = true;

            try {
                const response = await fetch('{{ route("cashier.process") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        cart: this.cart,
                        discount_percentage: parseFloat(this.selectedDiscount) || 0,
                        tax_percentage: parseFloat(this.selectedTax) || 0,
                        payment_method: this.paymentMethod
                    })
                });

                const result = await response.json();

                if (result.success) {
                    this.successMessage = `Transaksi ${result.transaction.code} berhasil diproses dengan total Rp ${this.formatNumber(this.finalTotal)}`;
                    
                    // Close cart modal and show success
                    this.showCartModal = false;
                    this.showSuccessModal = true;
                    
                    // Clear cart
                    this.cart = [];
                    this.paymentMethod = 'cash';
                    this.selectedDiscount = '';
                    this.selectedTax = '0';
                    
                    showToast(result.message, 'success');
                } else {
                    showToast(result.message || 'Terjadi kesalahan', 'error');
                }
            } catch (error) {
                console.error('Payment error:', error);
                showToast('Terjadi kesalahan sistem', 'error');
            } finally {
                this.processing = false;
            }
        },

        closeSuccessModal() {
            this.showSuccessModal = false;
            this.successMessage = '';
            // Restore body scroll
            document.body.style.overflow = '';
        },

        formatNumber(number) {
            const num = parseFloat(number);
            if (isNaN(num)) return '0';
            return new Intl.NumberFormat('id-ID').format(num);
        }
    }
}
</script>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Better touch targets for mobile */
@media (max-width: 768px) {
    .touch-manipulation {
        touch-action: manipulation;
        -webkit-touch-callout: none;
        -webkit-user-select: none;
        user-select: none;
    }
    
    /* Increase touch target size on mobile */
    button, .touch-manipulation {
        min-height: 44px;
        min-width: 44px;
    }
}

/* Prevent viewport zoom on iOS */
input[type="text"], 
input[type="number"], 
select {
    font-size: 16px; /* Prevents zoom on iOS */
}

@keyframes bounce-in {
    0% { transform: scale(0.3); opacity: 0; }
    50% { transform: scale(1.05); }
    70% { transform: scale(0.9); }
    100% { transform: scale(1); opacity: 1; }
}

.animate-bounce-in {
    animation: bounce-in 0.6s ease-out;
}

/* Safe area for iPhone */
@supports (padding: max(0px)) {
    .safe-bottom {
        padding-bottom: max(1rem, env(safe-area-inset-bottom));
    }
}
</style>
@endpush
@endsection