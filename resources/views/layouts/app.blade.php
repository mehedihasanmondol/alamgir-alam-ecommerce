<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Laravel'))</title>

    <!-- Favicon -->
    @php
        $favicon = \App\Models\SiteSetting::get('site_favicon');
    @endphp
    @if($favicon)
        <link rel="icon" type="image/x-icon" href="{{ asset('storage/' . $favicon) }}">
        <link rel="shortcut icon" type="image/x-icon" href="{{ asset('storage/' . $favicon) }}">
    @endif

    <!-- SEO Meta Tags -->
    <meta name="description" content="@yield('description', \App\Models\SiteSetting::get('site_description', 'Shop health, wellness and beauty products'))">
    <meta name="keywords" content="@yield('keywords', \App\Models\SiteSetting::get('site_keywords', 'health, wellness, beauty, supplements'))">
    <meta name="author" content="@yield('author', \App\Models\SiteSetting::get('site_name', config('app.name')))">
    
    <!-- Canonical URL -->
    <link rel="canonical" href="@yield('canonical', url()->current())">
    
    <!-- Robots Meta -->
    <meta name="robots" content="@yield('robots', 'index, follow')">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:url" content="@yield('og_url', url()->current())">
    <meta property="og:title" content="@yield('og_title', config('app.name'))">
    <meta property="og:description" content="@yield('og_description', \App\Models\SiteSetting::get('site_description', 'Shop health, wellness and beauty products'))">
    <meta property="og:image" content="@yield('og_image', asset('images/og-default.jpg'))">
    <meta property="og:site_name" content="{{ \App\Models\SiteSetting::get('site_name', config('app.name')) }}">
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="@yield('twitter_card', 'summary_large_image')">
    <meta name="twitter:url" content="@yield('twitter_url', url()->current())">
    <meta name="twitter:title" content="@yield('twitter_title', config('app.name'))">
    <meta name="twitter:description" content="@yield('twitter_description', \App\Models\SiteSetting::get('site_description'))">
    <meta name="twitter:image" content="@yield('twitter_image', asset('images/og-default.jpg'))">
    
    <!-- Additional Meta Tags -->
    @stack('meta_tags')

    <!-- Search Engine Verification -->
    @if(\App\Models\SiteSetting::get('google_verification'))
    <meta name="google-site-verification" content="{{ \App\Models\SiteSetting::get('google_verification') }}">
    @endif
    @if(\App\Models\SiteSetting::get('bing_verification'))
    <meta name="msvalidate.01" content="{{ \App\Models\SiteSetting::get('bing_verification') }}">
    @endif
    @if(\App\Models\SiteSetting::get('yandex_verification'))
    <meta name="yandex-verification" content="{{ \App\Models\SiteSetting::get('yandex_verification') }}">
    @endif
    @if(\App\Models\SiteSetting::get('pinterest_verification'))
    <meta name="p:domain_verify" content="{{ \App\Models\SiteSetting::get('pinterest_verification') }}">
    @endif

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    
    @stack('styles')
</head>
<body class="antialiased bg-gray-50">
    <!-- Promotional Banner -->
    <x-frontend.promo-banner />
    
    <!-- Header -->
    <x-frontend.header />

    <!-- Main Content -->
    <main class="min-h-screen">
        @yield('content')
    </main>

    <!-- Footer -->
    <x-frontend.footer />

    <!-- Alert Components -->
    <x-confirm-modal />
    <x-alert-toast />

    <!-- Cart Sidebar -->
    @livewire('cart.cart-sidebar')

    @stack('scripts')
    @livewireScripts
    
    <script>
        // Function to add to cart and update button state without page reload
        function addToCartAndUpdate(button, productId, variantId, quantity, currentQuantity) {
            // Dispatch the Livewire event
            Livewire.dispatch('add-to-cart', { 
                productId: productId, 
                variantId: variantId, 
                quantity: quantity 
            });
            
            // Update button state immediately for better UX
            updateButtonState(button, currentQuantity + quantity);
            
            // Also update any other buttons for the same variant
            updateAllButtonsForVariant(variantId, currentQuantity + quantity);
        }
        
        // Function to update a single button's state
        function updateButtonState(button, newQuantity) {
            const isInCart = newQuantity > 0;
            
            // Update data attributes
            button.setAttribute('data-is-in-cart', isInCart ? 'true' : 'false');
            button.setAttribute('data-cart-quantity', newQuantity);
            
            // Update button classes
            if (isInCart) {
                button.classList.remove('bg-green-600', 'hover:bg-green-700');
                button.classList.add('bg-blue-600', 'hover:bg-blue-700');
            } else {
                button.classList.remove('bg-blue-600', 'hover:bg-blue-700');
                button.classList.add('bg-green-600', 'hover:bg-green-700');
            }
            
            // Update button content
            const svg = button.querySelector('svg');
            const textNode = button.childNodes[button.childNodes.length - 1];
            
            if (isInCart) {
                // Change to "Add More" state
                svg.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>';
                textNode.textContent = ` Add More (${newQuantity})`;
            } else {
                // Change to "Add to Cart" state
                svg.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>';
                textNode.textContent = ' Add to Cart';
            }
            
            // Update onclick function
            button.setAttribute('onclick', `addToCartAndUpdate(this, ${button.getAttribute('data-product-id')}, ${button.getAttribute('data-variant-id')}, 1, ${newQuantity})`);
        }
        
        // Function to update all buttons for the same variant across the page
        function updateAllButtonsForVariant(variantId, newQuantity) {
            const buttons = document.querySelectorAll(`button[data-variant-id="${variantId}"]`);
            buttons.forEach(button => {
                updateButtonState(button, newQuantity);
            });
        }

        // Global carousel scroll function for all homepage sliders
        function scrollCarousel(carouselId, direction) {
            const carousel = document.getElementById(carouselId);
            if (!carousel) return;
            
            const scrollAmount = 220; // Card width + gap
            
            if (direction === 'left') {
                carousel.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
            } else {
                carousel.scrollBy({ left: scrollAmount, behavior: 'smooth' });
            }
        }

        // Hide scrollbar for webkit browsers
        document.addEventListener('DOMContentLoaded', function() {
            const style = document.createElement('style');
            style.textContent = `
                .scrollbar-hide::-webkit-scrollbar {
                    display: none;
                }
            `;
            document.head.appendChild(style);
        });

        // Refresh CSRF token periodically to prevent Livewire session expiry warnings
        // Refresh every 2 hours (before the 12-hour session expires)
        setInterval(function() {
            fetch('/refresh-csrf', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.token) {
                    // Update CSRF token in meta tag
                    document.querySelector('meta[name="csrf-token"]').setAttribute('content', data.token);
                    // Update all CSRF inputs
                    document.querySelectorAll('input[name="_token"]').forEach(input => {
                        input.value = data.token;
                    });
                    // Update Livewire CSRF token if available
                    if (window.Livewire) {
                        window.Livewire.rescan();
                    }
                }
            })
            .catch(error => console.error('CSRF token refresh failed:', error));
        }, 7200000); // 2 hours in milliseconds
    </script>
</body>
</html>
