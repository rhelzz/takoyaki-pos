@extends('layouts.app')

@section('title', $category->name . ' - Detail Kategori')

@section('content')
<div class="p-4">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center mb-4">
            <a href="{{ route('categories.index') }}" 
               class="mr-3 p-2 text-gray-600 hover:text-gray-800">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div class="flex-1">
                <h1 class="text-2xl font-bold text-gray-800">{{ $category->name }}</h1>
                <p class="text-gray-600">Detail kategori dan produk terkait</p>
            </div>
            <div class="flex items-center space-x-3">
                @if($category->is_active)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                        <i class="fas fa-check-circle mr-2"></i>
                        Aktif
                    </span>
                @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                        <i class="fas fa-times-circle mr-2"></i>
                        Tidak Aktif
                    </span>
                @endif
                
                <a href="{{ route('categories.edit', $category) }}" 
                   class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    <i class="fas fa-edit mr-2"></i>
                    Edit Kategori
                </a>
            </div>
        </div>
    </div>

    <!-- Category Info -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Category Details -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center mb-4">
                    <div class="flex-shrink-0 h-12 w-12">
                        <div class="h-12 w-12 rounded-lg bg-gradient-to-br from-red-400 to-red-600 flex items-center justify-center">
                            <i class="fas fa-tag text-white text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-xl font-semibold text-gray-800">{{ $category->name }}</h2>
                        <p class="text-gray-600">{{ $category->description ?: 'Tidak ada deskripsi' }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mt-6">
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <span class="text-sm text-gray-600">Dibuat:</span>
                        <p class="font-medium">{{ $category->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <span class="text-sm text-gray-600">Terakhir Diubah:</span>
                        <p class="font-medium">{{ $category->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics -->
        <div class="space-y-4">
            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <i class="fas fa-box text-blue-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-gray-600">Total Produk</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['total_products'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <i class="fas fa-check-circle text-green-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-gray-600">Produk Aktif</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['active_products'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-red-100 rounded-lg">
                        <i class="fas fa-times-circle text-red-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-gray-600">Tidak Aktif</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['inactive_products'] }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Products in this Category -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-800">
                    Produk dalam Kategori ({{ $products->total() }})
                </h3>
                <a href="{{ route('products.create') }}?category={{ $category->id }}" 
                   class="inline-flex items-center px-3 py-2 text-sm bg-red-600 text-white rounded-lg hover:bg-red-700">
                    <i class="fas fa-plus mr-2"></i>
                    Tambah Produk
                </a>
            </div>
        </div>

        @if($products->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Produk
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Harga
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Dibuat
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($products as $product)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    @if($product->image)
                                        <img class="h-10 w-10 rounded-lg object-cover" 
                                             src="{{ Storage::url($product->image) }}" 
                                             alt="{{ $product->name }}">
                                    @else
                                        <div class="h-10 w-10 rounded-lg bg-gradient-to-br from-gray-400 to-gray-600 flex items-center justify-center">
                                            <i class="fas fa-image text-white"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                                    <div class="text-sm text-gray-500">{{ Str::limit($product->description, 50) }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                Rp {{ number_format($product->selling_price, 0, ',', '.') }}
                            </div>
                            <div class="text-sm text-gray-500">
                                Modal: Rp {{ number_format($product->cost_price, 0, ',', '.') }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($product->is_active)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <i class="fas fa-times-circle mr-1"></i>
                                    Tidak Aktif
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $product->created_at->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="{{ route('products.show', $product) }}" 
                                   class="text-blue-600 hover:text-blue-900 p-1">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('products.edit', $product) }}" 
                                   class="text-indigo-600 hover:text-indigo-900 p-1">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($products->hasPages())
        <div class="bg-white px-4 py-3 border-t border-gray-200">
            {{ $products->links() }}
        </div>
        @endif

        @else
        <!-- Empty State -->
        <div class="px-6 py-12 text-center">
            <div class="flex flex-col items-center">
                <i class="fas fa-box-open text-gray-300 text-4xl mb-4"></i>
                <p class="text-gray-500 text-lg mb-2">Belum ada produk dalam kategori ini</p>
                <p class="text-gray-400 mb-4">Mulai dengan menambahkan produk pertama</p>
                <a href="{{ route('products.create') }}?category={{ $category->id }}" 
                   class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    <i class="fas fa-plus mr-2"></i>
                    Tambah Produk
                </a>
            </div>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
// Add any JavaScript if needed
</script>
@endpush
@endsection