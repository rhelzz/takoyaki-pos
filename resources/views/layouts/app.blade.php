<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Takoyaki POS')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#fef2f2',
                            500: '#ef4444',
                            600: '#dc2626',
                            700: '#b91c1c',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 min-h-screen" x-data="{ mobileMenuOpen: false }">
    <!-- Mobile Navigation -->
    <nav class="bg-white shadow-lg border-b fixed top-0 left-0 right-0 z-50">
        <div class="px-4 py-3">
            <div class="flex items-center justify-between">
                <!-- Logo -->
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-red-500 rounded-full flex items-center justify-center">
                        <i class="fas fa-store text-white text-sm"></i>
                    </div>
                    <h1 class="text-lg font-bold text-gray-800">Takoyaki POS</h1>
                </div>

                <!-- Mobile Menu Button -->
                <button @click="mobileMenuOpen = !mobileMenuOpen" 
                        class="lg:hidden p-2 rounded-md hover:bg-gray-100 transition-colors">
                    <div class="w-6 h-6 flex flex-col justify-center items-center">
                        <!-- Hamburger Icon -->
                        <span class="block w-5 h-0.5 bg-gray-600 transition-all duration-300 ease-out"
                              :class="mobileMenuOpen ? 'rotate-45 translate-y-1' : '-translate-y-0.5'"></span>
                        <span class="block w-5 h-0.5 bg-gray-600 my-0.5 transition-all duration-300 ease-out"
                              :class="mobileMenuOpen ? 'opacity-0' : 'opacity-100'"></span>
                        <span class="block w-5 h-0.5 bg-gray-600 transition-all duration-300 ease-out"
                              :class="mobileMenuOpen ? '-rotate-45 -translate-y-1' : 'translate-y-0.5'"></span>
                    </div>
                </button>

                <!-- Desktop Menu -->
                <div class="hidden lg:flex items-center space-x-6">
                    @auth
                        <div class="flex items-center space-x-4">
                            <span class="text-sm text-gray-600">{{ auth()->user()->name }}</span>
                            <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full">
                                {{ auth()->user()->role_label }}
                            </span>
                            <form action="{{ route('logout') }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-red-600 hover:text-red-800 text-sm">
                                    <i class="fas fa-sign-out-alt mr-1"></i>Logout
                                </button>
                            </form>
                        </div>
                    @endauth
                </div>
            </div>
        </div>

        <!-- Mobile Menu Overlay -->
        <div x-show="mobileMenuOpen" 
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="lg:hidden fixed inset-0 bg-black bg-opacity-50 z-40"
             @click="mobileMenuOpen = false"
             style="display: none;">
        </div>

        <!-- Mobile Menu Sidebar -->
        <div x-show="mobileMenuOpen"
             x-transition:enter="transition ease-in-out duration-300 transform"
             x-transition:enter-start="-translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in-out duration-300 transform"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="-translate-x-full"
             class="lg:hidden fixed top-0 left-0 h-full w-56 bg-white shadow-xl z-50 overflow-y-auto"
             style="display: none;">
            
            <!-- Mobile Menu Header -->
            <div class="p-3 border-b bg-red-500 text-white">
                <div class="flex items-center justify-between">
                    <h2 class="text-base font-bold">Menu</h2>
                    <button @click="mobileMenuOpen = false" 
                            class="p-1 rounded hover:bg-red-600 transition-colors">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>
                @auth
                    <div class="mt-2">
                        <p class="text-sm opacity-90">{{ auth()->user()->name }}</p>
                        <p class="text-xs opacity-75">{{ auth()->user()->role_label }}</p>
                    </div>
                @endauth
            </div>

            <!-- Mobile Menu Items -->
            <div class="p-3">
                @auth
                    @include('layouts.partials.mobile-menu')
                @endauth
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="pt-16 pb-20 lg:pb-4">
        @yield('content')
    </main>

    <!-- Bottom Navigation (Mobile Only) -->
    @auth
        <nav class="lg:hidden fixed bottom-0 left-0 right-0 bg-white border-t shadow-lg z-40">
            <div class="flex">
                <a href="{{ route('dashboard') }}" 
                   class="flex-1 flex flex-col items-center py-2 {{ request()->routeIs('dashboard') ? 'text-red-500' : 'text-gray-600' }}">
                    <i class="fas fa-home text-lg mb-1"></i>
                    <span class="text-xs">Dashboard</span>
                </a>
                
                @if(auth()->user()->canProcessTransactions())
                    <a href="{{ route('cashier') }}" 
                       class="flex-1 flex flex-col items-center py-2 {{ request()->routeIs('cashier*') ? 'text-red-500' : 'text-gray-600' }}">
                        <i class="fas fa-cash-register text-lg mb-1"></i>
                        <span class="text-xs">Kasir</span>
                    </a>
                @endif

                @if(auth()->user()->canViewReports())
                    <a href="{{ route('transactions.index') }}" 
                       class="flex-1 flex flex-col items-center py-2 {{ request()->routeIs('transactions*') ? 'text-red-500' : 'text-gray-600' }}">
                        <i class="fas fa-list text-lg mb-1"></i>
                        <span class="text-xs">Transaksi</span>
                    </a>
                @endif

                @if(auth()->user()->canManageProducts())
                    <a href="{{ route('products.index') }}" 
                       class="flex-1 flex flex-col items-center py-2 {{ request()->routeIs('products*') ? 'text-red-500' : 'text-gray-600' }}">
                        <i class="fas fa-box text-lg mb-1"></i>
                        <span class="text-xs">Produk</span>
                    </a>
                @endif

                @if(auth()->user()->canViewReports())
                    <a href="{{ route('reports.index') }}" 
                       class="flex-1 flex flex-col items-center py-2 {{ request()->routeIs('reports*') ? 'text-red-500' : 'text-gray-600' }}">
                        <i class="fas fa-chart-bar text-lg mb-1"></i>
                        <span class="text-xs">Laporan</span>
                    </a>
                @endif
            </div>
        </nav>
    @endauth

    <!-- Toast Notifications -->
    <div id="toast-container" class="fixed top-20 right-4 z-50 space-y-2"></div>

    <script>
        // Toast notification function
        function showToast(message, type = 'success') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            
            const bgColor = type === 'success' ? 'bg-green-500' : 
                           type === 'error' ? 'bg-red-500' : 
                           type === 'warning' ? 'bg-yellow-500' : 'bg-blue-500';
            
            toast.className = `${bgColor} text-white px-4 py-3 rounded-lg shadow-lg transform transition-all duration-300 translate-x-full`;
            toast.innerHTML = `
                <div class="flex items-center justify-between">
                    <span class="text-sm">${message}</span>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-3 text-white hover:text-gray-200">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            
            container.appendChild(toast);
            
            // Animate in
            setTimeout(() => {
                toast.classList.remove('translate-x-full');
            }, 100);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                toast.classList.add('translate-x-full');
                setTimeout(() => {
                    if (toast.parentElement) {
                        toast.remove();
                    }
                }, 300);
            }, 5000);
        }

        // Show Laravel flash messages as toasts
        @if(session('success'))
            showToast("{{ session('success') }}", 'success');
        @endif

        @if(session('error'))
            showToast("{{ session('error') }}", 'error');
        @endif

        @if(session('warning'))
            showToast("{{ session('warning') }}", 'warning');
        @endif

        // Close mobile menu when clicking on nav links
        document.addEventListener('DOMContentLoaded', function() {
            const navLinks = document.querySelectorAll('.mobile-menu-link');
            navLinks.forEach(link => {
                link.addEventListener('click', function() {
                    // Close mobile menu using Alpine.js
                    this.dispatchEvent(new CustomEvent('close-mobile-menu'));
                });
            });
        });
    </script>

    @stack('scripts')
</body>
</html>