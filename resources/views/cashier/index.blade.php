@extends('layouts.app')

@section('title', 'Kasir - Takoyaki POS')

@section('content')
<div class="flex h-screen pt-16 pb-20 lg:pb-0" x-data="cashier()">
    <!-- Product Selection (Left Side) -->
    <div class="flex-1 bg-gray-50 overflow-y-auto">
        <div class="p-4">
            <!-- Search Bar -->
            <div class="mb-4">
                <div class="relative">
                    <input type="text" 
                           x-model="searchQuery"
                           placeholder="Cari produk..."
                           class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
            </div>

            <!-- Category Filter -->
            <div class="mb-4 flex space-x-2 overflow-x-auto pb-2">
                <button @click="selectedCategory = null"
                        :class="selectedCategory === null ? 'bg-red-500 text-white' : 'bg-white text-gray-700'"
                        class="px-4 py-2 rounded-lg text-sm font-medium whitespace-nowrap border">
                    Semua
                </button>
                @foreach($categories as $category)
                    <button @click="selectedCategory = {{ $category->id }}"
                            :class="selectedCategory === {{ $category->id }} ? 'bg-red-500 text-white' : 'bg-white text-gray-700'"
                            class="px-4 py-2 rounded-lg text-sm font-medium whitespace-nowrap border">
                        {{ $category->name }}
                    </button>
                @endforeach
            </div>

            <!-- Products Grid -->
            <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @foreach($products as $product)
                    <div x-show="filterProduct({{ $product->id }}, '{{ strtolower($product->name) }}', {{ $product->category_id }})"
                         class="bg-white rounded-lg shadow-sm border hover:shadow-md transition-shadow cursor-pointer"
                         @click="addToCart({{ $product->id }})">
                        
                        <!-- Product Image -->
                        <div class="aspect-square bg-gray-100 rounded-t-lg overflow-hidden">
                            @if($product->image)
                                <img src="{{ $product->image_url }}" 
                                     alt="{{ $product->name }}"
                                     class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <i class="fas fa-image text-gray-300 text-3xl"></i>
                                </div>
                            @endif
                        </div>

                        <!-- Product Info -->
                        <div class="p-3">
                            <h3 class="font-medium text-gray-800 text-sm mb-1 truncate">{{ $product->name }}</h3>
                            <p class="text-xs text-gray-500 mb-2">{{ $product->quantity_per_serving }} pcs</p>
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-bold text-red-600">
                                    Rp {{ number_format($product->selling_price, 0, ',', '.') }}
                                </span>
                                <button class="w-8 h-8 bg-red-500 text-white rounded-full flex items-center justify-center hover:bg-red-600 transition-colors">
                                    <i class="fas fa-plus text-xs"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Cart & Checkout (Right Side) -->
    <div class="w-full lg:w-96 bg-white border-l border-gray-200 flex flex-col">
        <!-- Cart Header -->
        <div class="p-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-800">Keranjang</h2>
                <button @click="clearCart()" 
                        x-show="cart.length > 0"
                        class="text-red-600 hover:text-red-800 text-sm">
                    <i class="fas fa-trash mr-1"></i>Kosongkan
                </button>
            </div>
        </div>

        <!-- Cart Items -->
        <div class="flex-1 overflow-y-auto">
            <div x-show="cart.length === 0" class="p-8 text-center text-gray-500">
                <i class="fas fa-shopping-cart text-4xl mb-4 text-gray-300"></i>
                <p>Keranjang masih kosong</p>
                <p class="text-sm mt-1">Pilih produk untuk mulai transaksi</p>
            </div>

            <div x-show="cart.length > 0" class="p-4 space-y-3">
                <template x-for="(item, index) in cart" :key="index">
                    <div class="bg-gray-50 rounded-lg p-3">
                        <div class="flex items-start justify-between mb-2">
                            <div class="flex-1">
                                <h4 class="font-medium text-gray-800 text-sm" x-text="item.name"></h4>
                                <p class="text-xs text-gray-500" x-text="'Rp ' + formatNumber(item.price) + ' x ' + item.quantity"></p>
                            </div>
                            <button @click="removeFromCart(index)" 
                                    class="text-red-500 hover:text-red-700 ml-2">
                                <i class="fas fa-times text-sm"></i>
                            </button>
                        </div>
                        
                        <!-- Quantity Controls -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <button @click="updateQuantity(index, item.quantity - 1)"
                                        class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center hover:bg-gray-300">
                                    <i class="fas fa-minus text-xs"></i>
                                </button>
                                <span class="w-8 text-center text-sm font-medium" x-text="item.quantity"></span>
                                <button @click="updateQuantity(index, item.quantity + 1)"
                                        class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center hover:bg-gray-300">
                                    <i class="fas fa-plus text-xs"></i>
                                </button>
                            </div>
                            <span class="font-medium text-gray-800 text-sm" x-text="'Rp ' + formatNumber(item.total)"></span>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Cart Summary & Checkout -->
        <div x-show="cart.length > 0" class="border-t border-gray-200 p-4 space-y-4">
            <!-- Discount -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Diskon</label>
                <select x-model="selectedDiscount" @change="calculateTotal()" 
                        class="w-full p-2 border border-gray-300 rounded-lg text-sm">
                    <option value="0">Tanpa Diskon</option>
                    @foreach($discountTemplates as $discount)
                        <option value="{{ $discount->percentage }}">{{ $discount->display_name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Tax -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Pajak</label>
                <select x-model="selectedTax" @change="calculateTotal()" 
                        class="w-full p-2 border border-gray-300 rounded-lg text-sm">
                    @foreach($taxTemplates as $tax)
                        <option value="{{ $tax->percentage }}">{{ $tax->display_name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Payment Method -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Metode Pembayaran</label>
                <div class="grid grid-cols-3 gap-2">
                    <button @click="paymentMethod = 'cash'"
                            :class="paymentMethod === 'cash' ? 'bg-red-500 text-white' : 'bg-gray-100 text-gray-700'"
                            class="p-2 rounded-lg text-sm font-medium transition-colors">
                        <i class="fas fa-money-bill-wave mb-1 block"></i>
                        Tunai
                    </button>
                    <button @click="paymentMethod = 'card'"
                            :class="paymentMethod === 'card' ? 'bg-red-500 text-white' : 'bg-gray-100 text-gray-700'"
                            class="p-2 rounded-lg text-sm font-medium transition-colors">
                        <i class="fas fa-credit-card mb-1 block"></i>
                        Kartu
                    </button>
                    <button @click="paymentMethod = 'digital'"
                            :class="paymentMethod === 'digital' ? 'bg-red-500 text-white' : 'bg-gray-100 text-gray-700'"
                            class="p-2 rounded-lg text-sm font-medium transition-colors">
                        <i class="fas fa-mobile-alt mb-1 block"></i>
                        Digital
                    </button>
                </div>
            </div>

            <!-- Summary -->
            <div class="bg-gray-50 rounded-lg p-3 space-y-2">
                <div class="flex justify-between text-sm">
                    <span>Subtotal:</span>
                    <span x-text="'Rp ' + formatNumber(subtotal)"></span>
                </div>
                <div x-show="discountAmount > 0" class="flex justify-between text-sm text-red-600">
                    <span>Diskon (<span x-text="selectedDiscount"></span>%):</span>
                    <span x-text="'-Rp ' + formatNumber(discountAmount)"></span>
                </div>
                <div x-show="taxAmount > 0" class="flex justify-between text-sm">
                    <span>Pajak (<span x-text="selectedTax"></span>%):</span>
                    <span x-text="'Rp ' + formatNumber(taxAmount)"></span>
                </div>
                <hr>
                <div class="flex justify-between font-bold">
                    <span>Total:</span>
                    <span class="text-lg text-red-600" x-text="'Rp ' + formatNumber(total)"></span>
                </div>
            </div>

            <!-- Checkout Button -->
            <button @click="processPayment()" 
                    :disabled="processing"
                    class="w-full bg-red-500 hover:bg-red-600 disabled:bg-gray-400 text-white font-medium py-3 rounded-lg transition-colors">
                <span x-show="!processing">
                    <i class="fas fa-credit-card mr-2"></i>Proses Pembayaran
                </span>
                <span x-show="processing">
                    <i class="fas fa-spinner fa-spin mr-2"></i>Memproses...
                </span>
            </button>
        </div>
    </div>

    <!-- Receipt Modal -->
    <div x-show="showReceipt" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        
        <div class="bg-white rounded-lg max-w-md w-full max-h-[90vh] overflow-y-auto"
             @click.away="closeReceipt()">
            
            <!-- Receipt Header -->
            <div class="p-4 border-b bg-red-500 text-white text-center">
                <h3 class="text-lg font-bold">Struk Pembayaran</h3>
                <p class="text-sm opacity-90">Takoyaki POS</p>
            </div>

            <!-- Receipt Content -->
            <div class="p-4" x-show="receiptData">
                <div class="text-center mb-4">
                    <p class="font-mono text-sm" x-text="receiptData?.transaction?.code"></p>
                    <p class="text-sm text-gray-600" x-text="new Date().toLocaleString('id-ID')"></p>
                    <p class="text-sm text-gray-600">Kasir: {{ auth()->user()->name }}</p>
                </div>

                <!-- Items -->
                <div class="mb-4">
                    <template x-for="item in receiptData?.items || []">
                        <div class="flex justify-between items-start py-1 text-sm">
                            <div class="flex-1">
                                <p class="font-medium" x-text="item.name"></p>
                                <p class="text-gray-500 text-xs" x-text="item.quantity + ' x Rp ' + formatNumber(item.price)"></p>
                            </div>
                            <span class="font-medium" x-text="'Rp ' + formatNumber(item.total)"></span>
                        </div>
                    </template>
                </div>

                <!-- Summary -->
                <div class="border-t pt-3 space-y-1 text-sm">
                    <div class="flex justify-between">
                        <span>Subtotal:</span>
                        <span x-text="'Rp ' + formatNumber(receiptData?.subtotal || 0)"></span>
                    </div>
                    <div x-show="(receiptData?.discount || 0) > 0" class="flex justify-between text-red-600">
                        <span>Diskon:</span>
                        <span x-text="'-Rp ' + formatNumber(receiptData?.discount || 0)"></span>
                    </div>
                    <div x-show="(receiptData?.tax || 0) > 0" class="flex justify-between">
                        <span>Pajak:</span>
                        <span x-text="'Rp ' + formatNumber(receiptData?.tax || 0)"></span>
                    </div>
                    <div class="flex justify-between font-bold text-lg border-t pt-2">
                        <span>Total:</span>
                        <span x-text="'Rp ' + formatNumber(receiptData?.total || 0)"></span>
                    </div>
                </div>

                <div class="text-center mt-4 text-sm text-gray-600">
                    <p>Terima kasih atas kunjungan Anda!</p>
                    <p>{{ config('app.name') }}</p>
                </div>
            </div>

            <!-- Receipt Actions -->
            <div class="p-4 border-t bg-gray-50 flex space-x-3">
                <button @click="printReceipt()" 
                        class="flex-1 bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-lg text-sm font-medium">
                    <i class="fas fa-print mr-2"></i>Print
                </button>
                <button @click="closeReceipt()" 
                        class="flex-1 bg-gray-500 hover:bg-gray-600 text-white py-2 px-4 rounded-lg text-sm font-medium">
                    <i class="fas fa-times mr-2"></i>Tutup
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function cashier() {
    return {
        // Data
        cart: [],
        products: @json($products),
        searchQuery: '',
        selectedCategory: null,
        selectedDiscount: 0,
        selectedTax: 0,
        paymentMethod: 'cash',
        processing: false,
        showReceipt: false,
        receiptData: null,

        // Computed values
        get subtotal() {
            return this.cart.reduce((sum, item) => sum + item.total, 0);
        },

        get discountAmount() {
            return (this.subtotal * this.selectedDiscount) / 100;
        },

        get taxAmount() {
            const afterDiscount = this.subtotal - this.discountAmount;
            return (afterDiscount * this.selectedTax) / 100;
        },

        get total() {
            return this.subtotal - this.discountAmount + this.taxAmount;
        },

        // Methods
        init() {
            console.log('Cashier initialized');
        },

        filterProduct(productId, productName, categoryId) {
            const matchesSearch = !this.searchQuery || 
                productName.includes(this.searchQuery.toLowerCase());
            const matchesCategory = this.selectedCategory === null || 
                categoryId === this.selectedCategory;
            
            return matchesSearch && matchesCategory;
        },

        addToCart(productId) {
            const product = this.products.find(p => p.id === productId);
            if (!product) return;

            const existingItem = this.cart.find(item => item.product_id === productId);
            
            if (existingItem) {
                existingItem.quantity += 1;
                existingItem.total = existingItem.quantity * existingItem.price;
            } else {
                this.cart.push({
                    product_id: productId,
                    name: product.name,
                    price: product.selling_price,
                    quantity: 1,
                    total: product.selling_price
                });
            }
            
            this.calculateTotal();
        },

        removeFromCart(index) {
            this.cart.splice(index, 1);
            this.calculateTotal();
        },

        updateQuantity(index, newQuantity) {
            if (newQuantity <= 0) {
                this.removeFromCart(index);
                return;
            }
            
            this.cart[index].quantity = newQuantity;
            this.cart[index].total = newQuantity * this.cart[index].price;
            this.calculateTotal();
        },

        clearCart() {
            if (confirm('Yakin ingin mengosongkan keranjang?')) {
                this.cart = [];
                this.selectedDiscount = 0;
                this.selectedTax = 0;
                this.paymentMethod = 'cash';
            }
        },

        calculateTotal() {
            // Reactive computation happens automatically
        },

        async processPayment() {
            if (this.cart.length === 0) {
                showToast('Keranjang masih kosong', 'error');
                return;
            }

            if (!this.paymentMethod) {
                showToast('Pilih metode pembayaran', 'error');
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
                        discount_percentage: this.selectedDiscount,
                        tax_percentage: this.selectedTax,
                        payment_method: this.paymentMethod
                    })
                });

                const result = await response.json();

                if (result.success) {
                    // Prepare receipt data
                    this.receiptData = {
                        transaction: {
                            code: result.transaction.code
                        },
                        items: this.cart,
                        subtotal: this.subtotal,
                        discount: this.discountAmount,
                        tax: this.taxAmount,
                        total: this.total,
                        payment_method: this.paymentMethod
                    };

                    // Show receipt
                    this.showReceipt = true;

                    // Clear cart
                    this.cart = [];
                    this.selectedDiscount = 0;
                    this.selectedTax = 0;
                    this.paymentMethod = 'cash';

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

        closeReceipt() {
            this.showReceipt = false;
            this.receiptData = null;
        },

        printReceipt() {
            window.print();
        },

        formatNumber(number) {
            return new Intl.NumberFormat('id-ID').format(number);
        }
    }
}
</script>

<style>
@media print {
    body * {
        visibility: hidden;
    }
    
    .receipt-content, .receipt-content * {
        visibility: visible;
    }
    
    .receipt-content {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
}
</style>
@endpush
@endsection