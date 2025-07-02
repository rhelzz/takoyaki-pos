@extends('layouts.app')

@section('title', 'Detail Stock Masuk - Takoyaki POS')

@section('content')
<div class="min-h-screen bg-gray-50" x-data="stockDetail()">
    <!-- Mobile Header -->
    <div class="bg-white border-b border-gray-200 sticky top-0 z-10">
        <div class="px-4 py-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <a href="{{ route('stock-masuk.index') }}" 
                       class="p-2 text-gray-600 hover:text-gray-800 -ml-2">
                        <i class="fas fa-arrow-left text-lg"></i>
                    </a>
                    <div>
                        <h1 class="text-lg font-bold text-gray-800">Detail Stock Masuk</h1>
                        <p class="text-xs text-gray-500">{{ $stockMasuk->formatted_tanggal }}</p>
                    </div>
                </div>
                
                <!-- Mobile Action Menu -->
                <div class="flex items-center space-x-2">
                    <button @click="showActions = !showActions" 
                            class="p-2 text-gray-600 hover:text-gray-800">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                </div>
            </div>
            
            <!-- Mobile Actions Dropdown -->
            <div x-show="showActions" 
                 x-transition:enter="transition ease-out duration-100"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="mt-3 flex flex-col space-y-2 sm:hidden">
                <a href="{{ route('stock-masuk.edit', $stockMasuk) }}" 
                   class="flex items-center px-3 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg text-sm">
                    <i class="fas fa-edit mr-2"></i>Edit Stock
                </a>
                <button @click="deleteStock()"
                        class="flex items-center px-3 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg text-sm">
                    <i class="fas fa-trash mr-2"></i>Hapus Stock
                </button>
            </div>

            <!-- Desktop Actions -->
            <div class="hidden sm:flex items-center space-x-3 mt-3">
                <a href="{{ route('stock-masuk.edit', $stockMasuk) }}" 
                   class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg text-sm">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
                <button @click="deleteStock()"
                        class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm">
                    <i class="fas fa-trash mr-2"></i>Hapus
                </button>
            </div>
        </div>
    </div>

    <div class="p-4 max-w-4xl mx-auto space-y-4">
        <!-- Stock Info Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 lg:p-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Left Column -->
                <div class="space-y-4">
                    <div>
                        <h2 class="text-xl lg:text-2xl font-bold text-gray-800 mb-2">
                            {{ $stockMasuk->nama_barang }}
                        </h2>
                        <div class="flex items-center space-x-4 text-sm text-gray-600">
                            <div class="flex items-center">
                                <i class="fas fa-calendar mr-2"></i>
                                {{ $stockMasuk->formatted_tanggal }}
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-arrow-up mr-2 text-blue-500"></i>
                                Stock Masuk
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Summary -->
                <div class="lg:border-l lg:border-gray-200 lg:pl-6">
                    <div class="bg-blue-50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Quantity Masuk</h3>
                        <div class="text-2xl lg:text-3xl font-bold text-blue-600">
                            +{{ $stockMasuk->qty }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Back to List -->
        <div class="text-center">
            <a href="{{ route('stock-masuk.index') }}" 
               class="inline-flex items-center px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white rounded-lg font-medium">
                <i class="fas fa-arrow-left mr-2"></i>Kembali ke Daftar
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script>
function stockDetail() {
    return {
        showActions: false,

        deleteStock() {
            if (confirm('Apakah Anda yakin ingin menghapus stock masuk ini?')) {
                fetch('{{ route("stock-masuk.destroy", $stockMasuk) }}', {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast(data.message, 'success');
                        setTimeout(() => {
                            window.location.href = '{{ route("stock-masuk.index") }}';
                        }, 1000);
                    } else {
                        showToast(data.message || 'Terjadi kesalahan', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('Gagal menghapus stock', 'error');
                });
            }
        },

        init() {
            // Close dropdown when clicking outside
            document.addEventListener('click', (e) => {
                if (!e.target.closest('[x-data]')) {
                    this.showActions = false;
                }
            });
        }
    }
}
</script>
@endpush
@endsection