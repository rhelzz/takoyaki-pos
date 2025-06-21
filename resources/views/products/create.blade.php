@extends('layouts.app')

@section('title', 'Tambah Produk - Takoyaki POS')

@section('content')
<div class="p-4 max-w-2xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center mb-4">
            <a href="{{ route('products.index') }}" 
               class="mr-3 p-2 text-gray-600 hover:text-gray-800">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Tambah Produk</h1>
                <p class="text-gray-600">Tambahkan produk takoyaki baru</p>
            </div>
        </div>
    </div>

    <!-- Form -->
    <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" 
          class="bg-white rounded-lg shadow p-6" x-data="productForm()">
        @csrf

        <!-- Product Image -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Gambar Produk</label>
            <div class="flex flex-col items-center">
                <!-- Image Preview -->
                <div class="w-32 h-32 bg-gray-100 rounded-lg border-2 border-dashed border-gray-300 flex items-center justify-center mb-4 overflow-hidden">
                    <img x-show="imagePreview" 
                         :src="imagePreview" 
                         class="w-full h-full object-cover rounded-lg">
                    <div x-show="!imagePreview" class="text-center">
                        <i class="fas fa-image text-3xl text-gray-300 mb-2"></i>
                        <p class="text-sm text-gray-500">Preview Gambar</p>
                    </div>
                </div>
                
                <!-- File Input -->
                <input type="file" 
                       name="image" 
                       accept="image/*"
                       @change="previewImage($event)"
                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-red-50 file:text-red-700 hover:file:bg-red-100">
                <p class="text-xs text-gray-500 mt-1">JPG, PNG, WEBP. Max 2MB</p>
            </div>
            @error('image')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Product Name -->
        <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                Nama Produk <span class="text-red-500">*</span>
            </label>
            <input type="text" 
                   id="name" 
                   name="name" 
                   value="{{ old('name') }}"
                   required
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                   placeholder="Masukkan nama produk">
            @error('name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Category -->
        <div class="mb-4">
            <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">
                Kategori <span class="text-red-500">*</span>
            </label>
            <select id="category_id" 
                    name="category_id" 
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                <option value="">Pilih Kategori</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            @error('category_id')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Description -->
        <div class="mb-4">
            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
            <textarea id="description" 
                      name="description" 
                      rows="3"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                      placeholder="Masukkan deskripsi produk">{{ old('description') }}</textarea>
            @error('description')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Pricing -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
            <!-- Cost Price -->
            <div>
                <label for="cost_price" class="block text-sm font-medium text-gray-700 mb-2">
                    Harga Modal <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">Rp</span>
                    <input type="number" 
                           id="cost_price" 
                           name="cost_price" 
                           value="{{ old('cost_price') }}"
                           min="0"
                           step="100"
                           required
                           x-model="costPrice"
                           @input="calculateProfit()"
                           class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                           placeholder="0">
                </div>
                @error('cost_price')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Selling Price -->
            <div>
                <label for="selling_price" class="block text-sm font-medium text-gray-700 mb-2">
                    Harga Jual <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">Rp</span>
                    <input type="number" 
                           id="selling_price" 
                           name="selling_price" 
                           value="{{ old('selling_price') }}"
                           min="0"
                           step="100"
                           required
                           x-model="sellingPrice"
                           @input="calculateProfit()"
                           class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                           placeholder="0">
                </div>
                @error('selling_price')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Profit Calculator -->
        <div class="mb-4 p-3 bg-blue-50 rounded-lg">
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="text-gray-600">Keuntungan per Unit:</span>
                    <span class="font-medium text-blue-600" x-text="'Rp ' + formatNumber(profit)"></span>
                </div>
                <div>
                    <span class="text-gray-600">Margin Keuntungan:</span>
                    <span class="font-medium text-blue-600" x-text="profitMargin + '%'"></span>
                </div>
            </div>
        </div>

        <!-- Quantity per Serving -->
        <div class="mb-4">
            <label for="quantity_per_serving" class="block text-sm font-medium text-gray-700 mb-2">
                Jumlah per Porsi <span class="text-red-500">*</span>
            </label>
            <select id="quantity_per_serving" 
                    name="quantity_per_serving" 
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                <option value="">Pilih Jumlah</option>
                <option value="1" {{ old('quantity_per_serving') == '1' ? 'selected' : '' }}>1 pcs (Minuman)</option>
                <option value="5" {{ old('quantity_per_serving', '5') == '5' ? 'selected' : '' }}>5 pcs</option>
                <option value="10" {{ old('quantity_per_serving') == '10' ? 'selected' : '' }}>10 pcs</option>
                <option value="15" {{ old('quantity_per_serving') == '15' ? 'selected' : '' }}>15 pcs</option>
            </select>
            @error('quantity_per_serving')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Status -->
        <div class="mb-6">
            <label class="flex items-center">
                <input type="checkbox" 
                       name="is_active" 
                       value="1" 
                       {{ old('is_active', '1') ? 'checked' : '' }}
                       class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                <span class="ml-2 text-sm text-gray-700">Produk Aktif</span>
            </label>
            @error('is_active')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Submit Buttons -->
        <div class="flex flex-col lg:flex-row lg:space-x-3 space-y-3 lg:space-y-0">
            <button type="submit" 
                    class="flex-1 bg-red-500 hover:bg-red-600 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                <i class="fas fa-save mr-2"></i>Simpan Produk
            </button>
            <a href="{{ route('products.index') }}" 
               class="flex-1 bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-lg text-center transition-colors">
                <i class="fas fa-times mr-2"></i>Batal
            </a>
        </div>
    </form>
</div>

@push('scripts')
<script>
function productForm() {
    return {
        imagePreview: null,
        costPrice: {{ old('cost_price', 0) }},
        sellingPrice: {{ old('selling_price', 0) }},

        get profit() {
            return Math.max(0, this.sellingPrice - this.costPrice);
        },

        get profitMargin() {
            if (this.sellingPrice === 0) return 0;
            return Math.round((this.profit / this.sellingPrice) * 100 * 10) / 10;
        },

        previewImage(event) {
            const file = event.target.files[0];
            if (file) {
                if (file.size > 2 * 1024 * 1024) { // 2MB
                    showToast('Ukuran file terlalu besar. Maksimal 2MB', 'error');
                    event.target.value = '';
                    this.imagePreview = null;
                    return;
                }

                const reader = new FileReader();
                reader.onload = (e) => {
                    this.imagePreview = e.target.result;
                };
                reader.readAsDataURL(file);
            } else {
                this.imagePreview = null;
            }
        },

        calculateProfit() {
            // Reactive calculation happens automatically
        },

        formatNumber(number) {
            return new Intl.NumberFormat('id-ID').format(number || 0);
        }
    }
}
</script>
@endpush
@endsection