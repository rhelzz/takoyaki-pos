@extends('layouts.app')

@section('title', 'Tambah Kategori - Takoyaki POS')

@section('content')
<div class="p-4 max-w-2xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center mb-4">
            <a href="{{ route('categories.index') }}" 
               class="mr-3 p-2 text-gray-600 hover:text-gray-800">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Tambah Kategori</h1>
                <p class="text-gray-600">Buat kategori baru untuk produk takoyaki</p>
            </div>
        </div>
    </div>

    <!-- Form -->
    <form action="{{ route('categories.store') }}" method="POST" 
          class="bg-white rounded-lg shadow p-6" x-data="categoryForm()">
        @csrf

        <!-- Category Name -->
        <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                Nama Kategori <span class="text-red-500">*</span>
            </label>
            <input type="text" 
                   id="name" 
                   name="name" 
                   value="{{ old('name') }}"
                   required
                   x-model="name"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                   placeholder="Contoh: Takoyaki Original">
            @error('name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Description -->
        <div class="mb-4">
            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                Deskripsi
            </label>
            <textarea id="description" 
                      name="description" 
                      rows="3"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                      placeholder="Deskripsi kategori (opsional)">{{ old('description') }}</textarea>
            @error('description')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Status -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-3">
                Status Kategori <span class="text-red-500">*</span>
            </label>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-3">
                <label class="relative cursor-pointer">
                    <input type="radio" 
                           name="is_active" 
                           value="1" 
                           {{ old('is_active', '1') == '1' ? 'checked' : '' }}
                           class="sr-only">
                    <div class="p-4 border-2 border-green-200 rounded-lg hover:border-green-400 peer-checked:border-green-500 peer-checked:bg-green-50">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-600 text-xl mr-3"></i>
                            <div>
                                <h3 class="font-medium text-green-800">Aktif</h3>
                                <p class="text-sm text-green-600">Kategori dapat digunakan</p>
                            </div>
                        </div>
                    </div>
                </label>

                <label class="relative cursor-pointer">
                    <input type="radio" 
                           name="is_active" 
                           value="0" 
                           {{ old('is_active') == '0' ? 'checked' : '' }}
                           class="sr-only">
                    <div class="p-4 border-2 border-red-200 rounded-lg hover:border-red-400 peer-checked:border-red-500 peer-checked:bg-red-50">
                        <div class="flex items-center">
                            <i class="fas fa-times-circle text-red-600 text-xl mr-3"></i>
                            <div>
                                <h3 class="font-medium text-red-800">Tidak Aktif</h3>
                                <p class="text-sm text-red-600">Kategori tidak dapat digunakan</p>
                            </div>
                        </div>
                    </div>
                </label>
            </div>
            @error('is_active')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Preview Card -->
        <div x-show="name" class="mb-6 p-4 bg-gray-50 rounded-lg border">
            <h4 class="font-medium text-gray-800 mb-2">Preview Kategori:</h4>
            <div class="flex items-center p-3 bg-white rounded-lg border">
                <div class="flex-shrink-0 h-10 w-10">
                    <div class="h-10 w-10 rounded-lg bg-gradient-to-br from-red-400 to-red-600 flex items-center justify-center">
                        <i class="fas fa-tag text-white"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <div class="text-sm font-medium text-gray-900" x-text="name || 'Nama Kategori'"></div>
                    <div class="text-xs text-gray-500">Kategori Produk</div>
                </div>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="flex flex-col lg:flex-row lg:justify-end lg:space-x-3 space-y-3 lg:space-y-0">
            <a href="{{ route('categories.index') }}" 
               class="w-full lg:w-auto px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-center">
                Batal
            </a>
            <button type="submit" 
                    class="w-full lg:w-auto px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 disabled:opacity-50">
                <i class="fas fa-save mr-2"></i>
                Simpan Kategori
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
function categoryForm() {
    return {
        name: '{{ old("name") }}',
    }
}
</script>
@endpush
@endsection