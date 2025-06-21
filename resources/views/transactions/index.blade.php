@extends('layouts.app')

@section('title', 'Daftar Transaksi - Takoyaki POS')

@section('content')
<div class="min-h-screen bg-gray-50" x-data="transactionList()">
    <!-- Header - Mobile Optimized -->
    <div class="bg-white border-b border-gray-200 px-4 py-4 sticky top-0 z-10">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl md:text-2xl font-bold text-gray-800">Transaksi</h1>
                <p class="text-sm text-gray-600 hidden sm:block">Kelola dan pantau semua transaksi</p>
            </div>
            <!-- Mobile Quick Actions -->
            <div class="flex space-x-2">
                <button @click="showFilters = !showFilters" 
                        class="lg:hidden bg-red-500 text-white p-2 rounded-lg">
                    <i class="fas fa-filter text-sm"></i>
                </button>
                @if(auth()->user()->canProcessTransactions())
                    <a href="{{ route('cashier') }}" 
                       class="bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded-lg text-sm">
                        <i class="fas fa-plus mr-1"></i>
                        <span class="hidden sm:inline">Transaksi</span>
                    </a>
                @endif
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
                <div class="space-y-3 lg:space-y-0 lg:grid lg:grid-cols-5 lg:gap-4">
                    <!-- Search -->
                    <div class="lg:col-span-1">
                        <label class="block text-xs font-medium text-gray-700 mb-1 lg:hidden">Cari Transaksi</label>
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}"
                               placeholder="Cari kode transaksi..."
                               class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                    </div>

                    <!-- Date -->
                    <div class="lg:col-span-1">
                        <label class="block text-xs font-medium text-gray-700 mb-1 lg:hidden">Tanggal</label>
                        <input type="date" 
                               name="date" 
                               value="{{ request('date') }}"
                               class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                    </div>

                    <!-- User Filter -->
                    <div class="lg:col-span-1">
                        <label class="block text-xs font-medium text-gray-700 mb-1 lg:hidden">Kasir</label>
                        <select name="user_id" 
                                class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                            <option value="">Semua Kasir</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Payment Method -->
                    <div class="lg:col-span-1">
                        <label class="block text-xs font-medium text-gray-700 mb-1 lg:hidden">Pembayaran</label>
                        <select name="payment_method" 
                                class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                            <option value="">Semua Pembayaran</option>
                            <option value="cash" {{ request('payment_method') === 'cash' ? 'selected' : '' }}>Tunai</option>
                            <option value="card" {{ request('payment_method') === 'card' ? 'selected' : '' }}>Kartu</option>
                            <option value="digital" {{ request('payment_method') === 'digital' ? 'selected' : '' }}>Digital</option>
                        </select>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="lg:col-span-1">
                        <div class="flex space-x-2 lg:pt-0">
                            <button type="submit" 
                                    class="flex-1 bg-red-500 hover:bg-red-600 text-white px-4 py-2.5 rounded-lg text-sm font-medium">
                                <i class="fas fa-search mr-2"></i>Filter
                            </button>
                            <a href="{{ route('transactions.index') }}" 
                               class="px-4 py-2.5 bg-gray-500 hover:bg-gray-600 text-white rounded-lg">
                                <i class="fas fa-times text-sm"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Transactions List -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            @if($transactions->count() > 0)
                <!-- Desktop Table -->
                <div class="hidden lg:block overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Transaksi</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Waktu</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Kasir</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Items</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Pembayaran</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Total</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Profit</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($transactions as $transaction)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-4">
                                        <div class="font-medium text-gray-800">{{ $transaction->transaction_code }}</div>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-600">
                                        {{ $transaction->formatted_created_at }}
                                    </td>
                                    <td class="px-4 py-4 text-sm">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 bg-red-500 rounded-full flex items-center justify-center text-white text-xs font-bold mr-3">
                                                {{ strtoupper(substr($transaction->user->name, 0, 1)) }}
                                            </div>
                                            <span class="text-gray-700">{{ $transaction->user->name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-sm">
                                        <div class="text-gray-800">{{ $transaction->total_items }} item</div>
                                        <div class="text-xs text-gray-500">{{ $transaction->items_count }} produk</div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                                            {{ $transaction->payment_method === 'cash' ? 'bg-green-100 text-green-800' : 
                                               ($transaction->payment_method === 'card' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800') }}">
                                            {{ $transaction->payment_method_label }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="font-semibold text-green-600">{{ $transaction->formatted_total_amount }}</div>
                                        @if($transaction->hasDiscount())
                                            <div class="text-xs text-red-500">Diskon: {{ $transaction->discount_percentage }}%</div>
                                        @endif
                                        @if($transaction->hasTax())
                                            <div class="text-xs text-orange-500">Pajak: {{ $transaction->tax_percentage }}%</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="font-semibold text-blue-600">{{ $transaction->formatted_profit }}</div>
                                        <div class="text-xs text-gray-500">{{ number_format($transaction->profit_margin, 1) }}%</div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="flex space-x-3">
                                            <a href="{{ route('transactions.show', $transaction) }}" 
                                               class="text-blue-600 hover:text-blue-800" title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('transactions.receipt', $transaction) }}" 
                                               class="text-green-600 hover:text-green-800" target="_blank" title="Receipt">
                                                <i class="fas fa-receipt"></i>
                                            </a>
                                            @if(auth()->user()->isAdmin() && $transaction->created_at->isToday())
                                                <button @click="deleteTransaction('{{ $transaction->id }}', '{{ $transaction->transaction_code }}')"
                                                        class="text-red-600 hover:text-red-800" title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Cards - Completely Redesigned -->
                <div class="lg:hidden">
                    @foreach($transactions as $transaction)
                        <div class="border-b border-gray-100 last:border-b-0">
                            <!-- Card Header -->
                            <div class="p-4 bg-gray-50 border-b border-gray-100">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-red-500 rounded-full flex items-center justify-center text-white text-sm font-bold">
                                            {{ strtoupper(substr($transaction->user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <h3 class="font-semibold text-gray-800">{{ $transaction->transaction_code }}</h3>
                                            <p class="text-xs text-gray-500">{{ $transaction->user->name }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-bold text-green-600">{{ $transaction->formatted_total_amount }}</div>
                                        <div class="text-xs text-gray-500">{{ $transaction->formatted_created_at }}</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Card Body -->
                            <div class="p-4 space-y-3">
                                <!-- Transaction Details -->
                                <div class="grid grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <span class="text-gray-500">Items:</span>
                                        <span class="font-medium text-gray-800 ml-1">{{ $transaction->total_items }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500">Profit:</span>
                                        <span class="font-medium text-blue-600 ml-1">{{ $transaction->formatted_profit }}</span>
                                    </div>
                                </div>

                                <!-- Payment Method -->
                                <div class="flex items-center justify-between">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                                        {{ $transaction->payment_method === 'cash' ? 'bg-green-100 text-green-800' : 
                                           ($transaction->payment_method === 'card' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800') }}">
                                        {{ $transaction->payment_method_label }}
                                    </span>

                                    <!-- Action Buttons -->
                                    <div class="flex space-x-4">
                                        <a href="{{ route('transactions.show', $transaction) }}" 
                                           class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                            <i class="fas fa-eye mr-1"></i>Detail
                                        </a>
                                        <a href="{{ route('transactions.receipt', $transaction) }}" 
                                           class="text-green-600 hover:text-green-800 text-sm font-medium" target="_blank">
                                            <i class="fas fa-receipt mr-1"></i>Receipt
                                        </a>
                                        @if(auth()->user()->isAdmin() && $transaction->created_at->isToday())
                                            <button @click="deleteTransaction('{{ $transaction->id }}', '{{ $transaction->transaction_code }}')"
                                                    class="text-red-600 hover:text-red-800 text-sm font-medium">
                                                <i class="fas fa-trash mr-1"></i>Hapus
                                            </button>
                                        @endif
                                    </div>
                                </div>

                                <!-- Additional Info -->
                                @if($transaction->hasDiscount() || $transaction->hasTax())
                                    <div class="pt-2 border-t border-gray-100 text-xs space-y-1">
                                        @if($transaction->hasDiscount())
                                            <div class="text-red-500">Diskon: {{ $transaction->discount_percentage }}%</div>
                                        @endif
                                        @if($transaction->hasTax())
                                            <div class="text-orange-500">Pajak: {{ $transaction->tax_percentage }}%</div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination - Mobile Optimized -->
                @if($transactions->hasPages())
                    <div class="p-4 border-t border-gray-200 bg-gray-50">
                        {{ $transactions->appends(request()->query())->links() }}
                    </div>
                @endif
            @else
                <!-- Empty State - Mobile Optimized -->
                <div class="p-8 text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-receipt text-2xl text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-800 mb-2">Belum ada transaksi</h3>
                    <p class="text-gray-500 mb-6">Mulai buat transaksi pertama Anda</p>
                    @if(auth()->user()->canProcessTransactions())
                        <a href="{{ route('cashier') }}" 
                           class="inline-flex items-center px-6 py-3 bg-red-500 hover:bg-red-600 text-white rounded-lg font-medium">
                            <i class="fas fa-plus mr-2"></i>Buat Transaksi
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
function transactionList() {
    return {
        showFilters: false,
        
        init() {
            // Auto-hide filters on mobile unless there are active filters
            if (window.innerWidth < 1024) {
                const hasActiveFilters = new URLSearchParams(window.location.search).toString() !== '';
                this.showFilters = hasActiveFilters;
            }
        },

        async deleteTransaction(transactionId, transactionCode) {
            if (!confirm(`Yakin ingin menghapus transaksi ${transactionCode}? Aksi ini tidak dapat dibatalkan.`)) return;

            try {
                const response = await fetch(`/transactions/${transactionId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                if (response.ok) {
                    location.reload();
                } else {
                    showToast('Gagal menghapus transaksi', 'error');
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