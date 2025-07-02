@extends('layouts.app')

@section('title', $dailyExpense->nama_pengeluaran . ' - Takoyaki POS')

@section('content')
<div class="min-h-screen bg-gray-50" x-data="expenseDetail()">
    <!-- Mobile Header -->
    <div class="bg-white border-b border-gray-200 sticky top-0 z-10">
        <div class="px-4 py-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <a href="{{ route('daily-expenses.index') }}" 
                       class="p-2 text-gray-600 hover:text-gray-800 -ml-2">
                        <i class="fas fa-arrow-left text-lg"></i>
                    </a>
                    <div>
                        <h1 class="text-lg font-bold text-gray-800">Detail Pengeluaran</h1>
                        <p class="text-xs text-gray-500">{{ $dailyExpense->formatted_tanggal }}</p>
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
                <a href="{{ route('daily-expenses.edit', $dailyExpense) }}" 
                   class="flex items-center px-3 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg text-sm">
                    <i class="fas fa-edit mr-2"></i>Edit Pengeluaran
                </a>
                <button @click="deleteExpense()"
                        class="flex items-center px-3 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg text-sm">
                    <i class="fas fa-trash mr-2"></i>Hapus Pengeluaran
                </button>
            </div>

            <!-- Desktop Actions -->
            <div class="hidden sm:flex items-center space-x-3 mt-3">
                <a href="{{ route('daily-expenses.edit', $dailyExpense) }}" 
                   class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg text-sm">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
                <button @click="deleteExpense()"
                        class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm">
                    <i class="fas fa-trash mr-2"></i>Hapus
                </button>
            </div>
        </div>
    </div>

    <div class="p-4 max-w-4xl mx-auto space-y-4">
        <!-- Expense Info Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 lg:p-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Left Column -->
                <div class="space-y-4">
                    <div>
                        <h2 class="text-xl lg:text-2xl font-bold text-gray-800 mb-2">
                            {{ $dailyExpense->nama_pengeluaran }}
                        </h2>
                        <div class="flex items-center space-x-4 text-sm text-gray-600">
                            <div class="flex items-center">
                                <i class="fas fa-calendar mr-2"></i>
                                {{ $dailyExpense->formatted_tanggal }}
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-shopping-cart mr-2"></i>
                                {{ $dailyExpense->items->count() }} item
                            </div>
                        </div>
                    </div>

                    @if($dailyExpense->deskripsi)
                        <div>
                            <h3 class="text-sm font-medium text-gray-700 mb-2">Deskripsi</h3>
                            <p class="text-gray-600 text-sm">{{ $dailyExpense->deskripsi }}</p>
                        </div>
                    @endif
                </div>

                <!-- Right Column - Summary -->
                <div class="lg:border-l lg:border-gray-200 lg:pl-6">
                    <div class="bg-green-50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Total Pengeluaran</h3>
                        <div class="text-2xl lg:text-3xl font-bold text-green-600">
                            {{ $dailyExpense->formatted_total }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Items List -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-4 lg:p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Detail Pembelian</h3>
            </div>

            <!-- Desktop Table -->
            <div class="hidden lg:block overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Bahan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty/Satuan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga Satuan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($dailyExpense->items as $item)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm font-medium text-gray-800">
                                    {{ $item->nama_bahan }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $item->qty }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $item->formatted_harga_satuan }}
                                </td>
                                <td class="px-6 py-4 text-sm font-medium text-green-600">
                                    {{ $item->formatted_subtotal }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-sm font-semibold text-gray-800 text-right">
                                Total:
                            </td>
                            <td class="px-6 py-4 text-sm font-bold text-green-600">
                                {{ $dailyExpense->formatted_total }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Mobile Cards -->
            <div class="lg:hidden">
                @foreach($dailyExpense->items as $index => $item)
                    <div class="p-4 border-b border-gray-100 last:border-b-0">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h4 class="font-medium text-gray-800 mb-2">{{ $item->nama_bahan }}</h4>
                                <div class="space-y-1 text-sm text-gray-600">
                                    <div class="flex justify-between">
                                        <span>Qty/Satuan:</span>
                                        <span>{{ $item->qty }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Harga Satuan:</span>
                                        <span>{{ $item->formatted_harga_satuan }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="text-right ml-4">
                                <div class="text-sm text-gray-500 mb-1">Subtotal</div>
                                <div class="font-semibold text-green-600">{{ $item->formatted_subtotal }}</div>
                            </div>
                        </div>
                    </div>
                @endforeach

                <!-- Mobile Total -->
                <div class="p-4 bg-gray-50 border-t border-gray-200">
                    <div class="flex justify-between items-center">
                        <span class="text-lg font-semibold text-gray-800">Total:</span>
                        <span class="text-xl font-bold text-green-600">{{ $dailyExpense->formatted_total }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Back to List -->
        <div class="text-center">
            <a href="{{ route('daily-expenses.index') }}" 
               class="inline-flex items-center px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white rounded-lg font-medium">
                <i class="fas fa-arrow-left mr-2"></i>Kembali ke Daftar
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script>
function expenseDetail() {
    return {
        showActions: false,

        deleteExpense() {
            if (confirm('Apakah Anda yakin ingin menghapus pengeluaran ini?')) {
                fetch('{{ route("daily-expenses.destroy", $dailyExpense) }}', {
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
                            window.location.href = '{{ route("daily-expenses.index") }}';
                        }, 1000);
                    } else {
                        showToast(data.message || 'Terjadi kesalahan', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('Gagal menghapus pengeluaran', 'error');
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