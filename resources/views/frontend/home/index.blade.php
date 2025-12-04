@extends('layouts.app')

@section('title', $seoData['title'] ?? \App\Models\SiteSetting::get('site_name', config('app.name')))

@section('description', $seoData['description'] ?? \App\Models\SiteSetting::get('meta_description', 'Shop health, wellness and beauty products'))

@section('keywords', $seoData['keywords'] ?? \App\Models\SiteSetting::get('meta_keywords', 'health, wellness, beauty, supplements'))

@section('og_type', $seoData['og_type'] ?? 'website')
@section('og_title', $seoData['title'] ?? \App\Models\SiteSetting::get('site_name', config('app.name')))
@section('og_description', $seoData['description'] ?? \App\Models\SiteSetting::get('meta_description', 'Shop health, wellness and beauty products'))
@section('og_image', $seoData['og_image'] ?? asset('images/og-default.jpg'))
@section('canonical', $seoData['canonical'] ?? url('/'))

@section('twitter_card', 'summary_large_image')
@section('twitter_title', $seoData['title'] ?? \App\Models\SiteSetting::get('site_name', config('app.name')))
@section('twitter_description', $seoData['description'] ?? \App\Models\SiteSetting::get('meta_description', 'Shop health, wellness and beauty products'))
@section('twitter_image', $seoData['og_image'] ?? asset('images/og-default.jpg'))

@if(isset($seoData['author_name']))
@section('author', $seoData['author_name'])
@endif

@section('content')
<!-- Hero Slider -->
<x-frontend.hero-slider />

<!-- Recommended Products Slider -->
@php
    // Use featured products, or fallback to new arrivals if no featured products
    $recommendedProducts = $featuredProducts->count() > 0 ? $featuredProducts : $newArrivals;
@endphp

{{-- Debug: Show product count --}}
<!-- DEBUG: Featured Products: {{ $featuredProducts->count() }}, New Arrivals: {{ $newArrivals->count() }}, Recommended: {{ $recommendedProducts->count() }} -->

<x-frontend.recommended-slider :products="$recommendedProducts" />

<!-- Sale Offers Slider -->
@if(\App\Models\SiteSetting::get('sale_offers_section_enabled', '1') === '1')
    <x-frontend.sale-offers-slider 
        :products="$saleOffers" 
        :title="\App\Models\SiteSetting::get('sale_offers_section_title', 'Sale Offers')" />
@endif

<!-- Shop by Category Section -->
<x-frontend.shop-by-category :categories="$featuredCategories" />

<!-- Trending Products Section -->
@if(\App\Models\SiteSetting::get('trending_section_enabled', '1') === '1')
    <x-frontend.trending-products 
        :products="$trendingProducts" 
        :title="\App\Models\SiteSetting::get('trending_section_title', 'Trending Now')" />
@endif

<!-- Best Sellers Section -->
@if(\App\Models\SiteSetting::get('best_sellers_section_enabled', '1') === '1')
    <x-frontend.best-sellers 
        :products="$bestSellerProducts" 
        :title="\App\Models\SiteSetting::get('best_sellers_section_title', 'Best Sellers')" />
@endif

<!-- New Arrivals Section -->
@if(\App\Models\SiteSetting::get('new_arrivals_section_enabled', '1') === '1')
    <x-frontend.new-arrivals 
        :products="$newArrivalProducts" 
        :title="\App\Models\SiteSetting::get('new_arrivals_section_title', 'New Arrivals')" />
@endif
@endsection
