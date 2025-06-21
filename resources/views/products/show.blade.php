@extends('layouts.app')

@section('title', $product->name . ' - Takoyaki POS')

@section('content')
<div class="p-4 max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center mb-4">
            <a href="{{ route('products.index') }}" 
               class="mr-3 p-2 text-gray-600 hover:text-gray-800">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div class="flex-1">
                <h1 class="text-2xl font-bold text-gray-800">{{ $product->name }}</h1>
                <p class="text-gray-600">Detail produk dan statistik penjualan</p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('products.edit', $product) }}" 
                   class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
                @if($product->is_active)
                    <span class="inline-flex items-center px-3 py-2 rounded-lg text-sm font-medium bg-green-100 text-green-800">
                        <i class="fas fa-check-circle mr-2"></i>Aktif
                    </span>
                @else
                    <span class="inline-flex items-center px-3 py-2 rounded-lg text-sm font-medium bg-red-100 text-red-800">
                        <i class="fas fa-times-circle mr-2"></i>Nonaktif
                    </span>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Product Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Info -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Informasi Produk</h2>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Product Image -->
                    <div>
                        <div class="aspect-square bg-gray-100 rounded-lg overflow-hidden">
                            @if($product->image)
                                <img src="{{ $product->image_url }}" 
                                     alt="{{ $product->name }}"
                                     class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <i class="fas fa-image text-gray-300 text-6xl"></i>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Product Info -->
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Kategori</label>
                            <p class="text-lg text-gray-800">{{ $product->category->name }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-600">Jumlah per Porsi</label>
                            <p class="text-lg text-gray-800">{{ $product->quantity_per_serving }} pcs</p>
                        </div>

                        @if($product->description)
                            <div>
                                <label class="block text-sm font-medium text-gray-600">Deskripsi</label>
                                <p class="text-gray-800">{{ $product->description }}</p>
                            </div>
                        @endif

                        <div>
                            <label class="block text-sm font-medium text-gray-600">Dibuat</label>
                            <p class="text-gray-800">{{ $product->created_at->format('d/m/Y H:i') }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-600">Terakhir Update</label>
                            <p class="text-gray-800">{{ $product->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pricing Details -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Detail Harga</h2>
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                    <div class="text-center p-4 bg-red-50 rounded-lg">
                        <div class="text-2xl font-bold text-red-600 mb-1">
                            {{ $product->formatted_cost_price }}
                        </div>
                        <div class="text-sm text-gray-600">Harga Modal</div>
                    </div>

                    <div class="text-center p-4 bg-green-50 rounded-lg">
                        <div class="text-2xl font-bold text-green-600 mb-1">
                            {{ $product->formatted_selling_price }}
                        </div>
                        <div class="text-sm text-gray-600">Harga Jual</div>
                    </div>

                    <div class="text-center p-4 bg-blue-50 rounded-lg">
                        <div class="text-2xl font-bold text-blue-600 mb-1">
                            {{ $product->formatted_profit }}
                        </div>
                        <div class="text-sm text-gray-600">Keuntungan ({{ number_format($product->profit_margin, 1) }}%)</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics -->
        <div class="space-y-6">
            <!-- Sales Stats -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Statistik Penjualan</h2>
                
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Total Terjual:</span>
                        <span class="font-bold text-lg">{{ $stats['total_sold'] ?? 0 }} pcs</span>
                    </div>

                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Total Pendapatan:</span>
                        <span class="font-bold text-lg text-green-600">
                            Rp {{ number_format($stats['total_revenue'] ?? 0, 0, ',', '.') }}
                        </span>
                    </div>

                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Total Keuntungan:</span>
                        <span class="font-bold text-lg text-blue-600">
                            Rp {{ number_format($stats['total_profit'] ?? 0, 0, ',', '.') }}
                        </span>
                    </div>

                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Jumlah Transaksi:</span>
                        <span class="font-bold text-lg">{{ $stats['transaction_count'] ?? 0 }}</span>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Aksi Cepat</h2>
                
                <div class="space-y-3">
                    <a href="{{ route('products.edit', $product) }}" 
                       class="w-full bg-yellow-500 hover:bg-yellow-600 text-white py-2 px-4 rounded-lg text-center block">
                        <i class="fas fa-edit mr-2"></i>Edit Produk
                    </a>

                    <form action="{{ route('products.toggle-status', $product) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" 
                                class="w-full {{ $product->is_active ? 'bg-red-500 hover:bg-red-600' : 'bg-green-500 hover:bg-green-600' }} text-white py-2 px-4 rounded-lg">
                            <i class="fas {{ $product->is_active ? 'fa-times' : 'fa-check' }} mr-2"></i>
                            {{ $product->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                        </button>
                    </form>

                    @if(!$product->transactionItems()->exists())
                        <form action="{{ route('products.destroy', $product) }}" method="POST" 
                              onsubmit="return confirm('Yakin ingin menghapus produk ini? Aksi ini tidak dapat dibatalkan.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="w-full bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded-lg">
                                <i class="fas fa-trash mr-2"></i>Hapus Produk
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    @if($product->transactionItems()->exists())
        <div class="mt-6 bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">Transaksi Terbaru</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode Transaksi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quantity</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kasir</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($product->transactionItems()->with('transaction.user')->latest()->limit(10)->get() as $item)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $item->transaction->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-800">
                                    {{ $item->transaction->transaction_code }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $item->quantity }} pcs
                                </td>
                                <td class="px-6 py-4 text-sm font-medium text-green-600">
                                    {{ $item->formatted_total_price }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $item->transaction->user->name }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection