@extends('layouts.app')

@section('title', 'Edit Stock Masuk - Takoyaki POS')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Mobile Header -->
    <div class="bg-white border-b border-gray-200 sticky top-0 z-10">
        <div class="px-4 py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('stock-masuk.index') }}" 
                   class="p-2 text-gray-600 hover:text-gray-800 -ml-2">
                    <i class="fas fa-arrow-left text-lg"></i>
                </a>
                <div>
                    <h1 class="text-lg font-bold text-gray-800">Edit Stock Masuk</h1>
                    <p class="text-sm text-gray-500">{{ $stockMasuk->nama_barang }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="p-4 max-w-2xl mx-auto">
        <!-- Form -->
        <form action="{{ route('stock-masuk.update', $stockMasuk) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <!-- Stock Info Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 space-y-4">
                <h3 class="text-lg font-semibold text-gray-800">Informasi Stock Masuk</h3>
                
                <!-- Nama Barang -->
                <div>
                    <label for="nama_barang" class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Barang <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="nama_barang" 
                           name="nama_barang" 
                           value="{{ old('nama_barang', $stockMasuk->nama_barang) }}"
                           required
                           class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Contoh: Tepung Terigu">
                    @error('nama_barang')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Qty -->
                <div>
                    <label for="qty" class="block text-sm font-medium text-gray-700 mb-2">
                        Quantity <span class="text-red-500">*</span>
                    </label>
                    <input type="number" 
                           id="qty" 
                           name="qty" 
                           value="{{ old('qty', $stockMasuk->qty) }}"
                           required
                           min="1"
                           class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Masukkan jumlah">
                    @error('qty')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tanggal -->
                <div>
                    <label for="tanggal" class="block text-sm font-medium text-gray-700 mb-2">
                        Tanggal <span class="text-red-500">*</span>
                    </label>
                    <input type="date" 
                           id="tanggal" 
                           name="tanggal" 
                           value="{{ old('tanggal', $stockMasuk->tanggal->format('Y-m-d')) }}"
                           required
                           class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('tanggal')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 space-y-3">
                <button type="submit" 
                        class="w-full bg-blue-500 hover:bg-blue-600 text-white font-medium py-3 px-4 rounded-lg transition-colors">
                    <i class="fas fa-save mr-2"></i>Update Stock Masuk
                </button>
                <a href="{{ route('stock-masuk.index') }}" 
                   class="block w-full bg-gray-500 hover:bg-gray-600 text-white font-medium py-3 px-4 rounded-lg text-center transition-colors">
                    <i class="fas fa-times mr-2"></i>Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection