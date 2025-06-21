@extends('layouts.app')

@section('title', 'Daftar Transaksi - Takoyaki POS')

@section('content')
<div class="p-4 max-w-7xl mx-auto" x-data="transactionList()">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Daftar Transaksi</h1>
        <p class="text-gray-600">Kelola dan pantau semua transaksi</p>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-receipt text-blue-600"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-800">{{ $transactions->total() }}</p>
                    <p class="text-sm text-gray-600">Total Transaksi</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-money-bill-wave text-green-600"></i>
                </div>
                <div>
                    <p class="text-xl font-bold text-gray-800">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
                    <p class="text-sm text-gray-600">Total Pendapatan</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-chart-line text-purple-600"></i>
                </div>
                <div>
                    <p class="text-xl font-bold text-gray-800">Rp {{ number_format($totalProfit, 0, ',', '.') }}</p>
                    <p class="text-sm text-gray-600">Total Profit</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow mb-6 p-4">
        <form method="GET" class="grid grid-cols-1 lg:grid-cols-5 gap-4">
            <!-- Search -->
            <div>
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}"
                       placeholder="Cari kode transaksi..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
            </div>

            <!-- Date -->
            <div>
                <input type="date" 
                       name="date" 
                       value="{{ request('date') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
            </div>

            <!-- User Filter -->
            <div>
                <select name="user_id" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                    <option value="">Semua Kasir</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Payment Method -->
            <div>
                <select name="payment_method" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                    <option value="">Semua Pembayaran</option>
                    <option value="cash" {{ request('payment_method') === 'cash' ? 'selected' : '' }}>Tunai</option>
                    <option value="card" {{ request('payment_method') === 'card' ? 'selected' : '' }}>Kartu</option>
                    <option value="digital" {{ request('payment_method') === 'digital' ? 'selected' : '' }}>Digital</option>
                </select>
            </div>

            <!-- Submit -->
            <div class="flex space-x-2">
                <button type="submit" 
                        class="flex-1 bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg">
                    <i class="fas fa-search mr-2"></i>Filter
                </button>
                <a href="{{ route('transactions.index') }}" 
                   class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Transactions Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        @if($transactions->count() > 0)
            <!-- Desktop Table -->
            <div class="hidden lg:block overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode Transaksi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal/Waktu</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kasir</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Items</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pembayaran</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Profit</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($transactions as $transaction)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-800">{{ $transaction->transaction_code }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    <div>{{ $transaction->formatted_created_at }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-red-500 rounded-full flex items-center justify-center text-white text-xs font-bold mr-2">
                                            {{ strtoupper(substr($transaction->user->name, 0, 1)) }}
                                        </div>
                                        {{ $transaction->user->name }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    <div>{{ $transaction->total_items }} item</div>
                                    <div class="text-xs text-gray-500">{{ $transaction->items_count }} produk</div>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                        {{ $transaction->payment_method === 'cash' ? 'bg-green-100 text-green-800' : 
                                           ($transaction->payment_method === 'card' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800') }}">
                                        {{ $transaction->payment_method_label }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-green-600">{{ $transaction->formatted_total_amount }}</div>
                                    @if($transaction->hasDiscount())
                                        <div class="text-xs text-red-500">Diskon: {{ $transaction->discount_percentage }}%</div>
                                    @endif
                                    @if($transaction->hasTax())
                                        <div class="text-xs text-orange-500">Pajak: {{ $transaction->tax_percentage }}%</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-blue-600">{{ $transaction->formatted_profit }}</div>
                                    <div class="text-xs text-gray-500">{{ number_format($transaction->profit_margin, 1) }}%</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('transactions.show', $transaction) }}" 
                                           class="text-blue-600 hover:text-blue-800">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('transactions.receipt', $transaction) }}" 
                                           class="text-green-600 hover:text-green-800" target="_blank">
                                            <i class="fas fa-receipt"></i>
                                        </a>
                                        @if(auth()->user()->isAdmin() && $transaction->created_at->isToday())
                                            <button @click="deleteTransaction('{{ $transaction->id }}', '{{ $transaction->transaction_code }}')"
                                                    class="text-red-600 hover:text-red-800">
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

            <!-- Mobile Cards -->
            <div class="lg:hidden divide-y divide-gray-200">
                @foreach($transactions as $transaction)
                    <div class="p-4">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h3 class="font-medium text-gray-800">{{ $transaction->transaction_code }}</h3>
                                <p class="text-sm text-gray-600">{{ $transaction->formatted_created_at }}</p>
                            </div>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                {{ $transaction->payment_method === 'cash' ? 'bg-green-100 text-green-800' : 
                                   ($transaction->payment_method === 'card' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800') }}">
                                {{ $transaction->payment_method_label }}
                            </span>
                        </div>

                        <div class="flex justify-between items-center mb-3">
                            <div class="text-sm text-gray-600">
                                <div class="flex items-center mb-1">
                                    <div class="w-6 h-6 bg-red-500 rounded-full flex items-center justify-center text-white text-xs font-bold mr-2">
                                        {{ strtoupper(substr($transaction->user->name, 0, 1)) }}
                                    </div>
                                    {{ $transaction->user->name }}
                                </div>
                                <div>{{ $transaction->total_items }} item</div>
                            </div>
                            <div class="text-right">
                                <div class="font-medium text-green-600">{{ $transaction->formatted_total_amount }}</div>
                                <div class="text-sm text-blue-600">{{ $transaction->formatted_profit }}</div>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('transactions.show', $transaction) }}" 
                               class="text-blue-600 hover:text-blue-800 text-sm">
                                <i class="fas fa-eye mr-1"></i>Detail
                            </a>
                            <a href="{{ route('transactions.receipt', $transaction) }}" 
                               class="text-green-600 hover:text-green-800 text-sm" target="_blank">
                                <i class="fas fa-receipt mr-1"></i>Receipt
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($transactions->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $transactions->appends(request()->query())->links() }}
                </div>
            @endif
        @else
            <div class="p-8 text-center text-gray-500">
                <i class="fas fa-receipt text-4xl mb-4 text-gray-300"></i>
                <p>Belum ada transaksi</p>
                @if(auth()->user()->canProcessTransactions())
                    <a href="{{ route('cashier') }}" 
                       class="mt-4 inline-block bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg">
                        <i class="fas fa-plus mr-2"></i>Buat Transaksi
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function transactionList() {
    return {
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