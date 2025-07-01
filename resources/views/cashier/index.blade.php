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
                class="relative bg-red-500 hover:bg-red-600 text-white rounded-full w-12 h-12 lg:w-14 lg:h-14 flex items-center justify-center shadow-lg hover:shadow-xl transition-all duration-300">
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
        
        <!-- Modal Content - Optimized for mobile height -->
        <div class="bg-white w-full h-full lg:w-96 lg:max-w-md lg:h-auto lg:max-h-[90vh] lg:rounded-xl overflow-hidden flex flex-col"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="transform translate-y-full lg:translate-y-0 lg:scale-95 opacity-0"
             x-transition:enter-end="transform translate-y-0 lg:scale-100 opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="transform translate-y-0 lg:scale-100 opacity-100"
             x-transition:leave-end="transform translate-y-full lg:translate-y-0 lg:scale-95 opacity-0">
            
            <!-- Modal Header - Compact -->
            <div class="p-3 border-b border-gray-200 bg-red-500 text-white flex-shrink-0">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold">Keranjang</h3>
                    <button @click="showCartModal = false" 
                            class="p-1 hover:bg-red-600 rounded-full transition-colors">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
                <p class="text-red-100 text-xs mt-1" x-text="cart.length + ' item'"></p>
            </div>

            <!-- Cart Items - Scrollable Area with better mobile sizing -->
            <div class="flex-1 overflow-y-auto min-h-0">
                <!-- Empty Cart -->
                <div x-show="cart.length === 0" class="p-6 text-center text-gray-500 h-full flex flex-col justify-center">
                    <i class="fas fa-shopping-cart text-3xl mb-3 text-gray-300"></i>
                    <p class="font-medium mb-1">Keranjang kosong</p>
                    <p class="text-xs">Pilih produk untuk belanja</p>
                    <button @click="showCartModal = false" 
                            class="mt-3 bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm mx-auto">
                        Mulai Belanja
                    </button>
                </div>

                <!-- Cart Items List - Compact untuk mobile -->
                <div x-show="cart.length > 0" class="p-3 space-y-2">
                    <template x-for="(item, index) in cart" :key="'cart-' + index">
                        <div class="bg-gray-50 rounded-lg p-2.5 border border-gray-200"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 transform scale-95"
                             x-transition:enter-end="opacity-100 transform scale-100">
                            
                            <div class="flex items-start justify-between mb-2">
                                <div class="flex-1 pr-2 min-w-0">
                                    <h4 class="font-medium text-gray-800 text-sm mb-1 truncate" x-text="item.name"></h4>
                                    <p class="text-xs text-gray-500" x-text="'Rp ' + formatNumber(item.price)"></p>
                                </div>
                                <button @click="removeFromCart(index)" 
                                        class="text-red-500 hover:text-red-700 p-1 hover:bg-red-50 rounded transition-colors flex-shrink-0">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            </div>
                            
                            <!-- Quantity Controls - Compact -->
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2 bg-white rounded-lg border border-gray-300">
                                    <button @click="updateQuantity(index, item.quantity - 1)"
                                            class="p-1.5 hover:bg-gray-50 text-gray-600 transition-colors touch-manipulation">
                                        <i class="fas fa-minus text-xs"></i>
                                    </button>
                                    <span class="text-sm font-medium px-2 min-w-[1.5rem] text-center" x-text="item.quantity"></span>
                                    <button @click="updateQuantity(index, item.quantity + 1)"
                                            class="p-1.5 hover:bg-gray-50 text-gray-600 transition-colors touch-manipulation">
                                        <i class="fas fa-plus text-xs"></i>
                                    </button>
                                </div>
                                <div class="text-right">
                                    <div class="font-semibold text-gray-800 text-sm" x-text="'Rp ' + formatNumber(item.total)"></div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Cart Footer - Compact and fixed at bottom -->
            <div x-show="cart.length > 0" class="border-t border-gray-200 p-3 bg-gray-50 flex-shrink-0 max-h-[60vh] overflow-y-auto">
                
                <!-- Settings Section - Collapsible untuk mobile -->
                <div class="mb-3">
                    <button @click="showSettings = !showSettings" 
                            class="w-full flex items-center justify-between p-2 bg-white rounded-lg border text-sm font-medium text-gray-700 lg:hidden">
                        <span>Pengaturan Transaksi</span>
                        <i class="fas fa-chevron-down transition-transform" :class="{ 'rotate-180': showSettings }"></i>
                    </button>
                    
                    <!-- Settings Content -->
                    <div x-show="showSettings || window.innerWidth >= 1024" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 transform -translate-y-2"
                         x-transition:enter-end="opacity-100 transform translate-y-0"
                         class="space-y-2 mt-2 lg:mt-0">
                        
                        <!-- Discount Selection - Compact -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Diskon:</label>
                            <select x-model="selectedDiscount" 
                                    @change="calculateTotals()"
                                    class="w-full p-2 border border-gray-300 rounded-lg text-xs focus:ring-2 focus:ring-red-500 focus:border-red-500">
                                <option value="">Tanpa Diskon</option>
                                @foreach($discountTemplates as $discount)
                                    <option value="{{ $discount->percentage }}">{{ $discount->display_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Tax Selection - Compact -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Pajak:</label>
                            <select x-model="selectedTax" 
                                    @change="calculateTotals()"
                                    class="w-full p-2 border border-gray-300 rounded-lg text-xs focus:ring-2 focus:ring-red-500 focus:border-red-500">
                                @foreach($taxTemplates as $tax)
                                    <option value="{{ $tax->percentage }}" {{ $tax->percentage == 0 ? 'selected' : '' }}>
                                        {{ $tax->display_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Payment Method Selection - Grid layout optimized -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Pembayaran:</label>
                            <div class="grid grid-cols-3 gap-1.5">
                                <!-- Tunai -->
                                <button @click="setPaymentMethod('cash')"
                                        :class="paymentMethod === 'cash' ? 'bg-green-500 text-white border-green-500' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'"
                                        class="p-2 rounded-lg text-xs font-medium transition-colors border touch-manipulation">
                                    <i class="fas fa-money-bill-wave mb-1 block text-xs"></i>
                                    <span class="block text-xs">Tunai</span>
                                </button>
                                <!-- DANA -->
                                <button @click="setPaymentMethod('dana')"
                                        :class="paymentMethod === 'dana' ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'"
                                        class="p-2 rounded-lg text-xs font-medium transition-colors border touch-manipulation">
                                    <i class="fas fa-mobile-alt mb-1 block text-xs"></i>
                                    <span class="block text-xs">DANA</span>
                                </button>
                                <!-- GoPay -->
                                <button @click="setPaymentMethod('gopay')"
                                        :class="paymentMethod === 'gopay' ? 'bg-green-600 text-white border-green-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'"
                                        class="p-2 rounded-lg text-xs font-medium transition-colors border touch-manipulation">
                                    <i class="fas fa-wallet mb-1 block text-xs"></i>
                                    <span class="block text-xs">GoPay</span>
                                </button>
                                <!-- OVO -->
                                <button @click="setPaymentMethod('ovo')"
                                        :class="paymentMethod === 'ovo' ? 'bg-purple-500 text-white border-purple-500' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'"
                                        class="p-2 rounded-lg text-xs font-medium transition-colors border touch-manipulation">
                                    <i class="fas fa-coins mb-1 block text-xs"></i>
                                    <span class="block text-xs">OVO</span>
                                </button>
                                <!-- Kartu -->
                                <button @click="setPaymentMethod('card')"
                                        :class="paymentMethod === 'card' ? 'bg-blue-500 text-white border-blue-500' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'"
                                        class="p-2 rounded-lg text-xs font-medium transition-colors border touch-manipulation">
                                    <i class="fas fa-credit-card mb-1 block text-xs"></i>
                                    <span class="block text-xs">Kartu</span>
                                </button>
                                <!-- E-Wallet -->
                                <button @click="setPaymentMethod('digital')"
                                        :class="paymentMethod === 'digital' ? 'bg-indigo-500 text-white border-indigo-500' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'"
                                        class="p-2 rounded-lg text-xs font-medium transition-colors border touch-manipulation">
                                    <i class="fas fa-qrcode mb-1 block text-xs"></i>
                                    <span class="block text-xs">E-Wallet</span>
                                </button>
                            </div>
                        </div>

                        <!-- Customer Money Input (hanya untuk cash) - Compact -->
                        <div x-show="paymentMethod === 'cash'">
                            <label class="block text-xs font-medium text-gray-700 mb-1">Uang Customer:</label>
                            <input type="number" 
                                   x-model="customerMoney"
                                   @input="calculateChange()"
                                   :placeholder="'Min Rp ' + formatNumber(finalTotal)"
                                   class="w-full p-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500">
                            
                            <!-- Quick Cash Buttons - Compact -->
                            <div class="grid grid-cols-3 gap-1 mt-1">
                                <button @click="setCustomerMoney(finalTotal)"
                                        class="p-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded text-xs font-medium transition-colors">
                                    Pas
                                </button>
                                <button @click="setCustomerMoney(getQuickAmount(finalTotal, 5000))"
                                        class="p-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded text-xs font-medium transition-colors"
                                        x-text="'+5K'">
                                </button>
                                <button @click="setCustomerMoney(getQuickAmount(finalTotal, 10000))"
                                        class="p-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded text-xs font-medium transition-colors"
                                        x-text="'+10K'">
                                </button>
                            </div>
                            
                            <!-- Kembalian Display - Compact -->
                            <div x-show="changeAmount >= 0 && customerMoney > 0" class="mt-2 p-2 bg-green-50 border border-green-200 rounded-lg">
                                <div class="flex justify-between items-center">
                                    <span class="text-xs font-medium text-green-800">Kembalian:</span>
                                    <span class="text-sm font-bold text-green-600" x-text="'Rp ' + formatNumber(changeAmount)"></span>
                                </div>
                            </div>
                            
                            <!-- Error jika uang kurang - Compact -->
                            <div x-show="customerMoney > 0 && customerMoney < finalTotal" class="mt-2 p-2 bg-red-50 border border-red-200 rounded-lg">
                                <div class="text-xs text-red-600">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                    Kurang Rp <span x-text="formatNumber(finalTotal - customerMoney)"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Breakdown - Always visible -->
                <div class="space-y-1 mb-3 p-2.5 bg-white rounded-lg border">
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-600">Subtotal:</span>
                        <span x-text="'Rp ' + formatNumber(subtotal)"></span>
                    </div>
                    <div x-show="discountAmount > 0" class="flex justify-between text-xs text-green-600">
                        <span>Diskon (<span x-text="selectedDiscount + '%'"></span>):</span>
                        <span x-text="'- Rp ' + formatNumber(discountAmount)"></span>
                    </div>
                    <div x-show="taxAmount > 0" class="flex justify-between text-xs text-blue-600">
                        <span>Pajak (<span x-text="selectedTax + '%'"></span>):</span>
                        <span x-text="'+ Rp ' + formatNumber(taxAmount)"></span>
                    </div>
                    <hr class="border-gray-200">
                    <div class="flex justify-between items-center font-bold">
                        <span class="text-gray-800 text-sm">Total:</span>
                        <span class="text-red-600 text-base" x-text="'Rp ' + formatNumber(finalTotal)"></span>
                    </div>
                </div>
                
                <!-- Action Buttons - Always visible at bottom -->
                <div class="space-y-2">
                    <button @click="processPayment()"
                            :disabled="processing || !canProcessPayment()"
                            :class="!canProcessPayment() ? 'bg-gray-400 cursor-not-allowed' : 'bg-red-500 hover:bg-red-600'"
                            class="w-full disabled:bg-gray-400 disabled:cursor-not-allowed text-white font-semibold py-2.5 rounded-lg transition-colors touch-manipulation text-sm">
                        <span x-show="!processing">
                            <i class="fas fa-credit-card mr-2"></i>Proses Pembayaran
                        </span>
                        <span x-show="processing">
                            <i class="fas fa-spinner fa-spin mr-2"></i>Memproses...
                        </span>
                    </button>

                    <button @click="clearCart()"
                            x-show="cart.length > 0 && !processing"
                            class="w-full bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 rounded-lg transition-colors text-xs touch-manipulation">
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
            
            <!-- Change Display untuk cash -->
            <div x-show="lastTransactionChange > 0" class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg">
                <div class="text-sm font-medium text-green-800 mb-1">Kembalian:</div>
                <div class="text-lg font-bold text-green-600" x-text="'Rp ' + formatNumber(lastTransactionChange)"></div>
            </div>
            
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
        
        // Cash Payment
        customerMoney: 0,
        changeAmount: 0,
        lastTransactionChange: 0,
        
        // UI States
        showCartModal: false,
        showSuccessModal: false,
        showSettings: false, // For mobile collapsible settings
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
                    this.showSettings = false; // Reset settings when closing
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

            // Recalculate change if cash payment
            if (this.paymentMethod === 'cash') {
                this.calculateChange();
            }

            console.log('Totals calculated:', {
                subtotal: this.subtotal,
                discountAmount: this.discountAmount,
                taxAmount: this.taxAmount,
                finalTotal: this.finalTotal
            });
        },

        setPaymentMethod(method) {
            this.paymentMethod = method;
            
            // Reset customer money dan change jika bukan cash
            if (method !== 'cash') {
                this.customerMoney = 0;
                this.changeAmount = 0;
            } else {
                this.calculateChange();
            }
        },

        calculateChange() {
            if (this.paymentMethod === 'cash') {
                const money = parseFloat(this.customerMoney) || 0;
                this.changeAmount = Math.max(0, money - this.finalTotal);
            } else {
                this.changeAmount = 0;
            }
        },

        setCustomerMoney(amount) {
            this.customerMoney = amount;
            this.calculateChange();
        },

        getQuickAmount(total, roundTo) {
            return Math.ceil(total / roundTo) * roundTo;
        },

        canProcessPayment() {
            if (this.paymentMethod === 'cash') {
                const money = parseFloat(this.customerMoney) || 0;
                return money >= this.finalTotal;
            }
            return true; // Non-cash payments don't need validation
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
                this.customerMoney = 0;
                this.changeAmount = 0;
                showToast('Keranjang dikosongkan', 'info');
            }
        },

        async processPayment() {
            if (this.cart.length === 0) {
                showToast('Keranjang masih kosong', 'error');
                return;
            }

            if (!this.canProcessPayment()) {
                showToast('Uang customer tidak mencukupi', 'error');
                return;
            }

            this.processing = true;

            try {
                const paymentData = {
                    cart: this.cart,
                    discount_percentage: parseFloat(this.selectedDiscount) || 0,
                    tax_percentage: parseFloat(this.selectedTax) || 0,
                    payment_method: this.paymentMethod
                };

                // Tambahkan data cash jika payment method cash
                if (this.paymentMethod === 'cash') {
                    paymentData.customer_money = parseFloat(this.customerMoney) || 0;
                    paymentData.change_amount = this.changeAmount;
                }

                const response = await fetch('{{ route("cashier.process") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(paymentData)
                });

                const result = await response.json();

                if (result.success) {
                    this.successMessage = `Transaksi ${result.transaction.code} berhasil diproses dengan total Rp ${this.formatNumber(this.finalTotal)}`;
                    
                    // Store change amount untuk ditampilkan di success modal
                    this.lastTransactionChange = this.changeAmount;
                    
                    // Close cart modal and show success
                    this.showCartModal = false;
                    this.showSuccessModal = true;
                    
                    // Clear cart
                    this.cart = [];
                    this.paymentMethod = 'cash';
                    this.selectedDiscount = '';
                    this.selectedTax = '0';
                    this.customerMoney = 0;
                    this.changeAmount = 0;
                    
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
            this.lastTransactionChange = 0;
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

/* Fix for mobile viewport height */
@media (max-width: 1024px) {
    .h-full {
        height: 100vh;
        height: 100dvh; /* Dynamic viewport height for mobile */
    }
}
</style>
@endpush
@endsection