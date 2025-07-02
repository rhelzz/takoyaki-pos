@extends('layouts.app')

@section('title', 'Tambah Pengeluaran Harian - Takoyaki POS')

@section('content')
<div class="min-h-screen bg-gray-50" x-data="expenseForm()">
    <!-- Mobile Header -->
    <div class="bg-white border-b border-gray-200 sticky top-0 z-10">
        <div class="px-4 py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('daily-expenses.index') }}" 
                   class="p-2 text-gray-600 hover:text-gray-800 -ml-2">
                    <i class="fas fa-arrow-left text-lg"></i>
                </a>
                <div>
                    <h1 class="text-lg font-bold text-gray-800">Tambah Pengeluaran</h1>
                    <p class="text-sm text-gray-500">Catat pengeluaran harian baru</p>
                </div>
            </div>
        </div>
    </div>

    <div class="p-4 max-w-2xl mx-auto">
        <!-- Form -->
        <form action="{{ route('daily-expenses.store') }}" method="POST" class="space-y-4">
            @csrf

            <!-- Basic Info Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 space-y-4">
                <h3 class="text-lg font-semibold text-gray-800">Informasi Dasar</h3>
                
                <!-- Nama Pengeluaran -->
                <div>
                    <label for="nama_pengeluaran" class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Pengeluaran <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="nama_pengeluaran" 
                           name="nama_pengeluaran" 
                           value="{{ old('nama_pengeluaran') }}"
                           required
                           class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                           placeholder="Contoh: Belanja Bahan Baku">
                    @error('nama_pengeluaran')
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
                           class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                    @error('tanggal')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Deskripsi -->
                <div>
                    <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-2">
                        Deskripsi (Opsional)
                    </label>
                    <textarea id="deskripsi" 
                              name="deskripsi" 
                              rows="3"
                              class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                              placeholder="Deskripsi tambahan...">{{ old('deskripsi') }}</textarea>
                    @error('deskripsi')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Items Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-800">Detail Pembelian</h3>
                    <button type="button" 
                            @click="addItem()"
                            class="bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded-lg text-sm">
                        <i class="fas fa-plus mr-1"></i>Tambah Item
                    </button>
                </div>

                <div class="space-y-3">
                    <template x-for="(item, index) in items" :key="index">
                        <div class="border border-gray-200 rounded-lg p-3 space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-700">Item <span x-text="index + 1"></span></span>
                                <button type="button" 
                                        @click="removeItem(index)"
                                        x-show="items.length > 1"
                                        class="text-red-500 hover:text-red-700 text-sm">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <!-- Nama Bahan -->
                                <div class="sm:col-span-2">
                                    <label class="block text-xs font-medium text-gray-700 mb-1">
                                        Nama Bahan <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" 
                                           :name="`items[${index}][nama_bahan]`"
                                           x-model="item.nama_bahan"
                                           required
                                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                           placeholder="Contoh: Gula pasir">
                                </div>

                                <!-- Qty -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">
                                        Qty/Satuan <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" 
                                           :name="`items[${index}][qty]`"
                                           x-model="item.qty"
                                           required
                                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                           placeholder="Contoh: 1kg">
                                </div>

                                <!-- Harga Satuan -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">
                                        Harga Satuan <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 text-sm">Rp</span>
                                        <input type="number" 
                                               :name="`items[${index}][harga_satuan]`"
                                               x-model="item.harga_satuan"
                                               @input="calculateSubtotal(index)"
                                               required
                                               min="0"
                                               step="0.01"
                                               class="w-full pl-10 pr-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                               placeholder="0">
                                    </div>
                                </div>
                            </div>

                            <!-- Subtotal (Read Only) -->
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Subtotal</label>
                                <div class="w-full px-3 py-2 text-sm bg-gray-50 border border-gray-200 rounded-lg">
                                    <span x-text="formatRupiah(item.subtotal)"></span>
                                </div>
                                <input type="hidden" 
                                       :name="`items[${index}][subtotal]`"
                                       :value="item.subtotal">
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Total -->
                <div class="border-t border-gray-200 pt-4">
                    <div class="flex justify-between items-center">
                        <span class="text-lg font-semibold text-gray-800">Total Keseluruhan:</span>
                        <span class="text-xl font-bold text-green-600" x-text="formatRupiah(totalAmount)"></span>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 space-y-3">
                <button type="submit" 
                        class="w-full bg-red-500 hover:bg-red-600 text-white font-medium py-3 px-4 rounded-lg transition-colors">
                    <i class="fas fa-save mr-2"></i>Simpan Pengeluaran
                </button>
                <a href="{{ route('daily-expenses.index') }}" 
                   class="block w-full bg-gray-500 hover:bg-gray-600 text-white font-medium py-3 px-4 rounded-lg text-center transition-colors">
                    <i class="fas fa-times mr-2"></i>Batal
                </a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function expenseForm() {
    return {
        items: [
            {
                nama_bahan: '',
                qty: '',
                harga_satuan: 0,
                subtotal: 0
            }
        ],

        get totalAmount() {
            return this.items.reduce((sum, item) => sum + (parseFloat(item.subtotal) || 0), 0);
        },

        addItem() {
            this.items.push({
                nama_bahan: '',
                qty: '',
                harga_satuan: 0,
                subtotal: 0
            });
        },

        removeItem(index) {
            if (this.items.length > 1) {
                this.items.splice(index, 1);
            }
        },

        calculateSubtotal(index) {
            const item = this.items[index];
            const harga = parseFloat(item.harga_satuan) || 0;
            item.subtotal = harga;
        },

        formatRupiah(amount) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount || 0);
        }
    }
}
</script>
@endpush
@endsection