@extends('layouts.app')

@section('title', 'Detail Transaksi - Takoyaki POS')

@section('content')
<div class="min-h-screen bg-gray-50" x-data="transactionDetail()">
    <!-- Mobile Header -->
    <div class="bg-white border-b border-gray-200 sticky top-0 z-10">
        <div class="px-4 py-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <a href="{{ route('transactions.index') }}" 
                       class="p-2 text-gray-600 hover:text-gray-800 -ml-2">
                        <i class="fas fa-arrow-left text-lg"></i>
                    </a>
                    <div>
                        <h1 class="text-lg font-bold text-gray-800">{{ $transaction->transaction_code }}</h1>
                        <p class="text-xs text-gray-500">{{ $transaction->formatted_created_at }}</p>
                    </div>
                </div>
                
                <!-- Mobile Action Menu -->
                <div class="flex items-center space-x-2">
                    <button @click="showActions = !showActions" 
                            class="p-2 text-gray-600 hover:text-gray-800">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                </div>
            </div>
            
            <!-- Mobile Actions Dropdown -->
            <div x-show="showActions" 
                 x-transition:enter="transition ease-out duration-100"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="mt-3 flex flex-col space-y-2 sm:hidden">
                <a href="{{ route('transactions.receipt', $transaction) }}" 
                   target="_blank"
                   class="flex items-center px-3 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg text-sm">
                    <i class="fas fa-receipt mr-2"></i>Lihat Receipt
                </a>
                @if(auth()->user()->isAdmin() && $transaction->created_at->isToday())
                    <form action="{{ route('transactions.destroy', $transaction) }}" method="POST" 
                          onsubmit="return confirm('Yakin ingin menghapus transaksi ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="w-full flex items-center px-3 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg text-sm">
                            <i class="fas fa-trash mr-2"></i>Hapus Transaksi
                        </button>
                    </form>
                @endif
            </div>

            <!-- Desktop Actions -->
            <div class="hidden sm:flex items-center space-x-2 mt-3">
                <a href="{{ route('transactions.receipt', $transaction) }}" 
                   target="_blank"
                   class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm">
                    <i class="fas fa-receipt mr-2"></i>Lihat Receipt
                </a>
                @if(auth()->user()->isAdmin() && $transaction->created_at->isToday())
                    <form action="{{ route('transactions.destroy', $transaction) }}" method="POST" 
                          onsubmit="return confirm('Yakin ingin menghapus transaksi ini?')" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm">
                            <i class="fas fa-trash mr-2"></i>Hapus
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <div class="p-4 max-w-6xl mx-auto space-y-4">
        <!-- Summary Cards - Mobile First -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
            <!-- Total Amount -->
            <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100 col-span-2 lg:col-span-1">
                <div class="text-center">
                    <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Total Bayar</p>
                    <p class="text-lg font-bold text-green-600">{{ $transaction->formatted_total_amount }}</p>
                </div>
            </div>

            <!-- Profit -->
            <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
                <div class="text-center">
                    <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Profit</p>
                    <p class="text-lg font-bold text-blue-600">{{ $transaction->formatted_profit }}</p>
                </div>
            </div>

            <!-- Items -->
            <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
                <div class="text-center">
                    <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Items</p>
                    <p class="text-lg font-bold text-gray-800">{{ $transaction->total_items }}</p>
                </div>
            </div>
        </div>

        <!-- Transaction Info -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-4 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-800">Informasi Transaksi</h2>
            </div>
            <div class="p-4 space-y-4">
                <!-- Kasir & Payment -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-red-500 rounded-full flex items-center justify-center text-white text-sm font-bold">
                            {{ strtoupper(substr($transaction->user->name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="font-medium text-gray-800">{{ $transaction->user->name }}</p>
                            <p class="text-xs text-gray-500">{{ $transaction->user->role_label ?? 'Kasir' }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                            @switch($transaction->payment_method)
                                @case('cash')
                                    bg-green-100 text-green-800
                                    @break
                                @case('card')
                                    bg-blue-100 text-blue-800
                                    @break
                                @case('dana')
                                    bg-blue-100 text-blue-800
                                    @break
                                @case('gopay')
                                    bg-green-100 text-green-800
                                    @break
                                @case('ovo')
                                    bg-purple-100 text-purple-800
                                    @break
                                @default
                                    bg-gray-100 text-gray-800
                            @endswitch">
                            @switch($transaction->payment_method)
                                @case('cash')
                                    <i class="fas fa-money-bill-wave mr-1"></i>
                                    @break
                                @case('card')
                                    <i class="fas fa-credit-card mr-1"></i>
                                    @break
                                @case('dana')
                                    <i class="fas fa-mobile-alt mr-1"></i>
                                    @break
                                @case('gopay')
                                    <i class="fas fa-wallet mr-1"></i>
                                    @break
                                @case('ovo')
                                    <i class="fas fa-coins mr-1"></i>
                                    @break
                                @default
                                    <i class="fas fa-qrcode mr-1"></i>
                            @endswitch
                            {{ $transaction->payment_method_label }}
                        </span>
                        
                        <!-- Show customer money and change for cash payments -->
                        @if($transaction->payment_method === 'cash')
                            @if($transaction->customer_money)
                                <div class="text-xs text-gray-600 mt-1">
                                    Bayar: {{ $transaction->formatted_customer_money }}
                                </div>
                            @endif
                            @if($transaction->hasChange())
                                <div class="text-xs text-green-600">
                                    Kembalian: {{ $transaction->formatted_change_amount }}
                                </div>
                            @endif
                        @endif
                    </div>
                </div>

                <!-- Additional Info Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4 border-t border-gray-100">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide">Kode Transaksi</label>
                        <p class="text-sm font-mono text-gray-800 mt-1">{{ $transaction->transaction_code }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide">Waktu</label>
                        <p class="text-sm text-gray-800 mt-1">{{ $transaction->formatted_created_at }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Items List -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-4 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-800">Item Transaksi</h2>
            </div>

            <!-- Desktop Table -->
            <div class="hidden lg:block overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Produk</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Harga</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Qty</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Total</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Profit</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($transaction->items as $item)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-4">
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
                                            <p class="text-xs text-gray-500">{{ $item->product->category->name }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-600">{{ $item->formatted_unit_price }}</td>
                                <td class="px-4 py-4 text-sm font-medium text-gray-800">{{ $item->quantity }}</td>
                                <td class="px-4 py-4 font-medium text-gray-800">{{ $item->formatted_total_price }}</td>
                                <td class="px-4 py-4 font-medium text-blue-600">{{ $item->formatted_profit }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Mobile Cards -->
            <div class="lg:hidden divide-y divide-gray-100">
                @foreach($transaction->items as $item)
                    <div class="p-4">
                        <div class="flex items-start space-x-3">
                            @if($item->product->image)
                                <img src="{{ $item->product->image_url }}" 
                                     alt="{{ $item->product->name }}"
                                     class="w-12 h-12 rounded-lg object-cover flex-shrink-0">
                            @else
                                <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-image text-gray-400"></i>
                                </div>
                            @endif
                            
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between mb-2">
                                    <div class="flex-1 min-w-0">
                                        <h3 class="font-medium text-gray-800 truncate">{{ $item->product->name }}</h3>
                                        <p class="text-xs text-gray-500">{{ $item->product->category->name }}</p>
                                    </div>
                                    <div class="text-right ml-2">
                                        <p class="font-medium text-gray-800">{{ $item->formatted_total_price }}</p>
                                        <p class="text-xs text-blue-600">{{ $item->formatted_profit }}</p>
                                    </div>
                                </div>
                                
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-600">{{ $item->formatted_unit_price }} × {{ $item->quantity }}</span>
                                    <span class="text-xs text-gray-500">Qty: {{ $item->quantity }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Financial Summary -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-4 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-800">Ringkasan Pembayaran</h2>
            </div>
            <div class="p-4">
                <div class="space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Subtotal:</span>
                        <span class="font-medium text-gray-800">Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}</span>
                    </div>

                    @if($transaction->hasDiscount())
                        <div class="flex justify-between text-sm text-red-600">
                            <span>Diskon ({{ $transaction->discount_percentage }}%):</span>
                            <span class="font-medium">-Rp {{ number_format($transaction->discount_amount, 0, ',', '.') }}</span>
                        </div>
                    @endif

                    @if($transaction->hasTax())
                        <div class="flex justify-between text-sm text-orange-600">
                            <span>Pajak ({{ $transaction->tax_percentage }}%):</span>
                            <span class="font-medium">Rp {{ number_format($transaction->tax_amount, 0, ',', '.') }}</span>
                        </div>
                    @endif

                    <div class="border-t border-gray-200 pt-3">
                        <div class="flex justify-between">
                            <span class="font-semibold text-gray-800">Total:</span>
                            <span class="font-bold text-lg text-green-600">{{ $transaction->formatted_total_amount }}</span>
                        </div>
                    </div>
                    
                    <!-- Cash Payment Details -->
                    @if($transaction->payment_method === 'cash')
                        <div class="border-t border-gray-200 pt-3 space-y-2">
                            @if($transaction->customer_money)
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Uang Customer:</span>
                                    <span class="font-medium text-gray-800">{{ $transaction->formatted_customer_money }}</span>
                                </div>
                            @endif
                            
                            @if($transaction->hasChange())
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Kembalian:</span>
                                    <span class="font-medium text-green-600">{{ $transaction->formatted_change_amount }}</span>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Profit & Stats - Mobile Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <!-- Profit Analysis -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-4 border-b border-gray-100">
                    <h2 class="text-lg font-semibold text-gray-800">Analisis Keuntungan</h2>
                </div>
                <div class="p-4">
                    <div class="space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Modal:</span>
                            <span class="font-medium text-red-600">Rp {{ number_format($transaction->total_cost, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Laba Kotor:</span>
                            <span class="font-medium text-blue-600">Rp {{ number_format($transaction->gross_profit, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Laba Bersih:</span>
                            <span class="font-medium text-purple-600">{{ $transaction->formatted_profit }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Margin:</span>
                            <span class="font-medium text-gray-800">{{ number_format($transaction->profit_margin, 1) }}%</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transaction Stats -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-4 border-b border-gray-100">
                    <h2 class="text-lg font-semibold text-gray-800">Statistik Transaksi</h2>
                </div>
                <div class="p-4">
                    <div class="space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Total Item:</span>
                            <span class="font-medium text-gray-800">{{ $transaction->total_items }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Jenis Produk:</span>
                            <span class="font-medium text-gray-800">{{ $transaction->items_count }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Rata-rata per Item:</span>
                            <span class="font-medium text-gray-800">Rp {{ number_format($transaction->total_amount / $transaction->total_items, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function transactionDetail() {
    return {
        showActions: false,
        
        init() {
            // Close actions when clicking outside
            document.addEventListener('click', (e) => {
                if (!e.target.closest('[x-data]')) {
                    this.showActions = false;
                }
            });
        }
    }
}
</script>
@endpush
@endsection