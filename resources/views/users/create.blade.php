@extends('layouts.app')

@section('title', 'Tambah User - Takoyaki POS')

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
                <h1 class="text-2xl font-bold text-gray-800">Tambah User</h1>
                <p class="text-gray-600">Buat akun pengguna baru</p>
            </div>
        </div>
    </div>

    <!-- Form -->
    <form action="{{ route('users.store') }}" method="POST" 
          class="bg-white rounded-lg shadow p-6" x-data="userForm()">
        @csrf

        <!-- User Avatar Preview -->
        <div class="mb-6 text-center">
            <div class="w-20 h-20 bg-gradient-to-br from-red-400 to-red-600 rounded-full flex items-center justify-center text-white font-bold text-2xl mx-auto mb-2"
                 x-text="avatarInitial">
                U
            </div>
            <p class="text-sm text-gray-500">Avatar akan dibuat otomatis dari nama</p>
        </div>

        <!-- Full Name -->
        <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                Nama Lengkap <span class="text-red-500">*</span>
            </label>
            <input type="text" 
                   id="name" 
                   name="name" 
                   value="{{ old('name') }}"
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
                   value="{{ old('email') }}"
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
                Password <span class="text-red-500">*</span>
            </label>
            <div class="relative">
                <input type="password" 
                       id="password" 
                       name="password" 
                       required
                       minlength="8"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 pr-12"
                       placeholder="Minimal 8 karakter">
                <button type="button" 
                        @click="togglePassword('password')"
                        class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                    <i class="fas fa-eye" id="passwordIcon"></i>
                </button>
            </div>
            @error('password')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div class="mb-4">
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                Konfirmasi Password <span class="text-red-500">*</span>
            </label>
            <div class="relative">
                <input type="password" 
                       id="password_confirmation" 
                       name="password_confirmation" 
                       required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 pr-12"
                       placeholder="Ketik ulang password">
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
        <div class="mb-6">
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
                <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Administrator</option>
                <option value="manager" {{ old('role') === 'manager' ? 'selected' : '' }}>Manager</option>
                <option value="cashier" {{ old('role') === 'cashier' ? 'selected' : '' }}>Kasir</option>
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

        <!-- Quick Role Buttons -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-3">Pilih Cepat:</label>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-3">
                <button type="button" 
                        @click="selectRole('admin')"
                        class="p-4 border-2 border-purple-200 rounded-lg hover:border-purple-400 hover:bg-purple-50 text-center transition-colors">
                    <i class="fas fa-crown text-purple-600 text-2xl mb-2"></i>
                    <h3 class="font-medium text-purple-800">Administrator</h3>
                    <p class="text-xs text-purple-600 mt-1">Akses penuh sistem</p>
                </button>

                <button type="button" 
                        @click="selectRole('manager')"
                        class="p-4 border-2 border-blue-200 rounded-lg hover:border-blue-400 hover:bg-blue-50 text-center transition-colors">
                    <i class="fas fa-user-tie text-blue-600 text-2xl mb-2"></i>
                    <h3 class="font-medium text-blue-800">Manager</h3>
                    <p class="text-xs text-blue-600 mt-1">Kelola produk & laporan</p>
                </button>

                <button type="button" 
                        @click="selectRole('cashier')"
                        class="p-4 border-2 border-green-200 rounded-lg hover:border-green-400 hover:bg-green-50 text-center transition-colors">
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
                <i class="fas fa-save mr-2"></i>Buat User
            </button>
            <a href="{{ route('users.index') }}" 
               class="flex-1 bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-lg text-center transition-colors">
                <i class="fas fa-times mr-2"></i>Batal
            </a>
        </div>
    </form>
</div>

@push('scripts')
<script>
function userForm() {
    return {
        name: '{{ old("name") }}',
        selectedRole: '{{ old("role") }}',
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
        }
    }
}
</script>
@endpush
@endsection