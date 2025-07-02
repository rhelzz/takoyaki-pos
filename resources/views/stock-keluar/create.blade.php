@extends('layouts.app')

@section('title', 'Tambah Stock Keluar - Takoyaki POS')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Mobile Header -->
    <div class="bg-white border-b border-gray-200 sticky top-0 z-10">
        <div class="px-4 py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('stock-keluar.index') }}" 
                   class="p-2 text-gray-600 hover:text-gray-800 -ml-2">
                    <i class="fas fa-arrow-left text-lg"></i>
                </a>
                <div>
                    <h1 class="text-lg font-bold text-gray-800">Tambah Stock Keluar</h1>
                    <p class="text-sm text-gray-500">Catat barang yang keluar</p>
                </div>
            </div>
        </div>
    </div>

    <div class="p-4 max-w-2xl mx-auto">
        <!-- Form -->
        <form action="{{ route('stock-keluar.store') }}" method="POST" class="space-y-4">
            @csrf

            <!-- Stock Info Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 space-y-4">
                <h3 class="text-lg font-semibold text-gray-800">Informasi Stock Keluar</h3>
                
                <!-- Nama Barang -->
                <div>
                    <label for="nama_barang" class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Barang <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="nama_barang" 
                           name="nama_barang" 
                           value="{{ old('nama_barang') }}"
                           required
                           class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
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
                           value="{{ old('qty') }}"
                           required
                           min="1"
                           class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
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
                           value="{{ old('tanggal', date('Y-m-d')) }}"
                           required
                           class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                    @error('tanggal')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Warning Info -->
            <div class="bg-orange-50 border border-orange-200 rounded-xl p-4">
                <div class="flex items-start space-x-3">
                    <div class="w-5 h-5 text-orange-500 mt-0.5">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-orange-800 mb-1">Perhatian</h4>
                        <p class="text-sm text-orange-700">
                            Pastikan stock barang tersedia sebelum mengeluarkan. 
                            Cek di <a href="{{ route('stock-summary.index') }}" class="underline font-medium">Stock Summary</a> untuk melihat stok saat ini.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 space-y-3">
                <button type="submit" 
                        class="w-full bg-orange-500 hover:bg-orange-600 text-white font-medium py-3 px-4 rounded-lg transition-colors">
                    <i class="fas fa-save mr-2"></i>Simpan Stock Keluar
                </button>
                <a href="{{ route('stock-keluar.index') }}" 
                   class="block w-full bg-gray-500 hover:bg-gray-600 text-white font-medium py-3 px-4 rounded-lg text-center transition-colors">
                    <i class="fas fa-times mr-2"></i>Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection