<div class="space-y-1" @click.away="mobileMenuOpen = false">
    <a href="{{ route('dashboard') }}" 
       class="mobile-menu-link flex items-center space-x-2 p-2 rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'bg-red-50 text-red-600' : 'text-gray-700 hover:bg-gray-50' }}"
       @click="mobileMenuOpen = false">
        <i class="fas fa-home w-4 text-sm"></i>
        <span class="text-sm">Dashboard</span>
    </a>

    @if(auth()->user()->canProcessTransactions())
        <a href="{{ route('cashier') }}" 
           class="mobile-menu-link flex items-center space-x-2 p-2 rounded-lg transition-colors {{ request()->routeIs('cashier*') ? 'bg-red-50 text-red-600' : 'text-gray-700 hover:bg-gray-50' }}"
           @click="mobileMenuOpen = false">
            <i class="fas fa-cash-register w-4 text-sm"></i>
            <span class="text-sm">Kasir</span>
        </a>
    @endif

    @if(auth()->user()->canManageProducts())
        <a href="{{ route('products.index') }}" 
           class="mobile-menu-link flex items-center space-x-2 p-2 rounded-lg transition-colors {{ request()->routeIs('products*') ? 'bg-red-50 text-red-600' : 'text-gray-700 hover:bg-gray-50' }}"
           @click="mobileMenuOpen = false">
            <i class="fas fa-box w-4 text-sm"></i>
            <span class="text-sm">Produk</span>
        </a>

        <a href="{{ route('categories.index') }}" 
           class="mobile-menu-link flex items-center space-x-2 p-2 rounded-lg transition-colors {{ request()->routeIs('categories*') ? 'bg-red-50 text-red-600' : 'text-gray-700 hover:bg-gray-50' }}"
           @click="mobileMenuOpen = false">
            <i class="fas fa-tags w-4 text-sm"></i>
            <span class="text-sm">Kategori</span>
        </a>

        <!-- Stock Management Section -->
        <div class="space-y-0.5">
            <div class="flex items-center space-x-2 p-2 text-gray-500 text-xs font-medium">
                <i class="fas fa-warehouse w-4"></i>
                <span>Stock</span>
            </div>
            <div class="ml-6 space-y-0.5">
                <a href="{{ route('stock-summary.index') }}" 
                   class="mobile-menu-link block p-1.5 text-xs rounded transition-colors {{ request()->routeIs('stock-summary*') ? 'bg-red-50 text-red-600' : 'text-gray-600 hover:bg-gray-50' }}"
                   @click="mobileMenuOpen = false">
                    Summary
                </a>
                <a href="{{ route('stock-masuk.index') }}" 
                   class="mobile-menu-link block p-1.5 text-xs rounded transition-colors {{ request()->routeIs('stock-masuk*') ? 'bg-red-50 text-red-600' : 'text-gray-600 hover:bg-gray-50' }}"
                   @click="mobileMenuOpen = false">
                    Stock Masuk
                </a>
                <a href="{{ route('stock-keluar.index') }}" 
                   class="mobile-menu-link block p-1.5 text-xs rounded transition-colors {{ request()->routeIs('stock-keluar*') ? 'bg-red-50 text-red-600' : 'text-gray-600 hover:bg-gray-50' }}"
                   @click="mobileMenuOpen = false">
                    Stock Keluar
                </a>
            </div>
        </div>
    @endif

    @if(auth()->user()->canViewReports())
        <!-- Transactions -->
        <a href="{{ route('transactions.index') }}" 
           class="mobile-menu-link flex items-center space-x-2 p-2 rounded-lg transition-colors {{ request()->routeIs('transactions*') ? 'bg-red-50 text-red-600' : 'text-gray-700 hover:bg-gray-50' }}"
           @click="mobileMenuOpen = false">
            <i class="fas fa-list w-4 text-sm"></i>
            <span class="text-sm">Transaksi</span>
        </a>

        <!-- Reports -->
        <div class="space-y-0.5">
            <div class="flex items-center space-x-2 p-2 text-gray-500 text-xs font-medium">
                <i class="fas fa-chart-bar w-4"></i>
                <span>Laporan</span>
            </div>
            <div class="ml-6 space-y-0.5">
                <a href="{{ route('reports.daily') }}" 
                   class="mobile-menu-link block p-1.5 text-xs rounded transition-colors {{ request()->routeIs('reports.daily') ? 'bg-red-50 text-red-600' : 'text-gray-600 hover:bg-gray-50' }}"
                   @click="mobileMenuOpen = false">
                    Harian
                </a>
                <a href="{{ route('reports.busiest-hours') }}" 
                   class="mobile-menu-link block p-1.5 text-xs rounded transition-colors {{ request()->routeIs('reports.busiest-hours') ? 'bg-red-50 text-red-600' : 'text-gray-600 hover:bg-gray-50' }}"
                   @click="mobileMenuOpen = false">
                    Jam Tersibuk
                </a>
                <a href="{{ route('reports.best-selling') }}" 
                   class="mobile-menu-link block p-1.5 text-xs rounded transition-colors {{ request()->routeIs('reports.best-selling') ? 'bg-red-50 text-red-600' : 'text-gray-600 hover:bg-gray-50' }}"
                   @click="mobileMenuOpen = false">
                    Terlaris
                </a>
                <a href="{{ route('reports.financial') }}" 
                   class="mobile-menu-link block p-1.5 text-xs rounded transition-colors {{ request()->routeIs('reports.financial') ? 'bg-red-50 text-red-600' : 'text-gray-600 hover:bg-gray-50' }}"
                   @click="mobileMenuOpen = false">
                    Keuangan
                </a>
            </div>
        </div>
    @endif

    @if(auth()->user()->canManageUsers())
        <a href="{{ route('users.index') }}" 
           class="mobile-menu-link flex items-center space-x-2 p-2 rounded-lg transition-colors {{ request()->routeIs('users*') ? 'bg-red-50 text-red-600' : 'text-gray-700 hover:bg-gray-50' }}"
           @click="mobileMenuOpen = false">
            <i class="fas fa-users w-4 text-sm"></i>
            <span class="text-sm">Kelola User</span>
        </a>
    @endif

    <hr class="my-3">

    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" 
                class="mobile-menu-link flex items-center space-x-2 p-2 rounded-lg text-red-600 hover:bg-red-50 w-full text-left transition-colors"
                @click="mobileMenuOpen = false">
            <i class="fas fa-sign-out-alt w-4 text-sm"></i>
            <span class="text-sm">Logout</span>
        </button>
    </form>
</div>