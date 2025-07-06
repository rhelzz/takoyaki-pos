@extends('layouts.app')

@section('title', 'Tambah Stock Keluar - Takoyaki POS')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="bg-white border-b border-gray-200 sticky top-0 z-10">
        <div class="px-4 py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('stock-keluar.index') }}" class="p-2 text-gray-600 hover:text-gray-800 -ml-2">
                    <i class="fas fa-arrow-left text-lg"></i>
                </a>
                <div>
                    <h1 class="text-lg font-bold text-gray-800">Tambah Stock Keluar</h1>
                    <p class="text-sm text-gray-500">Catat barang yang keluar (Toping & Packaging)</p>
                </div>
            </div>
        </div>
    </div>

    <div class="p-4 max-w-2xl mx-auto">
        <form action="{{ route('stock-keluar.store') }}" method="POST" class="space-y-4">
            @csrf
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 space-y-4">
                <h3 class="text-lg font-semibold text-gray-800">Informasi Stock Keluar</h3>

                <div>
                    <label for="judul" class="block text-sm font-medium text-gray-700 mb-2">
                        Judul <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="judul" name="judul" value="{{ old('judul') }}" required
                           class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                           placeholder="Contoh: Stock keluar cabang A">
                    @error('judul')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-2">
                        Deskripsi
                    </label>
                    <textarea id="deskripsi" name="deskripsi"
                              class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                              placeholder="Deskripsi laporan">{{ old('deskripsi') }}</textarea>
                    @error('deskripsi')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="tanggal" class="block text-sm font-medium text-gray-700 mb-2">
                        Tanggal <span class="text-red-500">*</span>
                    </label>
                    <input type="date" id="tanggal" name="tanggal"
                           value="{{ old('tanggal', date('Y-m-d')) }}"
                           required
                           class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                    @error('tanggal')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <h4 class="font-semibold mb-2 text-gray-700">Qty Toping</h4>
                    <div class="grid grid-cols-2 gap-3">
                        @foreach(['Gurita','Crabstick','Udang','Beef','Bakso','Sosis'] as $item)
                        <div>
                            <label class="block text-xs text-gray-600 mb-1">{{ $item }}</label>
                            <input type="number" name="items[{{ $item }}]" value="{{ old('items.'.$item, 0) }}" min="0"
                                class="w-full px-2 py-2 border border-gray-200 rounded focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm" />
                        </div>
                        @endforeach
                    </div>
                </div>

                <div>
                    <h4 class="font-semibold mb-2 mt-4 text-gray-700">Qty Packaging</h4>
                    <div class="grid grid-cols-2 gap-3">
                        @foreach(['Box S','Box M','Box L','Styrofoam'] as $item)
                        <div>
                            <label class="block text-xs text-gray-600 mb-1">{{ $item }}</label>
                            <input type="number" name="items[{{ $item }}]" value="{{ old('items.'.$item, 0) }}" min="0"
                                class="w-full px-2 py-2 border border-gray-200 rounded focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm" />
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
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