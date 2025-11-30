@extends('layouts.app')

@section('title', 'Tambah Stock Masuk - Takoyaki POS')

@section('content')
<div class="min-h-screen bg-gray-50" x-data="stockForm()">
    <div class="bg-white border-b border-gray-200 sticky top-0 z-10">
        <div class="px-4 py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('stock-masuk.index') }}" class="p-2 text-gray-600 hover:text-gray-800 -ml-2">
                    <i class="fas fa-arrow-left text-lg"></i>
                </a>
                <div>
                    <h1 class="text-lg font-bold text-gray-800">Tambah Stock Masuk</h1>
                    <p class="text-sm text-gray-500">Catat barang yang masuk (Toping & Packaging)</p>
                </div>
            </div>
        </div>
    </div>

    <div class="p-4 max-w-2xl mx-auto">
        <form action="{{ route('stock-masuk.store') }}" method="POST" class="space-y-4" @submit="prepareSubmit">
            @csrf
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 space-y-4">
                <h3 class="text-lg font-semibold text-gray-800">Informasi Stock Masuk</h3>

                <div>
                    <label for="judul" class="block text-sm font-medium text-gray-700 mb-2">
                        Judul <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="judul" name="judul" value="{{ old('judul') }}" required
                           class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Contoh: Stock masuk cabang A">
                    @error('judul')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-2">
                        Deskripsi
                    </label>
                    <textarea id="deskripsi" name="deskripsi"
                              class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
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
                           class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('tanggal')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Qty Topping Section -->
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="font-semibold text-gray-700 flex items-center">
                            <i class="fas fa-fish text-blue-600 mr-2"></i>
                            Qty Topping
                        </h4>
                        <button type="button" 
                                @click="addTopping()"
                                class="text-sm bg-blue-500 hover:bg-blue-600 text-white px-3 py-1.5 rounded-lg transition-colors">
                            <i class="fas fa-plus mr-1"></i>Tambah Topping
                        </button>
                    </div>
                    
                    <div class="space-y-2">
                        <template x-for="(topping, index) in toppings" :key="index">
                            <div class="flex items-center gap-2 p-2 bg-gray-50 rounded-lg border border-gray-200">
                                <input type="text" 
                                       x-model="topping.name"
                                       placeholder="Nama topping (cth: Gurita)"
                                       class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       required>
                                <input type="number" 
                                       x-model="topping.qty"
                                       placeholder="Qty"
                                       min="0"
                                       class="w-24 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       required>
                                <button type="button" 
                                        @click="removeTopping(index)"
                                        class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </template>
                        
                        <div x-show="toppings.length === 0" class="text-center py-6 text-gray-400 border-2 border-dashed border-gray-200 rounded-lg">
                            <i class="fas fa-fish text-2xl mb-2"></i>
                            <p class="text-sm">Belum ada topping. Klik "Tambah Topping" untuk menambah.</p>
                        </div>
                    </div>
                </div>

                <!-- Qty Packaging Section -->
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="font-semibold text-gray-700 flex items-center">
                            <i class="fas fa-box text-orange-600 mr-2"></i>
                            Qty Packaging
                        </h4>
                        <button type="button" 
                                @click="addPackaging()"
                                class="text-sm bg-orange-500 hover:bg-orange-600 text-white px-3 py-1.5 rounded-lg transition-colors">
                            <i class="fas fa-plus mr-1"></i>Tambah Packaging
                        </button>
                    </div>
                    
                    <div class="space-y-2">
                        <template x-for="(packaging, index) in packagings" :key="index">
                            <div class="flex items-center gap-2 p-2 bg-gray-50 rounded-lg border border-gray-200">
                                <input type="text" 
                                       x-model="packaging.name"
                                       placeholder="Nama packaging (cth: Box S)"
                                       class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                       required>
                                <input type="number" 
                                       x-model="packaging.qty"
                                       placeholder="Qty"
                                       min="0"
                                       class="w-24 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                       required>
                                <button type="button" 
                                        @click="removePackaging(index)"
                                        class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </template>
                        
                        <div x-show="packagings.length === 0" class="text-center py-6 text-gray-400 border-2 border-dashed border-gray-200 rounded-lg">
                            <i class="fas fa-box text-2xl mb-2"></i>
                            <p class="text-sm">Belum ada packaging. Klik "Tambah Packaging" untuk menambah.</p>
                        </div>
                    </div>
                </div>

                <!-- Hidden inputs untuk submit -->
                <input type="hidden" name="toppings" x-model="toppingsJson">
                <input type="hidden" name="packagings" x-model="packagingsJson">
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 space-y-3">
                <button type="submit"
                        class="w-full bg-blue-500 hover:bg-blue-600 text-white font-medium py-3 px-4 rounded-lg transition-colors">
                    <i class="fas fa-save mr-2"></i>Simpan Stock Masuk
                </button>
                <a href="{{ route('stock-masuk.index') }}"
                   class="block w-full bg-gray-500 hover:bg-gray-600 text-white font-medium py-3 px-4 rounded-lg text-center transition-colors">
                    <i class="fas fa-times mr-2"></i>Batal
                </a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function stockForm() {
    return {
        toppings: [],
        packagings: [],
        toppingsJson: '[]',
        packagingsJson: '[]',
        
        init() {
            // Default items (bisa dikosongkan atau diisi default)
            this.toppings = [
                { name: 'Gurita', qty: 0 },
                { name: 'Crabstick', qty: 0 },
                { name: 'Udang', qty: 0 },
                { name: 'Beef', qty: 0 },
                { name: 'Bakso', qty: 0 },
                { name: 'Sosis', qty: 0 }
            ];
            
            this.packagings = [
                { name: 'Box S', qty: 0 },
                { name: 'Box M', qty: 0 },
                { name: 'Box L', qty: 0 },
                { name: 'Styrofoam', qty: 0 }
            ];
        },
        
        addTopping() {
            this.toppings.push({ name: '', qty: 0 });
        },
        
        removeTopping(index) {
            this.toppings.splice(index, 1);
        },
        
        addPackaging() {
            this.packagings.push({ name: '', qty: 0 });
        },
        
        removePackaging(index) {
            this.packagings.splice(index, 1);
        },
        
        prepareSubmit(e) {
            // Convert arrays to JSON before submit
            this.toppingsJson = JSON.stringify(this.toppings);
            this.packagingsJson = JSON.stringify(this.packagings);
        }
    }
}
</script>
@endpush
@endsection