@extends('layouts.app')

@section('title', 'Kelola User - Takoyaki POS')

@section('content')
<div class="p-4 max-w-7xl mx-auto" x-data="userManagement()">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-6">
        <div class="mb-4 lg:mb-0">
            <h1 class="text-2xl font-bold text-gray-800">Kelola User</h1>
            <p class="text-gray-600">Atur pengguna dan hak akses sistem</p>
        </div>
        <a href="{{ route('users.create') }}" 
           class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg inline-flex items-center justify-center">
            <i class="fas fa-plus mr-2"></i>Tambah User
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow mb-6 p-4">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <!-- Search -->
            <div>
                <input type="text" 
                       x-model="searchQuery"
                       placeholder="Cari nama atau email..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
            </div>

            <!-- Role Filter -->
            <div>
                <select x-model="selectedRole" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                    <option value="">Semua Role</option>
                    <option value="admin">Administrator</option>
                    <option value="manager">Manager</option>
                    <option value="cashier">Kasir</option>
                </select>
            </div>

            <!-- Status Filter -->
            <div>
                <select x-model="selectedStatus" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                    <option value="">Semua Status</option>
                    <option value="1">Aktif</option>
                    <option value="0">Tidak Aktif</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Users Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
        @foreach($users as $user)
            <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow">
                <!-- User Header -->
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <!-- Avatar -->
                            <div class="w-12 h-12 bg-gradient-to-br from-red-400 to-red-600 rounded-full flex items-center justify-center text-white font-bold text-lg">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            
                            <div>
                                <h3 class="font-semibold text-gray-800">{{ $user->name }}</h3>
                                <p class="text-sm text-gray-600">{{ $user->email }}</p>
                            </div>
                        </div>

                        <!-- Status Badge -->
                        <div class="flex flex-col items-end space-y-1">
                            @if($user->is_active)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i>Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <i class="fas fa-times-circle mr-1"></i>Nonaktif
                                </span>
                            @endif

                            <!-- Role Badge -->
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium 
                                {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-800' : 
                                   ($user->role === 'manager' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                                {{ $user->role_label }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- User Info -->
                <div class="p-6">
                    <!-- Permissions -->
                    <div class="mb-4">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Hak Akses:</h4>
                        <div class="space-y-1">
                            @if($user->canProcessTransactions())
                                <div class="flex items-center text-sm text-green-600">
                                    <i class="fas fa-check mr-2"></i>Kasir
                                </div>
                            @endif
                            @if($user->canManageProducts())
                                <div class="flex items-center text-sm text-green-600">
                                    <i class="fas fa-check mr-2"></i>Kelola Produk
                                </div>
                            @endif
                            @if($user->canViewReports())
                                <div class="flex items-center text-sm text-green-600">
                                    <i class="fas fa-check mr-2"></i>Lihat Laporan
                                </div>
                            @endif
                            @if($user->canManageUsers())
                                <div class="flex items-center text-sm text-green-600">
                                    <i class="fas fa-check mr-2"></i>Kelola User
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Statistics -->
                    <div class="mb-4 p-3 bg-gray-50 rounded-lg">
                        <div class="grid grid-cols-2 gap-3 text-center">
                            <div>
                                <p class="text-lg font-bold text-blue-600">{{ $user->today_transactions_count }}</p>
                                <p class="text-xs text-gray-600">Transaksi Hari Ini</p>
                            </div>
                            <div>
                                <p class="text-lg font-bold text-green-600">{{ number_format($user->today_revenue, 0, ',', '.') }}</p>
                                <p class="text-xs text-gray-600">Pendapatan Hari Ini</p>
                            </div>
                        </div>
                    </div>

                    <!-- User Meta -->
                    <div class="text-sm text-gray-600 space-y-1 mb-4">
                        <div class="flex justify-between">
                            <span>Bergabung:</span>
                            <span>{{ $user->created_at->format('d/m/Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Total Transaksi:</span>
                            <span class="font-medium">{{ $user->total_transactions }}</span>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex space-x-2">
                        <a href="{{ route('users.edit', $user) }}" 
                           class="flex-1 bg-blue-500 hover:bg-blue-600 text-white py-2 px-3 rounded text-sm text-center">
                            <i class="fas fa-edit mr-1"></i>Edit
                        </a>

                        @if($user->id !== auth()->id())
                            <button @click="toggleStatus({{ $user->id }}, {{ $user->is_active ? 'false' : 'true' }})"
                                    class="px-3 py-2 rounded text-sm {{ $user->is_active ? 'bg-red-500 hover:bg-red-600' : 'bg-green-500 hover:bg-green-600' }} text-white">
                                <i class="fas {{ $user->is_active ? 'fa-times' : 'fa-check' }}"></i>
                            </button>

                            <button @click="deleteUser({{ $user->id }}, '{{ $user->name }}')"
                                    class="px-3 py-2 rounded text-sm bg-red-600 hover:bg-red-700 text-white">
                                <i class="fas fa-trash"></i>
                            </button>
                        @else
                            <div class="px-3 py-2 rounded text-sm bg-gray-300 text-gray-500 cursor-not-allowed">
                                <i class="fas fa-user-shield"></i>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Empty State -->
    @if($users->count() === 0)
        <div class="text-center py-12">
            <i class="fas fa-users text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada user</h3>
            <p class="text-gray-500 mb-4">Mulai dengan menambahkan user pertama</p>
            <a href="{{ route('users.create') }}" 
               class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg inline-flex items-center">
                <i class="fas fa-plus mr-2"></i>Tambah User
            </a>
        </div>
    @endif
</div>

@push('scripts')
<script>
function userManagement() {
    return {
        searchQuery: '',
        selectedRole: '',
        selectedStatus: '',

        async toggleStatus(userId, newStatus) {
            if (!confirm('Yakin ingin mengubah status user?')) return;

            try {
                const response = await fetch(`/users/${userId}/toggle-status`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                if (response.ok) {
                    location.reload();
                } else {
                    showToast('Gagal mengubah status user', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showToast('Terjadi kesalahan sistem', 'error');
            }
        },

        async deleteUser(userId, userName) {
            if (!confirm(`Yakin ingin menghapus user "${userName}"? Aksi ini tidak dapat dibatalkan.`)) return;

            try {
                const response = await fetch(`/users/${userId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                if (response.ok) {
                    location.reload();
                } else {
                    showToast('Gagal menghapus user', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showToast('Terjadi kesalahan sistem', 'error');
            }
        }
    }
}
</script>
@endpush
@endsection