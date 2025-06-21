@extends('layouts.app')

@section('title', 'Edit Produk - Takoyaki POS')

@section('content')
<div class="min-h-screen bg-gray-50" x-data="productForm()">
    <!-- Mobile Header -->
    <div class="bg-white border-b border-gray-200 sticky top-0 z-10">
        <div class="px-4 py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('products.index') }}" 
                   class="p-2 text-gray-600 hover:text-gray-800 -ml-2">
                    <i class="fas fa-arrow-left text-lg"></i>
                </a>
                <div>
                    <h1 class="text-lg font-bold text-gray-800">Edit Produk</h1>
                    <p class="text-sm text-gray-500">{{ $product->name }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="p-4 max-w-2xl mx-auto">
        <!-- Form -->
        <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data" 
              class="space-y-4">
            @csrf
            @method('PUT')

            <!-- Product Image Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                <label class="block text-sm font-medium text-gray-700 mb-3">Gambar Produk</label>
                <div class="flex flex-col items-center">
                    <!-- Current Image Preview -->
                    <div class="w-24 h-24 sm:w-32 sm:h-32 bg-gray-100 rounded-xl border-2 border-dashed border-gray-300 flex items-center justify-center mb-4 overflow-hidden">
                        <img x-show="imagePreview" 
                             :src="imagePreview" 
                             class="w-full h-full object-cover rounded-xl">
                        <img x-show="!imagePreview && '{{ $product->image }}'" 
                             src="{{ $product->image_url }}" 
                             class="w-full h-full object-cover rounded-xl">
                        <div x-show="!imagePreview && !'{{ $product->image }}'" class="text-center">
                            <i class="fas fa-image text-2xl text-gray-300 mb-2"></i>
                            <p class="text-xs text-gray-500">Preview</p>
                        </div>
                    </div>
                    
                    <!-- File Input -->
                    <input type="file" 
                           name="image" 
                           accept="image/*"
                           @change="previewImage($event)"
                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-red-50 file:text-red-700 hover:file:bg-red-100">
                    <p class="text-xs text-gray-500 mt-2 text-center">JPG, PNG, WEBP. Max 2MB.<br>Kosongkan jika tidak ingin mengubah.</p>
                </div>
                @error('image')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Basic Info Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 space-y-4">
                <h3 class="text-lg font-semibold text-gray-800">Informasi Dasar</h3>
                
                <!-- Product Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Produk <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           value="{{ old('name', $product->name) }}"
                           required
                           class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                           placeholder="Masukkan nama produk">
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Category -->
                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Kategori <span class="text-red-500">*</span>
                    </label>
                    <select id="category_id" 
                            name="category_id" 
                            required
                            class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                        <option value="">Pilih Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                    <textarea id="description" 
                              name="description" 
                              rows="3"
                              class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                              placeholder="Masukkan deskripsi produk">{{ old('description', $product->description) }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Quantity per Serving -->
                <div>
                    <label for="quantity_per_serving" class="block text-sm font-medium text-gray-700 mb-2">
                        Jumlah per Porsi <span class="text-red-500">*</span>
                    </label>
                    <select id="quantity_per_serving" 
                            name="quantity_per_serving" 
                            required
                            class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                        <option value="">Pilih Jumlah</option>
                        <option value="1" {{ old('quantity_per_serving', $product->quantity_per_serving) == '1' ? 'selected' : '' }}>1 pcs (Minuman)</option>
                        <option value="5" {{ old('quantity_per_serving', $product->quantity_per_serving) == '5' ? 'selected' : '' }}>5 pcs</option>
                        <option value="10" {{ old('quantity_per_serving', $product->quantity_per_serving) == '10' ? 'selected' : '' }}>10 pcs</option>
                        <option value="15" {{ old('quantity_per_serving', $product->quantity_per_serving) == '15' ? 'selected' : '' }}>15 pcs</option>
                    </select>
                    @error('quantity_per_serving')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Pricing Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 space-y-4">
                <h3 class="text-lg font-semibold text-gray-800">Harga & Keuntungan</h3>
                
                <!-- Pricing Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Cost Price -->
                    <div>
                        <label for="cost_price" class="block text-sm font-medium text-gray-700 mb-2">
                            Harga Modal <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 text-sm">Rp</span>
                            <input type="number" 
                                   id="cost_price" 
                                   name="cost_price" 
                                   value="{{ old('cost_price', $product->cost_price) }}"
                                   min="0"
                                   step="100"
                                   required
                                   x-model="costPrice"
                                   @input="calculateProfit()"
                                   class="w-full pl-8 pr-3 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
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
                            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 text-sm">Rp</span>
                            <input type="number" 
                                   id="selling_price" 
                                   name="selling_price" 
                                   value="{{ old('selling_price', $product->selling_price) }}"
                                   min="0"
                                   step="100"
                                   required
                                   x-model="sellingPrice"
                                   @input="calculateProfit()"
                                   class="w-full pl-8 pr-3 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                   placeholder="0">
                        </div>
                        @error('selling_price')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Profit Calculator -->
                <div class="p-3 bg-blue-50 rounded-lg">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-600 block">Keuntungan:</span>
                            <span class="font-medium text-blue-600" x-text="'Rp ' + formatNumber(profit)"></span>
                        </div>
                        <div>
                            <span class="text-gray-600 block">Margin:</span>
                            <span class="font-medium text-blue-600" x-text="profitMargin + '%'"></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status & Actions Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 space-y-4">
                <h3 class="text-lg font-semibold text-gray-800">Status & Aksi</h3>
                
                <!-- Status Toggle -->
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div>
                        <span class="text-sm font-medium text-gray-700">Status Produk</span>
                        <p class="text-xs text-gray-500">Aktifkan untuk menampilkan di kasir</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" 
                               name="is_active" 
                               value="1" 
                               {{ old('is_active', $product->is_active) ? 'checked' : '' }}
                               class="sr-only peer">
                        <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-red-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-600"></div>
                    </label>
                </div>
                @error('is_active')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror

                <!-- Submit Buttons -->
                <div class="space-y-3">
                    <button type="submit" 
                            class="w-full bg-red-500 hover:bg-red-600 text-white font-medium py-3 px-4 rounded-lg transition-colors">
                        <i class="fas fa-save mr-2"></i>Update Produk
                    </button>
                    <a href="{{ route('products.index') }}" 
                       class="block w-full bg-gray-500 hover:bg-gray-600 text-white font-medium py-3 px-4 rounded-lg text-center transition-colors">
                        <i class="fas fa-times mr-2"></i>Batal
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function productForm() {
    return {
        imagePreview: null,
        costPrice: {{ old('cost_price', $product->cost_price) }},
        sellingPrice: {{ old('selling_price', $product->selling_price) }},

        get profit() {
            return Math.max(0, (this.sellingPrice || 0) - (this.costPrice || 0));
        },

        get profitMargin() {
            if ((this.sellingPrice || 0) === 0) return 0;
            return Math.round((this.profit / this.sellingPrice) * 100 * 10) / 10;
        },

        previewImage(event) {
            const file = event.target.files[0];
            if (file) {
                if (file.size > 2 * 1024 * 1024) { // 2MB
                    if (typeof showToast === 'function') {
                        showToast('Ukuran file terlalu besar. Maksimal 2MB', 'error');
                    } else {
                        alert('Ukuran file terlalu besar. Maksimal 2MB');
                    }
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