@extends('layouts.app')

@section('title', 'Edit User - Takoyaki POS')

@section('content')
<div class="p-4 max-w-2xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center mb-4">
            <a href="{{ route('users.index') }}" 
               class="mr-3 p-2 text-gray-600 hover:text-gray-800">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Edit User</h1>
                <p class="text-gray-600">Edit {{ $user->name }}</p>
            </div>
        </div>
    </div>

    <!-- User Stats -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Statistik User</h2>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="text-center p-3 bg-blue-50 rounded-lg">
                <p class="text-2xl font-bold text-blue-600">{{ $user->total_transactions }}</p>
                <p class="text-sm text-gray-600">Total Transaksi</p>
            </div>
            <div class="text-center p-3 bg-green-50 rounded-lg">
                <p class="text-lg font-bold text-green-600">{{ number_format($user->total_revenue, 0, ',', '.') }}</p>
                <p class="text-sm text-gray-600">Total Pendapatan</p>
            </div>
            <div class="text-center p-3 bg-yellow-50 rounded-lg">
                <p class="text-2xl font-bold text-yellow-600">{{ $user->this_month_transactions_count }}</p>
                <p class="text-sm text-gray-600">Transaksi Bulan Ini</p>
            </div>
            <div class="text-center p-3 bg-purple-50 rounded-lg">
                <p class="text-sm font-bold text-purple-600">{{ $user->created_at->format('d/m/Y') }}</p>
                <p class="text-sm text-gray-600">Bergabung</p>
            </div>
        </div>
    </div>

    <!-- Form -->
    <form action="{{ route('users.update', $user) }}" method="POST" 
          class="bg-white rounded-lg shadow p-6" x-data="userForm()">
        @csrf
        @method('PUT')

        <!-- User Avatar Preview -->
        <div class="mb-6 text-center">
            <div class="w-20 h-20 bg-gradient-to-br from-red-400 to-red-600 rounded-full flex items-center justify-center text-white font-bold text-2xl mx-auto mb-2"
                 x-text="avatarInitial">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <p class="text-sm text-gray-500">Avatar akan diperbarui otomatis</p>
        </div>

        <!-- Full Name -->
        <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                Nama Lengkap <span class="text-red-500">*</span>
            </label>
            <input type="text" 
                   id="name" 
                   name="name" 
                   value="{{ old('name', $user->name) }}"
                   required
                   x-model="name"
                   @input="updateAvatar()"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                   placeholder="Masukkan nama lengkap">
            @error('name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Email -->
        <div class="mb-4">
            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                Email <span class="text-red-500">*</span>
            </label>
            <input type="email" 
                   id="email" 
                   name="email" 
                   value="{{ old('email', $user->email) }}"
                   required
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                   placeholder="user@example.com">
            @error('email')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div class="mb-4">
            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                Password Baru
            </label>
            <div class="relative">
                <input type="password" 
                       id="password" 
                       name="password" 
                       minlength="8"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 pr-12"
                       placeholder="Kosongkan jika tidak ingin mengubah">
                <button type="button" 
                        @click="togglePassword('password')"
                        class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                    <i class="fas fa-eye" id="passwordIcon"></i>
                </button>
            </div>
            <p class="text-sm text-gray-500 mt-1">Minimal 8 karakter. Kosongkan jika tidak ingin mengubah password.</p>
            @error('password')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div class="mb-4" x-show="document.getElementById('password').value.length > 0">
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                Konfirmasi Password Baru
            </label>
            <div class="relative">
                <input type="password" 
                       id="password_confirmation" 
                       name="password_confirmation" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 pr-12"
                       placeholder="Ketik ulang password baru">
                <button type="button" 
                        @click="togglePassword('password_confirmation')"
                        class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                    <i class="fas fa-eye" id="passwordConfirmationIcon"></i>
                </button>
            </div>
            @error('password_confirmation')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Role -->
        <div class="mb-4">
            <label for="role" class="block text-sm font-medium text-gray-700 mb-2">
                Role <span class="text-red-500">*</span>
            </label>
            <select id="role" 
                    name="role" 
                    required
                    x-model="selectedRole"
                    @change="updateRoleDescription()"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                <option value="">Pilih Role</option>
                <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Administrator</option>
                <option value="manager" {{ old('role', $user->role) === 'manager' ? 'selected' : '' }}>Manager</option>
                <option value="cashier" {{ old('role', $user->role) === 'cashier' ? 'selected' : '' }}>Kasir</option>
            </select>
            @error('role')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror

            <!-- Role Description -->
            <div x-show="roleDescription" 
                 x-transition
                 class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                <h4 class="font-medium text-blue-800 mb-2">Hak Akses Role:</h4>
                <div x-html="roleDescription" class="text-sm text-blue-700"></div>
            </div>
        </div>

        <!-- Status -->
        <div class="mb-6">
            <label class="flex items-center">
                <input type="checkbox" 
                       name="is_active" 
                       value="1" 
                       {{ old('is_active', $user->is_active) ? 'checked' : '' }}
                       @if($user->id === auth()->id()) disabled @endif
                       class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded {{ $user->id === auth()->id() ? 'opacity-50 cursor-not-allowed' : '' }}">
                <span class="ml-2 text-sm text-gray-700">
                    User Aktif
                    @if($user->id === auth()->id())
                        <span class="text-gray-500">(Tidak dapat menonaktifkan akun sendiri)</span>
                    @endif
                </span>
            </label>
            @error('is_active')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Quick Role Buttons -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-3">Pilih Cepat:</label>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-3">
                <button type="button" 
                        @click="selectRole('admin')"
                        :class="selectedRole === 'admin' ? 'border-purple-400 bg-purple-50' : 'border-purple-200 hover:border-purple-400 hover:bg-purple-50'"
                        class="p-4 border-2 rounded-lg text-center transition-colors">
                    <i class="fas fa-crown text-purple-600 text-2xl mb-2"></i>
                    <h3 class="font-medium text-purple-800">Administrator</h3>
                    <p class="text-xs text-purple-600 mt-1">Akses penuh sistem</p>
                </button>

                <button type="button" 
                        @click="selectRole('manager')"
                        :class="selectedRole === 'manager' ? 'border-blue-400 bg-blue-50' : 'border-blue-200 hover:border-blue-400 hover:bg-blue-50'"
                        class="p-4 border-2 rounded-lg text-center transition-colors">
                    <i class="fas fa-user-tie text-blue-600 text-2xl mb-2"></i>
                    <h3 class="font-medium text-blue-800">Manager</h3>
                    <p class="text-xs text-blue-600 mt-1">Kelola produk & laporan</p>
                </button>

                <button type="button" 
                        @click="selectRole('cashier')"
                        :class="selectedRole === 'cashier' ? 'border-green-400 bg-green-50' : 'border-green-200 hover:border-green-400 hover:bg-green-50'"
                        class="p-4 border-2 rounded-lg text-center transition-colors">
                    <i class="fas fa-cash-register text-green-600 text-2xl mb-2"></i>
                    <h3 class="font-medium text-green-800">Kasir</h3>
                    <p class="text-xs text-green-600 mt-1">Akses kasir saja</p>
                </button>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="flex flex-col lg:flex-row lg:space-x-3 space-y-3 lg:space-y-0">
            <button type="submit" 
                    class="flex-1 bg-red-500 hover:bg-red-600 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                <i class="fas fa-save mr-2"></i>Update User
            </button>
            <a href="{{ route('users.index') }}" 
               class="flex-1 bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-lg text-center transition-colors">
                <i class="fas fa-times mr-2"></i>Batal
            </a>
            
            @if($user->id !== auth()->id())
                <button type="button" 
                        @click="deleteUser()"
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors">
                    <i class="fas fa-trash mr-2"></i>Hapus
                </button>
            @endif
        </div>
    </form>
</div>

@push('scripts')
<script>
function userForm() {
    return {
        name: '{{ old("name", $user->name) }}',
        selectedRole: '{{ old("role", $user->role) }}',
        roleDescription: '',

        get avatarInitial() {
            return this.name ? this.name.charAt(0).toUpperCase() : 'U';
        },

        init() {
            this.updateRoleDescription();
        },

        updateAvatar() {
            // Avatar updates automatically via computed property
        },

        selectRole(role) {
            this.selectedRole = role;
            document.getElementById('role').value = role;
            this.updateRoleDescription();
        },

        updateRoleDescription() {
            const descriptions = {
                admin: `
                    <ul class="list-disc list-inside space-y-1">
                        <li>Kelola semua user dan hak akses</li>
                        <li>Akses semua laporan dan analisis</li>
                        <li>Kelola produk dan kategori</li>
                        <li>Akses kasir dan transaksi</li>
                        <li>Pengaturan sistem</li>
                    </ul>
                `,
                manager: `
                    <ul class="list-disc list-inside space-y-1">
                        <li>Lihat dan export laporan</li>
                        <li>Kelola produk dan kategori</li>
                        <li>Akses kasir dan transaksi</li>
                        <li>Tidak bisa kelola user</li>
                    </ul>
                `,
                cashier: `
                    <ul class="list-disc list-inside space-y-1">
                        <li>Akses kasir dan proses transaksi</li>
                        <li>Lihat dashboard basic</li>
                        <li>Tidak bisa kelola produk</li>
                        <li>Tidak bisa lihat laporan</li>
                    </ul>
                `
            };

            this.roleDescription = descriptions[this.selectedRole] || '';
        },

        togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = document.getElementById(fieldId + 'Icon');
            
            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        },

        async deleteUser() {
            if (!confirm('Yakin ingin menghapus user "{{ $user->name }}"? Aksi ini tidak dapat dibatalkan.')) return;

            try {
                const response = await fetch('{{ route("users.destroy", $user) }}', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                if (response.ok) {
                    window.location.href = '{{ route("users.index") }}';
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