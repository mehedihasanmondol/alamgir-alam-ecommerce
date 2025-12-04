<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Prevent Admin Panel from being indexed by search engines -->
    <meta name="robots" content="noindex, nofollow">
    <meta name="googlebot" content="noindex, nofollow">
    
    {{-- Allow Livewire to work in development --}}
    <meta http-equiv="Content-Security-Policy" content="default-src * 'unsafe-inline' 'unsafe-eval'; script-src * 'unsafe-inline' 'unsafe-eval'; connect-src * 'unsafe-inline'; img-src * data: blob: 'unsafe-inline'; frame-src *; style-src * 'unsafe-inline';">

    <title>@yield('title', 'Admin Panel') - {{ config('app.name', 'Laravel') }}</title>

    <!-- Favicon -->
    @php
        $favicon = \App\Models\SiteSetting::get('site_favicon');
    @endphp
    @if($favicon)
        <link rel="icon" type="image/x-icon" href="{{ asset('storage/' . $favicon) }}">
        <link rel="shortcut icon" type="image/x-icon" href="{{ asset('storage/' . $favicon) }}">
    @endif

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- TODO: Install Font Awesome locally via npm -->
    <!-- Temporary CDN - Replace with: npm install @fortawesome/fontawesome-free -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    
    <!-- CropperJS CSS for Image Uploader -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/2.1.0/cropper.min.css" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/admin.js', 'resources/js/ckeditor-init.js'])
    
    <!-- Alpine.js Collapse Plugin -->
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    
    @livewireStyles
    
    <!-- Page-specific styles -->
    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-100" x-data="{ sidebarOpen: true, mobileMenuOpen: false }">
    <!-- Toast Notification -->
    <x-toast-notification />
    
    <div class="min-h-screen">
        <!-- Top Navigation Bar -->
        <nav class="bg-white shadow-sm border-b border-gray-200 fixed w-full z-30">
            <div class="px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <!-- Left Side -->
                    <div class="flex items-center">
                        <!-- Sidebar Toggle -->
                        <button @click="sidebarOpen = !sidebarOpen" 
                                class="hidden lg:block text-gray-500 hover:text-gray-700 focus:outline-none mr-4">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                        
                        <!-- Mobile Menu Toggle -->
                        <button @click="mobileMenuOpen = !mobileMenuOpen" 
                                class="lg:hidden text-gray-500 hover:text-gray-700 focus:outline-none mr-4">
                            <i class="fas fa-bars text-xl"></i>
                        </button>

                        <!-- Logo -->
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center">
                            @php
                                $adminLogo = \App\Models\SiteSetting::get('admin_logo');
                                $siteName = \App\Models\SiteSetting::get('site_name', 'Laravel');
                            @endphp
                            
                            @if($adminLogo)
                                <img src="{{ asset('storage/' . $adminLogo) }}" 
                                     alt="{{ $siteName }} Admin" 
                                     class="h-10 w-auto">
                            @else
                                <i class="fas fa-shield-alt text-2xl text-blue-600 mr-2"></i>
                                <span class="text-xl font-bold text-gray-900">Admin Panel</span>
                            @endif
                        </a>
                    </div>

                    <!-- Right Side -->
                    <div class="flex items-center space-x-4">
                        <!-- Maintenance Mode Alert (Small) -->
                        @php
                            $maintenanceMode = \App\Models\SystemSetting::get('maintenance_mode', false);
                        @endphp
                        @if($maintenanceMode)
                        <div class="hidden md:flex items-center bg-orange-100 text-orange-800 px-3 py-1.5 rounded-lg text-xs font-medium border border-orange-200">
                            <svg class="w-4 h-4 mr-1.5 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            <span>Maintenance Mode</span>
                        </div>
                        @endif
                        
                        <!-- Global Admin Search -->
                        <div class="hidden md:block w-80">
                            @livewire('admin.global-admin-search')
                        </div>

                        <!-- Notifications -->
                        <button class="text-gray-500 hover:text-gray-700 relative">
                            <i class="fas fa-bell text-xl"></i>
                            <span class="absolute top-0 right-0 block h-2 w-2 rounded-full bg-red-500"></span>
                        </button>

                        <!-- User Dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" 
                                    class="flex items-center space-x-2 text-sm font-medium text-gray-700 hover:text-gray-900 focus:outline-none">
                                @if(auth()->user()->media)
                                    <img src="{{ auth()->user()->media->small_url }}" 
                                         alt="{{ auth()->user()->name }}"
                                         class="h-8 w-8 rounded-full object-cover border-2 border-gray-200">
                                @elseif(auth()->user()->avatar)
                                    <img src="{{ asset('storage/' . auth()->user()->avatar) }}" 
                                         alt="{{ auth()->user()->name }}"
                                         class="h-8 w-8 rounded-full object-cover border-2 border-gray-200">
                                @else
                                    <div class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold">
                                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                    </div>
                                @endif
                                <span class="hidden md:block">{{ auth()->user()->name }}</span>
                                <i class="fas fa-chevron-down text-xs hidden md:block"></i>
                            </button>

                            <div x-show="open" 
                                 @click.away="open = false"
                                 x-transition
                                 class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-1 z-50">
                                <a href="{{ route('admin.profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-user mr-2"></i>Profile
                                </a>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-cog mr-2"></i>Settings
                                </a>
                                <hr class="my-1">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" 
               class="fixed left-0 top-16 h-[calc(100vh-4rem)] w-64 bg-white border-r border-gray-200 transition-transform duration-300 ease-in-out z-20 hidden lg:block overflow-y-auto">
            <nav class="p-4 space-y-1">
                <!-- Dashboard -->
                <a href="{{ route('admin.dashboard') }}" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <i class="fas fa-tachometer-alt w-5 mr-3"></i>
                    <span>Dashboard</span>
                </a>

                <!-- User Management Section -->
                @if(auth()->user()->hasPermission('users.view'))
                <div class="pt-4 pb-2">
                    <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">User Management</p>
                </div>
                
                <a href="{{ route('admin.users.index') }}" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.users.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <i class="fas fa-users w-5 mr-3"></i>
                    <span>Users</span>
                    @if(request()->routeIs('admin.users.*'))
                        <i class="fas fa-chevron-right ml-auto text-xs"></i>
                    @endif
                </a>

                <a href="{{ route('admin.roles.index') }}" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.roles.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <i class="fas fa-shield-alt w-5 mr-3"></i>
                    <span>Roles & Permissions</span>
                    @if(request()->routeIs('admin.roles.*'))
                        <i class="fas fa-chevron-right ml-auto text-xs"></i>
                    @endif
                </a>

                <a href="{{ route('admin.email-preferences.index') }}" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.email-preferences.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <i class="fas fa-envelope-open-text w-5 mr-3"></i>
                    <span>Email Preferences</span>
                    @if(request()->routeIs('admin.email-preferences.*'))
                        <i class="fas fa-chevron-right ml-auto text-xs"></i>
                    @endif
                </a>
                @endif

                <!-- E-commerce Section (Placeholder) -->
                @if(auth()->user()->hasPermission('products.view'))
                <div class="pt-4 pb-2">
                    <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">E-commerce</p>
                </div>
                
                <a href="{{ route('admin.products.index') }}" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.products.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <i class="fas fa-box w-5 mr-3"></i>
                    <span>Products</span>
                    @if(request()->routeIs('admin.products.*'))
                        <i class="fas fa-chevron-right ml-auto text-xs"></i>
                    @endif
                </a>

                <a href="{{ route('admin.orders.index') }}" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.orders.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <i class="fas fa-shopping-cart w-5 mr-3"></i>
                    <span>Orders</span>
                    @if(request()->routeIs('admin.orders.*'))
                        <i class="fas fa-chevron-right ml-auto text-xs"></i>
                    @endif
                </a>

                <!-- Reports Section -->
                <div x-data="{ open: {{ request()->routeIs('admin.reports.*') ? 'true' : 'false' }} }" class="space-y-1">
                    <button @click="open = !open" 
                            class="w-full flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.reports.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                        <i class="fas fa-chart-line w-5 mr-3"></i>
                        <span class="flex-1 text-left">Reports & Analytics</span>
                        <i class="fas fa-chevron-down text-xs transition-transform" :class="open ? 'rotate-180' : ''"></i>
                    </button>
                    
                    <div x-show="open" x-collapse class="ml-4 space-y-1 border-l-2 border-gray-200 pl-2">
                        <a href="{{ route('admin.reports.index') }}" 
                           class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('admin.reports.index') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
                            <i class="fas fa-tachometer-alt w-4 mr-2 text-xs"></i>
                            <span>Dashboard</span>
                        </a>
                        <a href="{{ route('admin.reports.sales') }}" 
                           class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('admin.reports.sales') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
                            <i class="fas fa-dollar-sign w-4 mr-2 text-xs"></i>
                            <span>Sales Report</span>
                        </a>
                        <a href="{{ route('admin.reports.products') }}" 
                           class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('admin.reports.products') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
                            <i class="fas fa-box w-4 mr-2 text-xs"></i>
                            <span>Product Performance</span>
                        </a>
                        <a href="{{ route('admin.reports.inventory') }}" 
                           class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('admin.reports.inventory') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
                            <i class="fas fa-warehouse w-4 mr-2 text-xs"></i>
                            <span>Inventory Report</span>
                        </a>
                        <a href="{{ route('admin.reports.customers') }}" 
                           class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('admin.reports.customers') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
                            <i class="fas fa-users w-4 mr-2 text-xs"></i>
                            <span>Customer Report</span>
                        </a>
                        <a href="{{ route('admin.reports.delivery') }}" 
                           class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors {{ request()->routeIs('admin.reports.delivery') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
                            <i class="fas fa-truck w-4 mr-2 text-xs"></i>
                            <span>Delivery Report</span>
                        </a>
                    </div>
                </div>

                <a href="{{ route('admin.categories.index') }}" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.categories.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <i class="fas fa-tags w-5 mr-3"></i>
                    <span>Categories</span>
                    @if(request()->routeIs('admin.categories.*'))
                        <i class="fas fa-chevron-right ml-auto text-xs"></i>
                    @endif
                </a>

                <a href="{{ route('admin.brands.index') }}" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.brands.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <i class="fas fa-copyright w-5 mr-3"></i>
                    <span>Brands</span>
                    @if(request()->routeIs('admin.brands.*'))
                        <i class="fas fa-chevron-right ml-auto text-xs"></i>
                    @endif
                </a>

                <a href="{{ route('admin.attributes.index') }}" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.attributes.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <i class="fas fa-sliders-h w-5 mr-3"></i>
                    <span>Attributes</span>
                    @if(request()->routeIs('admin.attributes.*'))
                        <i class="fas fa-chevron-right ml-auto text-xs"></i>
                    @endif
                </a>

                <a href="{{ route('admin.product-questions.index') }}" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.product-questions.*') || request()->routeIs('admin.questions.*') || request()->routeIs('admin.answers.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <i class="fas fa-question-circle w-5 mr-3"></i>
                    <span>Product Q&A</span>
                    @if(request()->routeIs('admin.product-questions.*') || request()->routeIs('admin.questions.*') || request()->routeIs('admin.answers.*'))
                        <i class="fas fa-chevron-right ml-auto text-xs"></i>
                    @endif
                </a>

                <a href="{{ route('admin.reviews.index') }}" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.reviews.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <i class="fas fa-star w-5 mr-3"></i>
                    <span>Product Reviews</span>
                    @if(request()->routeIs('admin.reviews.*'))
                        <i class="fas fa-chevron-right ml-auto text-xs"></i>
                    @endif
                </a>
                @endif

                @if(auth()->user()->hasPermission('orders.view'))
                <a href="{{ route('admin.coupons.index') }}" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.coupons.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <i class="fas fa-ticket-alt w-5 mr-3"></i>
                    <span>Coupons</span>
                    @if(request()->routeIs('admin.coupons.*'))
                        <i class="fas fa-chevron-right ml-auto text-xs"></i>
                    @endif
                </a>

                <!-- Shipping & Delivery Section -->
                <div class="pt-4 pb-2">
                    <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Shipping & Delivery</p>
                </div>
                
                <a href="{{ route('admin.delivery.zones.index') }}" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.delivery.zones.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <i class="fas fa-map-marked-alt w-5 mr-3"></i>
                    <span>Delivery Zones</span>
                    @if(request()->routeIs('admin.delivery.zones.*'))
                        <i class="fas fa-chevron-right ml-auto text-xs"></i>
                    @endif
                </a>

                <a href="{{ route('admin.delivery.methods.index') }}" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.delivery.methods.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <i class="fas fa-shipping-fast w-5 mr-3"></i>
                    <span>Delivery Methods</span>
                    @if(request()->routeIs('admin.delivery.methods.*'))
                        <i class="fas fa-chevron-right ml-auto text-xs"></i>
                    @endif
                </a>

                <a href="{{ route('admin.delivery.rates.index') }}" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.delivery.rates.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <i class="fas fa-dollar-sign w-5 mr-3"></i>
                    <span>Delivery Rates</span>
                    @if(request()->routeIs('admin.delivery.rates.*'))
                        <i class="fas fa-chevron-right ml-auto text-xs"></i>
                    @endif
                </a>
                @endif

                <!-- Payments Section -->
                @if(auth()->user()->hasPermission('orders.view'))
                <div class="pt-4 pb-2">
                    <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Payments</p>
                </div>
                
                <a href="{{ route('admin.payment-gateways.index') }}" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.payment-gateways.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <i class="fas fa-credit-card w-5 mr-3"></i>
                    <span>Payment Gateways</span>
                    @if(request()->routeIs('admin.payment-gateways.*'))
                        <i class="fas fa-chevron-right ml-auto text-xs"></i>
                    @endif
                </a>
                @endif

                <!-- Inventory Section -->
                @if(auth()->user()->hasPermission('stock.view'))
                <div class="pt-4 pb-2">
                    <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Inventory</p>
                </div>
                
                <a href="{{ route('admin.stock.index') }}" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.stock.*') || request()->routeIs('admin.warehouses.*') || request()->routeIs('admin.suppliers.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <i class="fas fa-boxes w-5 mr-3"></i>
                    <span>Stock Management</span>
                    @php
                        $stockAlerts = \App\Modules\Stock\Models\StockAlert::where('status', 'pending')->count();
                    @endphp
                    @if($stockAlerts > 0)
                        <span class="ml-auto text-xs bg-red-500 text-white px-2 py-1 rounded-full">{{ $stockAlerts }}</span>
                    @endif
                    @if(request()->routeIs('admin.stock.*') || request()->routeIs('admin.warehouses.*') || request()->routeIs('admin.suppliers.*'))
                        <i class="fas fa-chevron-right ml-auto text-xs"></i>
                    @endif
                </a>

                <a href="{{ route('admin.warehouses.index') }}" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.warehouses.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <i class="fas fa-warehouse w-5 mr-3"></i>
                    <span>Warehouses</span>
                    @if(request()->routeIs('admin.warehouses.*'))
                        <i class="fas fa-chevron-right ml-auto text-xs"></i>
                    @endif
                </a>

                <a href="{{ route('admin.suppliers.index') }}" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.suppliers.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <i class="fas fa-truck w-5 mr-3"></i>
                    <span>Suppliers</span>
                    @if(request()->routeIs('admin.suppliers.*'))
                        <i class="fas fa-chevron-right ml-auto text-xs"></i>
                    @endif
                </a>

                <a href="{{ route('admin.stock.reports.index') }}" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.stock.reports.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <i class="fas fa-chart-bar w-5 mr-3"></i>
                    <span>Stock Reports</span>
                    @if(request()->routeIs('admin.stock.reports.*'))
                        <i class="fas fa-chevron-right ml-auto text-xs"></i>
                    @endif
                </a>
                @endif

                <!-- Content Section (Placeholder) -->
                @if(auth()->user()->hasPermission('users.view'))
                <div class="pt-4 pb-2">
                    <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Content</p>
                </div>
                
                <a href="{{ route('admin.homepage-settings.index') }}" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.homepage-settings.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <i class="fas fa-home w-5 mr-3"></i>
                    <span>Homepage Settings</span>
                    @if(request()->routeIs('admin.homepage-settings.*'))
                        <i class="fas fa-chevron-right ml-auto text-xs"></i>
                    @endif
                </a>

                <a href="{{ route('admin.secondary-menu.index') }}" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.secondary-menu.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <i class="fas fa-bars w-5 mr-3"></i>
                    <span>Secondary Menu</span>
                    @if(request()->routeIs('admin.secondary-menu.*'))
                        <i class="fas fa-chevron-right ml-auto text-xs"></i>
                    @endif
                </a>

                <a href="{{ route('admin.sale-offers.index') }}" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.sale-offers.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <i class="fas fa-tag w-5 mr-3"></i>
                    <span>Sale Offers</span>
                    @if(request()->routeIs('admin.sale-offers.*'))
                        <i class="fas fa-chevron-right ml-auto text-xs"></i>
                    @endif
                </a>

                <a href="{{ route('admin.trending-products.index') }}" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.trending-products.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <i class="fas fa-fire w-5 mr-3"></i>
                    <span>Trending Products</span>
                    @if(request()->routeIs('admin.trending-products.*'))
                        <i class="fas fa-chevron-right ml-auto text-xs"></i>
                    @endif
                </a>

                <a href="{{ route('admin.best-seller-products.index') }}" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.best-seller-products.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <i class="fas fa-trophy w-5 mr-3"></i>
                    <span>Best Sellers</span>
                    @if(request()->routeIs('admin.best-seller-products.*'))
                        <i class="fas fa-chevron-right ml-auto text-xs"></i>
                    @endif
                </a>

                <a href="{{ route('admin.new-arrival-products.index') }}" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.new-arrival-products.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <i class="fas fa-star w-5 mr-3"></i>
                    <span>New Arrivals</span>
                    @if(request()->routeIs('admin.new-arrival-products.*'))
                        <i class="fas fa-chevron-right ml-auto text-xs"></i>
                    @endif
                </a>

                <a href="{{ route('admin.footer-management.index') }}" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.footer-management.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <i class="fas fa-shoe-prints w-5 mr-3"></i>
                    <span>Footer Management</span>
                    @if(request()->routeIs('admin.footer-management.*'))
                        <i class="fas fa-chevron-right ml-auto text-xs"></i>
                    @endif
                </a>
                @endif

                <!-- Blog Section -->
                @if(auth()->user()->hasPermission('posts.view'))
                <div class="pt-4 pb-2">
                    <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Blog</p>
                </div>
                
                <a href="{{ route('admin.blog.posts.index') }}" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.blog.posts.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <i class="fas fa-file-alt w-5 mr-3"></i>
                    <span>Posts</span>
                    @if(request()->routeIs('admin.blog.posts.*'))
                        <i class="fas fa-chevron-right ml-auto text-xs"></i>
                    @endif
                </a>

                <a href="{{ route('admin.blog.categories.index') }}" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.blog.categories.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <i class="fas fa-folder w-5 mr-3"></i>
                    <span>Categories</span>
                    @if(request()->routeIs('admin.blog.categories.*'))
                        <i class="fas fa-chevron-right ml-auto text-xs"></i>
                    @endif
                </a>

                <a href="{{ route('admin.blog.tags.index') }}" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.blog.tags.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <i class="fas fa-tag w-5 mr-3"></i>
                    <span>Tags</span>
                    @if(request()->routeIs('admin.blog.tags.*'))
                        <i class="fas fa-chevron-right ml-auto text-xs"></i>
                    @endif
                </a>

                <a href="{{ route('admin.blog.comments.index') }}" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.blog.comments.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <i class="fas fa-comments w-5 mr-3"></i>
                    <span>Comments</span>
                    @if(request()->routeIs('admin.blog.comments.*'))
                        <i class="fas fa-chevron-right ml-auto text-xs"></i>
                    @endif
                </a>
                @endif

                <!-- Feedback Section -->
                @if(auth()->user()->hasPermission('feedback.view'))
                <div class="pt-4 pb-2">
                    <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Feedback</p>
                </div>

                <a href="{{ route('admin.feedback.index') }}" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.feedback.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <i class="fas fa-star w-5 mr-3"></i>
                    <span>Customer Feedback</span>
                    @php
                        try {
                            $pendingFeedbackCount = \App\Models\Feedback::where('status', 'pending')->count();
                        } catch (\Exception $e) {
                            $pendingFeedbackCount = 0;
                        }
                    @endphp
                    @if($pendingFeedbackCount > 0)
                        <span class="ml-auto bg-orange-500 text-white text-xs px-2 py-1 rounded-full">{{ $pendingFeedbackCount }}</span>
                    @endif
                    @if(request()->routeIs('admin.feedback.*'))
                        <i class="fas fa-chevron-right ml-auto text-xs"></i>
                    @endif
                </a>
                @endif

                <!-- Appointments Section -->
                @if(auth()->user()->hasPermission('appointments.view') || auth()->user()->hasPermission('chambers.manage'))
                <div class="pt-4 pb-2">
                    <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Appointments</p>
                </div>

                @if(auth()->user()->hasPermission('appointments.view'))
                <a href="{{ route('admin.appointments.index') }}" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.appointments.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <i class="fas fa-calendar-check w-5 mr-3"></i>
                    <span>Appointments</span>
                    @php
                        try {
                            $pendingAppointments = \App\Models\Appointment::where('status', 'pending')->count();
                        } catch (\Exception $e) {
                            $pendingAppointments = 0;
                        }
                    @endphp
                    @if($pendingAppointments > 0)
                        <span class="ml-auto bg-orange-500 text-white text-xs px-2 py-1 rounded-full">{{ $pendingAppointments }}</span>
                    @endif
                </a>
                @endif

                @if(auth()->user()->hasPermission('chambers.manage'))
                <a href="{{ route('admin.chambers.index') }}" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.chambers.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <i class="fas fa-building w-5 mr-3"></i>
                    <span>Chambers</span>
                </a>
                @endif
                @endif

                <!-- Finance Section (Placeholder) -->
                @if(auth()->user()->hasPermission('finance.view'))
                <div class="pt-4 pb-2">
                    <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Finance</p>
                </div>
                
                <a href="#" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg text-gray-400 cursor-not-allowed">
                    <i class="fas fa-dollar-sign w-5 mr-3"></i>
                    <span>Transactions</span>
                    <span class="ml-auto text-xs bg-gray-200 px-2 py-1 rounded">Soon</span>
                </a>

                <a href="#" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg text-gray-400 cursor-not-allowed">
                    <i class="fas fa-chart-line w-5 mr-3"></i>
                    <span>Reports</span>
                    <span class="ml-auto text-xs bg-gray-200 px-2 py-1 rounded">Soon</span>
                </a>
                @endif

                <!-- Communication Section -->
                @if(auth()->user()->hasPermission('users.view'))
                <div class="pt-4 pb-2">
                    <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Communication</p>
                </div>
                
                <a href="{{ route('admin.contact.messages.index') }}" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.contact.messages.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <i class="fas fa-envelope w-5 mr-3"></i>
                    <span>Contact Messages</span>
                    @php
                        try {
                            $unreadMessagesCount = \App\Models\ContactMessage::where('status', 'unread')->count();
                        } catch (\Exception $e) {
                            $unreadMessagesCount = 0;
                        }
                    @endphp
                    @if($unreadMessagesCount > 0)
                        <span class="ml-auto bg-blue-500 text-white text-xs px-2 py-1 rounded-full">{{ $unreadMessagesCount }}</span>
                    @endif
                    @if(request()->routeIs('admin.contact.messages.*'))
                        <i class="fas fa-chevron-right {{ $unreadMessagesCount > 0 ? '' : 'ml-auto' }} text-xs"></i>
                    @endif
                </a>
                @endif

                <!-- Settings Section -->
                @if(auth()->user()->hasPermission('users.view') || auth()->user()->hasPermission('system.settings.view'))
                <div class="pt-4 pb-2">
                    <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">System</p>
                </div>
                
                @if(auth()->user()->hasPermission('users.view'))
                <a href="{{ route('admin.site-settings.index') }}" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.site-settings.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <i class="fas fa-cog w-5 mr-3"></i>
                    <span>Site Settings</span>
                    @if(request()->routeIs('admin.site-settings.*'))
                        <i class="fas fa-chevron-right ml-auto text-xs"></i>
                    @endif
                </a>

                <a href="{{ route('admin.theme-settings.index') }}" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.theme-settings.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <i class="fas fa-palette w-5 mr-3"></i>
                    <span>Theme Settings</span>
                    @if(request()->routeIs('admin.theme-settings.*'))
                        <i class="fas fa-chevron-right ml-auto text-xs"></i>
                    @endif
                </a>
                @endif

                @if(auth()->user()->hasPermission('system.settings.view'))
                <a href="{{ route('admin.system-settings.index') }}" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.system-settings.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <i class="fas fa-server w-5 mr-3"></i>
                    <span>System Settings</span>
                    @if(request()->routeIs('admin.system-settings.*'))
                        <i class="fas fa-chevron-right ml-auto text-xs"></i>
                    @endif
                </a>
                @endif
                @endif
            </nav>
        </aside>

        <!-- Mobile Sidebar -->
        <aside x-show="mobileMenuOpen" 
               @click.away="mobileMenuOpen = false"
               x-transition:enter="transition ease-out duration-300"
               x-transition:enter-start="-translate-x-full"
               x-transition:enter-end="translate-x-0"
               x-transition:leave="transition ease-in duration-300"
               x-transition:leave-start="translate-x-0"
               x-transition:leave-end="-translate-x-full"
               class="fixed left-0 top-16 h-[calc(100vh-4rem)] w-64 bg-white border-r border-gray-200 z-40 lg:hidden overflow-y-auto">
            <nav class="p-4 space-y-1">
                <!-- Same navigation as desktop sidebar -->
                <a href="{{ route('admin.dashboard') }}" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ request()->routeIs('admin.dashboard') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <i class="fas fa-tachometer-alt w-5 mr-3"></i>
                    <span>Dashboard</span>
                </a>

                @if(auth()->user()->hasPermission('users.view'))
                <div class="pt-4 pb-2">
                    <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">User Management</p>
                </div>
                
                <a href="{{ route('admin.users.index') }}" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ request()->routeIs('admin.users.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <i class="fas fa-users w-5 mr-3"></i>
                    <span>Users</span>
                </a>

                <a href="{{ route('admin.roles.index') }}" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ request()->routeIs('admin.roles.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <i class="fas fa-shield-alt w-5 mr-3"></i>
                    <span>Roles & Permissions</span>
                </a>

                <a href="{{ route('admin.email-preferences.index') }}" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ request()->routeIs('admin.email-preferences.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <i class="fas fa-envelope-open-text w-5 mr-3"></i>
                    <span>Email Preferences</span>
                </a>
                @endif

                @if(auth()->user()->hasPermission('products.view'))
                <div class="pt-4 pb-2">
                    <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">E-commerce</p>
                </div>
                
                <a href="{{ route('admin.products.index') }}" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ request()->routeIs('admin.products.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <i class="fas fa-box w-5 mr-3"></i>
                    <span>Products</span>
                </a>

                <a href="{{ route('admin.orders.index') }}" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ request()->routeIs('admin.orders.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <i class="fas fa-shopping-cart w-5 mr-3"></i>
                    <span>Orders</span>
                </a>

                <!-- Reports Section (Mobile) -->
                <div x-data="{ open: {{ request()->routeIs('admin.reports.*') ? 'true' : 'false' }} }" class="space-y-1">
                    <button @click="open = !open" 
                            class="w-full flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.reports.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                        <i class="fas fa-chart-line w-5 mr-3"></i>
                        <span class="flex-1 text-left">Reports & Analytics</span>
                        <i class="fas fa-chevron-down text-xs transition-transform" :class="open ? 'rotate-180' : ''"></i>
                    </button>
                    
                    <div x-show="open" x-collapse class="ml-4 space-y-1 border-l-2 border-gray-200 pl-2">
                        <a href="{{ route('admin.reports.index') }}" 
                           class="flex items-center px-3 py-2 text-sm rounded-lg {{ request()->routeIs('admin.reports.index') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
                            <i class="fas fa-tachometer-alt w-4 mr-2 text-xs"></i>
                            <span>Dashboard</span>
                        </a>
                        <a href="{{ route('admin.reports.sales') }}" 
                           class="flex items-center px-3 py-2 text-sm rounded-lg {{ request()->routeIs('admin.reports.sales') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
                            <i class="fas fa-dollar-sign w-4 mr-2 text-xs"></i>
                            <span>Sales Report</span>
                        </a>
                        <a href="{{ route('admin.reports.products') }}" 
                           class="flex items-center px-3 py-2 text-sm rounded-lg {{ request()->routeIs('admin.reports.products') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
                            <i class="fas fa-box w-4 mr-2 text-xs"></i>
                            <span>Product Performance</span>
                        </a>
                        <a href="{{ route('admin.reports.inventory') }}" 
                           class="flex items-center px-3 py-2 text-sm rounded-lg {{ request()->routeIs('admin.reports.inventory') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
                            <i class="fas fa-warehouse w-4 mr-2 text-xs"></i>
                            <span>Inventory Report</span>
                        </a>
                        <a href="{{ route('admin.reports.customers') }}" 
                           class="flex items-center px-3 py-2 text-sm rounded-lg {{ request()->routeIs('admin.reports.customers') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
                            <i class="fas fa-users w-4 mr-2 text-xs"></i>
                            <span>Customer Report</span>
                        </a>
                        <a href="{{ route('admin.reports.delivery') }}" 
                           class="flex items-center px-3 py-2 text-sm rounded-lg {{ request()->routeIs('admin.reports.delivery') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
                            <i class="fas fa-truck w-4 mr-2 text-xs"></i>
                            <span>Delivery Report</span>
                        </a>
                    </div>
                </div>

                <a href="{{ route('admin.categories.index') }}" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ request()->routeIs('admin.categories.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <i class="fas fa-tags w-5 mr-3"></i>
                    <span>Categories</span>
                </a>

                <a href="{{ route('admin.brands.index') }}" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ request()->routeIs('admin.brands.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <i class="fas fa-copyright w-5 mr-3"></i>
                    <span>Brands</span>
                </a>

                <a href="{{ route('admin.attributes.index') }}" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ request()->routeIs('admin.attributes.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <i class="fas fa-sliders-h w-5 mr-3"></i>
                    <span>Attributes</span>
                </a>

                <a href="{{ route('admin.product-questions.index') }}" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ request()->routeIs('admin.product-questions.*') || request()->routeIs('admin.questions.*') || request()->routeIs('admin.answers.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <i class="fas fa-question-circle w-5 mr-3"></i>
                    <span>Product Q&A</span>
                </a>

                <a href="{{ route('admin.reviews.index') }}" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ request()->routeIs('admin.reviews.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <i class="fas fa-star w-5 mr-3"></i>
                    <span>Product Reviews</span>
                </a>
                @endif

                @if(auth()->user()->hasPermission('orders.view'))
                <div class="pt-4 pb-2">
                    <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Shipping & Delivery</p>
                </div>
                
                <a href="{{ route('admin.delivery.zones.index') }}" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ request()->routeIs('admin.delivery.zones.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <i class="fas fa-map-marked-alt w-5 mr-3"></i>
                    <span>Delivery Zones</span>
                </a>

                <a href="{{ route('admin.delivery.methods.index') }}" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ request()->routeIs('admin.delivery.methods.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <i class="fas fa-shipping-fast w-5 mr-3"></i>
                    <span>Delivery Methods</span>
                </a>

                <a href="{{ route('admin.delivery.rates.index') }}" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ request()->routeIs('admin.delivery.rates.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <i class="fas fa-dollar-sign w-5 mr-3"></i>
                    <span>Delivery Rates</span>
                </a>
                @endif

                @if(auth()->user()->hasPermission('stock.view'))
                <div class="pt-4 pb-2">
                    <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Inventory</p>
                </div>
                
                <a href="{{ route('admin.stock.index') }}" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ request()->routeIs('admin.stock.*') || request()->routeIs('admin.warehouses.*') || request()->routeIs('admin.suppliers.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <i class="fas fa-boxes w-5 mr-3"></i>
                    <span>Stock Management</span>
                </a>

                <a href="{{ route('admin.warehouses.index') }}" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ request()->routeIs('admin.warehouses.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <i class="fas fa-warehouse w-5 mr-3"></i>
                    <span>Warehouses</span>
                </a>

                <a href="{{ route('admin.suppliers.index') }}" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ request()->routeIs('admin.suppliers.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <i class="fas fa-truck w-5 mr-3"></i>
                    <span>Suppliers</span>
                </a>

                <a href="{{ route('admin.stock.reports.index') }}" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ request()->routeIs('admin.stock.reports.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <i class="fas fa-chart-bar w-5 mr-3"></i>
                    <span>Stock Reports</span>
                </a>
                @endif

                @if(auth()->user()->hasPermission('users.view'))
                <div class="pt-4 pb-2">
                    <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Content</p>
                </div>
                
                <a href="{{ route('admin.homepage-settings.index') }}" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ request()->routeIs('admin.homepage-settings.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <i class="fas fa-home w-5 mr-3"></i>
                    <span>Homepage Settings</span>
                </a>

                <a href="{{ route('admin.secondary-menu.index') }}" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ request()->routeIs('admin.secondary-menu.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <i class="fas fa-bars w-5 mr-3"></i>
                    <span>Secondary Menu</span>
                </a>
                @endif

                @if(auth()->user()->hasPermission('posts.view'))
                <div class="pt-4 pb-2">
                    <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Blog</p>
                </div>
                
                <a href="{{ route('admin.blog.posts.index') }}" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ request()->routeIs('admin.blog.posts.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <i class="fas fa-file-alt w-5 mr-3"></i>
                    <span>Posts</span>
                </a>

                <a href="{{ route('admin.blog.categories.index') }}" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ request()->routeIs('admin.blog.categories.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <i class="fas fa-folder w-5 mr-3"></i>
                    <span>Categories</span>
                </a>

                <a href="{{ route('admin.blog.tags.index') }}" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ request()->routeIs('admin.blog.tags.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <i class="fas fa-tag w-5 mr-3"></i>
                    <span>Tags</span>
                </a>

                <a href="{{ route('admin.blog.comments.index') }}" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ request()->routeIs('admin.blog.comments.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <i class="fas fa-comments w-5 mr-3"></i>
                    <span>Comments</span>
                </a>
                @endif

                @if(auth()->user()->hasPermission('feedback.view'))
                <div class="pt-4 pb-2">
                    <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Feedback</p>
                </div>

                <a href="{{ route('admin.feedback.index') }}" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ request()->routeIs('admin.feedback.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <i class="fas fa-star w-5 mr-3"></i>
                    <span>Customer Feedback</span>
                    @php
                        try {
                            $pendingFeedbackCount = \App\Models\Feedback::where('status', 'pending')->count();
                        } catch (\Exception $e) {
                            $pendingFeedbackCount = 0;
                        }
                    @endphp
                    @if($pendingFeedbackCount > 0)
                        <span class="ml-auto bg-orange-500 text-white text-xs px-2 py-1 rounded-full">{{ $pendingFeedbackCount }}</span>
                    @endif
                </a>
                @endif

                @if(auth()->user()->hasPermission('appointments.view') || auth()->user()->hasPermission('chambers.manage'))
                <div class="pt-4 pb-2">
                    <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Appointments</p>
                </div>

                @if(auth()->user()->hasPermission('appointments.view'))
                <a href="{{ route('admin.appointments.index') }}" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ request()->routeIs('admin.appointments.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <i class="fas fa-calendar-check w-5 mr-3"></i>
                    <span>Appointments</span>
                    @php
                        try {
                            $pendingAppointments = \App\Models\Appointment::where('status', 'pending')->count();
                        } catch (\Exception $e) {
                            $pendingAppointments = 0;
                        }
                    @endphp
                    @if($pendingAppointments > 0)
                        <span class="ml-auto bg-orange-500 text-white text-xs px-2 py-1 rounded-full">{{ $pendingAppointments }}</span>
                    @endif
                </a>
                @endif

                @if(auth()->user()->hasPermission('chambers.manage'))
                <a href="{{ route('admin.chambers.index') }}" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ request()->routeIs('admin.chambers.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    <i class="fas fa-building w-5 mr-3"></i>
                    <span>Chambers</span>
                </a>
                @endif
                @endif
            </nav>
        </aside>

        <!-- Page Content -->
        <main :class="sidebarOpen ? 'lg:ml-64' : 'lg:ml-0'" class="pt-16 transition-all duration-300 ease-in-out min-h-screen">
            <div class="py-6">
                <!-- Flash Messages -->
                @if(session('success'))
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-4">
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center justify-between">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle mr-3"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button onclick="this.parentElement.parentElement.remove()" class="text-green-600 hover:text-green-800">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            @endif

            @if(session('error'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-4">
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg flex items-center justify-between">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle mr-3"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button onclick="this.parentElement.parentElement.remove()" class="text-red-600 hover:text-red-800">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            @endif

            @if($errors->any())
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-4">
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                    <div class="flex items-start">
                        <i class="fas fa-exclamation-circle mr-3 mt-1"></i>
                        <div>
                            <p class="font-semibold">There were some errors with your submission:</p>
                            <ul class="mt-2 list-disc list-inside text-sm">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            @endif

                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    @yield('content')
                </div>
            </div>
        </main>
    </div>

    @livewireScripts

    <!-- Alert Components -->
    <x-confirm-modal />

    @stack('scripts')

    <!-- Auto-scroll to active menu item -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Function to scroll sidebar to active menu item
            function scrollToActiveMenuItem(sidebar) {
                if (!sidebar) return;
                
                // Find the active menu item within this sidebar
                const activeMenuItem = sidebar.querySelector('nav a.bg-blue-50');
                
                if (activeMenuItem) {
                    // Small delay to ensure sidebar is fully rendered
                    setTimeout(() => {
                        // Calculate the position to scroll to
                        const sidebarRect = sidebar.getBoundingClientRect();
                        const menuItemRect = activeMenuItem.getBoundingClientRect();
                        
                        // Calculate the offset from the top of the sidebar
                        const offsetTop = menuItemRect.top - sidebarRect.top + sidebar.scrollTop;
                        
                        // Calculate the center position (subtract half of sidebar height)
                        const sidebarHeight = sidebar.clientHeight;
                        const scrollPosition = offsetTop - (sidebarHeight / 2) + (activeMenuItem.offsetHeight / 2);
                        
                        // Smooth scroll to the active menu item
                        sidebar.scrollTo({
                            top: Math.max(0, scrollPosition),
                            behavior: 'smooth'
                        });
                    }, 100);
                }
            }
            
            // Handle desktop sidebar (always visible on desktop)
            const desktopSidebar = document.querySelector('aside.hidden.lg\\:block');
            if (desktopSidebar) {
                scrollToActiveMenuItem(desktopSidebar);
            }
            
            // Handle mobile sidebar (when it becomes visible)
            const mobileSidebar = document.querySelector('aside.lg\\:hidden');
            if (mobileSidebar) {
                // Create a MutationObserver to watch for when mobile sidebar becomes visible
                const observer = new MutationObserver(function(mutations) {
                    mutations.forEach(function(mutation) {
                        if (mutation.type === 'attributes' && mutation.attributeName === 'style') {
                            // Check if sidebar is now visible (not display: none)
                            const isVisible = !mobileSidebar.style.display || mobileSidebar.style.display !== 'none';
                            if (isVisible && mobileSidebar.offsetParent !== null) {
                                scrollToActiveMenuItem(mobileSidebar);
                            }
                        }
                    });
                });
                
                // Start observing
                observer.observe(mobileSidebar, {
                    attributes: true,
                    attributeFilter: ['style', 'class']
                });
                
                // Also scroll immediately if mobile sidebar is already visible
                if (mobileSidebar.offsetParent !== null) {
                    scrollToActiveMenuItem(mobileSidebar);
                }
            }
        });
    </script>
</body>
</html>
