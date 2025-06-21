@extends('layouts.app')

@section('title', 'Detail Transaksi - Takoyaki POS')

@section('content')
<div class="p-4 max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center mb-4">
            <a href="{{ route('transactions.index') }}" 
               class="mr-3 p-2 text-gray-600 hover:text-gray-800">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div class="flex-1">
                <h1 class="text-2xl font-bold text-gray-800">{{ $transaction->transaction_code }}</h1>
                <p class="text-gray-600">Detail transaksi {{ $transaction->formatted_created_at }}</p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('transactions.receipt', $transaction) }}" 
                   target="_blank"
                   class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg">
                    <i class="fas fa-receipt mr-2"></i>Lihat Receipt
                </a>
                @if(auth()->user()->isAdmin() && $transaction->created_at->isToday())
                    <form action="{{ route('transactions.destroy', $transaction) }}" method="POST" 
                          onsubmit="return confirm('Yakin ingin menghapus transaksi ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg">
                            <i class="fas fa-trash mr-2"></i>Hapus
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Transaction Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Info -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Informasi Transaksi</h2>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Kode Transaksi</label>
                        <p class="text-lg font-mono text-gray-800">{{ $transaction->transaction_code }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600">Tanggal & Waktu</label>
                        <p class="text-lg text-gray-800">{{ $transaction->formatted_created_at }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600">Kasir</label>
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-red-500 rounded-full flex items-center justify-center text-white text-sm font-bold mr-2">
                                {{ strtoupper(substr($transaction->user->name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-lg text-gray-800">{{ $transaction->user->name }}</p>
                                <p class="text-sm text-gray-500">{{ $transaction->user->role_label }}</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600">Metode Pembayaran</label>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium mt-1
                            {{ $transaction->payment_method === 'cash' ? 'bg-green-100 text-green-800' : 
                               ($transaction->payment_method === 'card' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800') }}">
                            @if($transaction->payment_method === 'cash')
                                <i class="fas fa-money-bill-wave mr-2"></i>
                            @elseif($transaction->payment_method === 'card')
                                <i class="fas fa-credit-card mr-2"></i>
                            @else
                                <i class="fas fa-mobile-alt mr-2"></i>
                            @endif
                            {{ $transaction->payment_method_label }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Items -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-800">Item Transaksi</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produk</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Harga</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Qty</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Profit</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($transaction->items as $item)
                                <tr>
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
                                                <p class="text-sm text-gray-500">{{ $item->product->category->name }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ $item->formatted_unit_price }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ $item->quantity }}
                                    </td>
                                    <td class="px-6 py-4 font-medium text-gray-800">
                                        {{ $item->formatted_total_price }}
                                    </td>
                                    <td class="px-6 py-4 font-medium text-blue-600">
                                        {{ $item->formatted_profit }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Summary -->
        <div class="space-y-6">
            <!-- Financial Summary -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Ringkasan Keuangan</h2>
                
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal:</span>
                        <span class="font-medium">Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}</span>
                    </div>

                    @if($transaction->hasDiscount())
                        <div class="flex justify-between text-red-600">
                            <span>Diskon ({{ $transaction->discount_percentage }}%):</span>
                            <span class="font-medium">-Rp {{ number_format($transaction->discount_amount, 0, ',', '.') }}</span>
                        </div>
                    @endif

                    @if($transaction->hasTax())
                        <div class="flex justify-between text-orange-600">
                            <span>Pajak ({{ $transaction->tax_percentage }}%):</span>
                            <span class="font-medium">Rp {{ number_format($transaction->tax_amount, 0, ',', '.') }}</span>
                        </div>
                    @endif

                    <hr>

                    <div class="flex justify-between font-bold text-lg">
                        <span>Total:</span>
                        <span class="text-green-600">{{ $transaction->formatted_total_amount }}</span>
                    </div>
                </div>
            </div>

            <!-- Profit Analysis -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Analisis Keuntungan</h2>
                
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Modal:</span>
                        <span class="font-medium text-red-600">Rp {{ number_format($transaction->total_cost, 0, ',', '.') }}</span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-gray-600">Laba Kotor:</span>
                        <span class="font-medium text-blue-600">Rp {{ number_format($transaction->gross_profit, 0, ',', '.') }}</span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-gray-600">Laba Bersih:</span>
                        <span class="font-medium text-purple-600">{{ $transaction->formatted_profit }}</span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-gray-600">Margin:</span>
                        <span class="font-medium text-gray-800">{{ number_format($transaction->profit_margin, 1) }}%</span>
                    </div>
                </div>
            </div>

            <!-- Transaction Stats -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Statistik</h2>
                
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Total Item:</span>
                        <span class="font-medium">{{ $transaction->total_items }}</span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-gray-600">Jenis Produk:</span>
                        <span class="font-medium">{{ $transaction->items_count }}</span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-gray-600">Rata-rata per Item:</span>
                        <span class="font-medium">Rp {{ number_format($transaction->total_amount / $transaction->total_items, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection