@extends('layouts.app')

@section('title', 'Shop All Products - ' . \App\Models\SiteSetting::get('site_name', config('app.name')))

@section('description', 'Browse our complete collection of health, wellness, and beauty products. Find the best supplements, vitamins, and natural health solutions.')

@section('keywords', 'shop, all products, health products, supplements, vitamins, wellness, beauty products, buy online')

@section('og_type', 'website')
@section('og_image', asset('images/shop-banner.jpg'))

@section('content')
<div class="bg-gray-50 min-h-screen" x-data="shopPage()">
    <div class="container mx-auto px-4 py-8">
        <div class="flex flex-col lg:flex-row gap-6">
            <!-- Sidebar Filters -->
            <aside class="lg:w-64 flex-shrink-0">
                @include('frontend.shop.partials.filters')
            </aside>

            <!-- Main Content -->
            <main class="flex-1">
                @include('frontend.shop.partials.header')
                @include('frontend.shop.partials.products')
            </main>
        </div>
    </div>
</div>

@push('scripts')
<script>
function shopPage() {
    const urlParams = new URLSearchParams(window.location.search);
    
    return {
        showFilters: false,
        viewMode: 'grid',
        filters: {
            search: urlParams.get('search') || '',
            category: urlParams.getAll('category[]').map(Number).filter(Boolean),
            brand: urlParams.getAll('brand[]').map(Number).filter(Boolean),
            min_price: urlParams.get('min_price') || '',
            max_price: urlParams.get('max_price') || '',
            rating: urlParams.get('rating') || '',
            in_stock: urlParams.get('in_stock') || '',
            on_sale: urlParams.get('on_sale') || '',
            sort: urlParams.get('sort') || 'latest',
            per_page: urlParams.get('per_page') || '24'
        },
        
        applyFilters() {
            const params = new URLSearchParams();
            
            if (this.filters.search) params.set('search', this.filters.search);
            this.filters.category.forEach(id => params.append('category[]', id));
            this.filters.brand.forEach(id => params.append('brand[]', id));
            if (this.filters.min_price) params.set('min_price', this.filters.min_price);
            if (this.filters.max_price) params.set('max_price', this.filters.max_price);
            if (this.filters.rating) params.set('rating', this.filters.rating);
            if (this.filters.in_stock) params.set('in_stock', this.filters.in_stock);
            if (this.filters.on_sale) params.set('on_sale', this.filters.on_sale);
            if (this.filters.sort) params.set('sort', this.filters.sort);
            if (this.filters.per_page) params.set('per_page', this.filters.per_page);
            
            window.location.href = '{{ route("shop") }}?' + params.toString();
        },
        
        clearFilters() {
            this.filters = {
                search: '',
                category: [],
                brand: [],
                min_price: '',
                max_price: '',
                rating: '',
                in_stock: '',
                on_sale: '',
                sort: 'latest',
                per_page: '24'
            };
            window.location.href = '{{ route("shop") }}';
        },
        
        hasActiveFilters() {
            return this.filters.search || 
                   this.filters.category.length > 0 || 
                   this.filters.brand.length > 0 ||
                   this.filters.min_price ||
                   this.filters.max_price ||
                   this.filters.rating ||
                   this.filters.in_stock ||
                   this.filters.on_sale;
        }
    }
}
</script>
@endpush
@endsection
