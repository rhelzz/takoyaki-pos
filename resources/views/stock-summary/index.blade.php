@extends('layouts.app')

@section('title', 'Stock Summary - Takoyaki POS')

@section('content')
<div class="p-4 max-w-7xl mx-auto">
    <!-- Enhanced Header with Visual Appeal -->
    <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-xl p-4 lg:p-6 mb-6 text-white">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
            <div class="mb-4 lg:mb-0">
                <h1 class="text-xl lg:text-2xl font-bold mb-2">📦 Stock Summary</h1>
                <div class="flex flex-wrap items-center gap-2 lg:gap-4 text-xs lg:text-sm">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 bg-white rounded-full opacity-75"></span>
                        <span>Total: <strong>{{ $totalItems }}</strong></span>
                    </div>
                    @if($lowStock > 0)
                        <div class="flex items-center gap-2 bg-red-500 bg-opacity-30 px-2 py-1 rounded-full">
                            <i class="fas fa-exclamation-triangle text-xs"></i>
                            <span>Rendah: <strong>{{ $lowStock }}</strong></span>
                        </div>
                    @endif
                    @if($outOfStock > 0)
                        <div class="flex items-center gap-2 bg-red-700 bg-opacity-40 px-2 py-1 rounded-full">
                            <i class="fas fa-times-circle text-xs"></i>
                            <span>Habis: <strong>{{ $outOfStock }}</strong></span>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Enhanced Quick Actions -->
            <div class="flex gap-2">
                <a href="{{ route('stock-masuk.create') }}" 
                   class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-3 py-2 rounded-lg text-xs lg:text-sm font-medium transition-all duration-200 backdrop-blur-sm flex items-center gap-1">
                    <i class="fas fa-plus text-xs"></i>
                    <span class="hidden sm:inline">Stock</span> Masuk
                </a>
                <a href="{{ route('stock-keluar.create') }}" 
                   class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-3 py-2 rounded-lg text-xs lg:text-sm font-medium transition-all duration-200 backdrop-blur-sm flex items-center gap-1">
                    <i class="fas fa-minus text-xs"></i>
                    <span class="hidden sm:inline">Stock</span> Keluar
                </a>
            </div>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-4 mb-6">
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg p-3 lg:p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-xs uppercase tracking-wide">Aman</p>
                    <p class="text-lg lg:text-2xl font-bold">{{ collect($stockSummary)->where('stock_now', '>', 3)->count() }}</p>
                </div>
                <div class="w-8 h-8 lg:w-10 lg:h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-sm lg:text-base"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg p-3 lg:p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-xs uppercase tracking-wide">Rendah</p>
                    <p class="text-lg lg:text-2xl font-bold">{{ $lowStock }}</p>
                </div>
                <div class="w-8 h-8 lg:w-10 lg:h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-sm lg:text-base"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-lg p-3 lg:p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-red-100 text-xs uppercase tracking-wide">Habis</p>
                    <p class="text-lg lg:text-2xl font-bold">{{ $outOfStock }}</p>
                </div>
                <div class="w-8 h-8 lg:w-10 lg:h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-times-circle text-sm lg:text-base"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg p-3 lg:p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-xs uppercase tracking-wide">Total</p>
                    <p class="text-lg lg:text-2xl font-bold">{{ $totalItems }}</p>
                </div>
                <div class="w-8 h-8 lg:w-10 lg:h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-boxes text-sm lg:text-base"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Table Card -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <!-- Search Header with Better Styling -->
        <div class="bg-gray-50 px-4 lg:px-6 py-4 border-b border-gray-200">
            <form method="GET" class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1">
                    <div class="relative">
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}" 
                               placeholder="Cari nama barang..." 
                               class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                    </div>
                </div>
                <div class="flex gap-2">
                    <button type="submit" 
                            class="bg-blue-500 hover:bg-blue-600 text-white px-4 lg:px-6 py-2.5 rounded-lg font-medium transition-colors flex items-center gap-2 text-sm">
                        <i class="fas fa-search"></i>
                        <span class="hidden sm:inline">Cari</span>
                    </button>
                    @if(request('search'))
                        <a href="{{ route('stock-summary.index') }}" 
                           class="bg-gray-500 hover:bg-gray-600 text-white px-4 lg:px-6 py-2.5 rounded-lg font-medium transition-colors flex items-center gap-2 text-sm">
                            <i class="fas fa-refresh"></i>
                            <span class="hidden sm:inline">Reset</span>
                        </a>
                    @endif
                </div>
            </form>
        </div>
        
        <!-- Mobile-Optimized Table/Cards -->
        @if($stockSummary->count() > 0)
            <!-- Desktop Table (Hidden on Mobile) -->
            <div class="hidden lg:block overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Nama Barang
                            </th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Stock Masuk
                            </th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Stock Keluar
                            </th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Stock Saat Ini
                            </th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Status
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @foreach($stockSummary as $item)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-gradient-to-br from-blue-100 to-blue-200 rounded-lg flex items-center justify-center mr-3">
                                            <i class="fas fa-box text-blue-600"></i>
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900">{{ $item['nama_barang'] }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center gap-1 text-sm font-medium text-green-600">
                                        <i class="fas fa-arrow-up text-xs"></i>
                                        {{ number_format($item['total_masuk']) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center gap-1 text-sm font-medium text-red-600">
                                        <i class="fas fa-arrow-down text-xs"></i>
                                        {{ number_format($item['total_keluar']) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="text-lg font-bold
                                        @if($item['stock_now'] <= 0) text-red-700
                                        @elseif($item['stock_now'] <= 3) text-orange-600
                                        @else text-gray-800
                                        @endif">
                                        {{ number_format($item['stock_now']) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($item['stock_now'] <= 0)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <span class="w-2 h-2 bg-red-500 rounded-full mr-2"></span>
                                            Habis
                                        </span>
                                    @elseif($item['stock_now'] <= 3)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                            <span class="w-2 h-2 bg-orange-500 rounded-full mr-2"></span>
                                            Rendah
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                                            Aman
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card Layout (Visible on Mobile Only) -->
            <div class="lg:hidden divide-y divide-gray-100">
                @foreach($stockSummary as $item)
                    <div class="p-4 hover:bg-gray-50 transition-colors">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-gradient-to-br from-blue-100 to-blue-200 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-box text-blue-600 text-sm"></i>
                                </div>
                                <div>
                                    <h3 class="font-medium text-gray-900 text-sm">{{ $item['nama_barang'] }}</h3>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-lg font-bold
                                    @if($item['stock_now'] <= 0) text-red-700
                                    @elseif($item['stock_now'] <= 3) text-orange-600
                                    @else text-gray-800
                                    @endif">
                                    {{ number_format($item['stock_now']) }}
                                </div>
                                @if($item['stock_now'] <= 0)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <span class="w-1.5 h-1.5 bg-red-500 rounded-full mr-1"></span>
                                        Habis
                                    </span>
                                @elseif($item['stock_now'] <= 3)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                        <span class="w-1.5 h-1.5 bg-orange-500 rounded-full mr-1"></span>
                                        Rendah
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1"></span>
                                        Aman
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="flex justify-between text-xs text-gray-600">
                            <div class="flex items-center gap-1">
                                <i class="fas fa-arrow-up text-green-600"></i>
                                <span class="text-green-600 font-medium">{{ number_format($item['total_masuk']) }}</span>
                                <span>masuk</span>
                            </div>
                            <div class="flex items-center gap-1">
                                <i class="fas fa-arrow-down text-red-600"></i>
                                <span class="text-red-600 font-medium">{{ number_format($item['total_keluar']) }}</span>
                                <span>keluar</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Enhanced Pagination -->
            @if($stockSummary->hasPages())
                <div class="bg-gray-50 px-4 lg:px-6 py-4 border-t border-gray-200">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div class="text-xs lg:text-sm text-gray-700 text-center sm:text-left">
                            Menampilkan <span class="font-medium">{{ $stockSummary->firstItem() }}</span> - 
                            <span class="font-medium">{{ $stockSummary->lastItem() }}</span> dari 
                            <span class="font-medium">{{ $stockSummary->total() }}</span> item
                        </div>
                        <div class="flex items-center justify-center gap-1">
                            {{-- Previous Page Link --}}
                            @if ($stockSummary->onFirstPage())
                                <span class="px-2 lg:px-3 py-2 text-xs lg:text-sm text-gray-400 cursor-not-allowed rounded">‹</span>
                            @else
                                <a href="{{ $stockSummary->previousPageUrl() }}" 
                                   class="px-2 lg:px-3 py-2 text-xs lg:text-sm text-gray-700 hover:bg-gray-200 rounded transition-colors">‹</a>
                            @endif

                            {{-- Page Numbers (Limited on Mobile) --}}
                            @php
                                $start = max(1, $stockSummary->currentPage() - 1);
                                $end = min($stockSummary->lastPage(), $stockSummary->currentPage() + 1);
                            @endphp
                            
                            @for($page = $start; $page <= $end; $page++)
                                @if ($page == $stockSummary->currentPage())
                                    <span class="px-2 lg:px-3 py-2 text-xs lg:text-sm bg-blue-500 text-white rounded font-medium">{{ $page }}</span>
                                @else
                                    <a href="{{ $stockSummary->url($page) }}" 
                                       class="px-2 lg:px-3 py-2 text-xs lg:text-sm text-gray-700 hover:bg-gray-200 rounded transition-colors">{{ $page }}</a>
                                @endif
                            @endfor

                            {{-- Next Page Link --}}
                            @if ($stockSummary->hasMorePages())
                                <a href="{{ $stockSummary->nextPageUrl() }}" 
                                   class="px-2 lg:px-3 py-2 text-xs lg:text-sm text-gray-700 hover:bg-gray-200 rounded transition-colors">›</a>
                            @else
                                <span class="px-2 lg:px-3 py-2 text-xs lg:text-sm text-gray-400 cursor-not-allowed rounded">›</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        @else
            <!-- Enhanced Empty State -->
            <div class="px-4 lg:px-6 py-12 lg:py-16 text-center">
                <div class="w-16 h-16 lg:w-20 lg:h-20 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-boxes text-gray-400 text-xl lg:text-2xl"></i>
                </div>
                <h3 class="text-lg lg:text-xl font-medium text-gray-900 mb-2">
                    @if(request('search'))
                        Tidak ditemukan "{{ request('search') }}"
                    @else
                        Belum ada data stock
                    @endif
                </h3>
                <p class="text-gray-500 mb-6 text-sm lg:text-base">
                    @if(request('search'))
                        Coba gunakan kata kunci yang berbeda
                    @else
                        Tambah stock masuk untuk mulai mengelola inventory
                    @endif
                </p>
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <a href="{{ route('stock-masuk.create') }}" 
                       class="bg-green-500 hover:bg-green-600 text-white px-4 lg:px-6 py-2 lg:py-3 rounded-lg font-medium transition-colors flex items-center justify-center gap-2 text-sm lg:text-base">
                        <i class="fas fa-plus"></i>Tambah Stock Masuk
                    </a>
                    <a href="{{ route('stock-keluar.create') }}" 
                       class="bg-red-500 hover:bg-red-600 text-white px-4 lg:px-6 py-2 lg:py-3 rounded-lg font-medium transition-colors flex items-center justify-center gap-2 text-sm lg:text-base">
                        <i class="fas fa-minus"></i>Tambah Stock Keluar
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection