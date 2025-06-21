<div class="space-y-2" @click.away="mobileMenuOpen = false">
    <a href="{{ route('dashboard') }}" 
       class="mobile-menu-link flex items-center space-x-3 p-3 rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'bg-red-50 text-red-600' : 'text-gray-700 hover:bg-gray-50' }}"
       @click="mobileMenuOpen = false">
        <i class="fas fa-home w-5"></i>
        <span>Dashboard</span>
    </a>

    @if(auth()->user()->canProcessTransactions())
        <a href="{{ route('cashier') }}" 
           class="mobile-menu-link flex items-center space-x-3 p-3 rounded-lg transition-colors {{ request()->routeIs('cashier*') ? 'bg-red-50 text-red-600' : 'text-gray-700 hover:bg-gray-50' }}"
           @click="mobileMenuOpen = false">
            <i class="fas fa-cash-register w-5"></i>
            <span>Kasir</span>
        </a>
    @endif

    @if(auth()->user()->canManageProducts())
        <a href="{{ route('products.index') }}" 
           class="mobile-menu-link flex items-center space-x-3 p-3 rounded-lg transition-colors {{ request()->routeIs('products*') ? 'bg-red-50 text-red-600' : 'text-gray-700 hover:bg-gray-50' }}"
           @click="mobileMenuOpen = false">
            <i class="fas fa-box w-5"></i>
            <span>Produk</span>
        </a>
    @endif

    @if(auth()->user()->canViewReports())
        <!-- Transactions -->
        <a href="{{ route('transactions.index') }}" 
           class="mobile-menu-link flex items-center space-x-3 p-3 rounded-lg transition-colors {{ request()->routeIs('transactions*') ? 'bg-red-50 text-red-600' : 'text-gray-700 hover:bg-gray-50' }}"
           @click="mobileMenuOpen = false">
            <i class="fas fa-list w-5"></i>
            <span>Transaksi</span>
        </a>

        <!-- Reports -->
        <div class="space-y-1">
            <div class="flex items-center space-x-3 p-3 text-gray-500 text-sm font-medium">
                <i class="fas fa-chart-bar w-5"></i>
                <span>Laporan</span>
            </div>
            <div class="ml-8 space-y-1">
                <a href="{{ route('reports.daily') }}" 
                   class="mobile-menu-link block p-2 text-sm rounded transition-colors {{ request()->routeIs('reports.daily') ? 'bg-red-50 text-red-600' : 'text-gray-600 hover:bg-gray-50' }}"
                   @click="mobileMenuOpen = false">
                    Harian
                </a>
                <a href="{{ route('reports.busiest-hours') }}" 
                   class="mobile-menu-link block p-2 text-sm rounded transition-colors {{ request()->routeIs('reports.busiest-hours') ? 'bg-red-50 text-red-600' : 'text-gray-600 hover:bg-gray-50' }}"
                   @click="mobileMenuOpen = false">
                    Jam Tersibuk
                </a>
                <a href="{{ route('reports.best-selling') }}" 
                   class="mobile-menu-link block p-2 text-sm rounded transition-colors {{ request()->routeIs('reports.best-selling') ? 'bg-red-50 text-red-600' : 'text-gray-600 hover:bg-gray-50' }}"
                   @click="mobileMenuOpen = false">
                    Terlaris
                </a>
                <a href="{{ route('reports.financial') }}" 
                   class="mobile-menu-link block p-2 text-sm rounded transition-colors {{ request()->routeIs('reports.financial') ? 'bg-red-50 text-red-600' : 'text-gray-600 hover:bg-gray-50' }}"
                   @click="mobileMenuOpen = false">
                    Keuangan
                </a>
            </div>
        </div>
    @endif

    @if(auth()->user()->canManageUsers())
        <a href="{{ route('users.index') }}" 
           class="mobile-menu-link flex items-center space-x-3 p-3 rounded-lg transition-colors {{ request()->routeIs('users*') ? 'bg-red-50 text-red-600' : 'text-gray-700 hover:bg-gray-50' }}"
           @click="mobileMenuOpen = false">
            <i class="fas fa-users w-5"></i>
            <span>Kelola User</span>
        </a>
    @endif

    <hr class="my-4">

    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" 
                class="mobile-menu-link flex items-center space-x-3 p-3 rounded-lg text-red-600 hover:bg-red-50 w-full text-left transition-colors"
                @click="mobileMenuOpen = false">
            <i class="fas fa-sign-out-alt w-5"></i>
            <span>Logout</span>
        </button>
    </form>
</div>