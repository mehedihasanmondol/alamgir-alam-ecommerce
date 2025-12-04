@extends('layouts.admin')

@section('title', 'Manage Product Images')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <a href="{{ route('admin.products.edit', $product) }}" class="text-blue-600 hover:text-blue-800 text-sm">
            <i class="fas fa-arrow-left mr-1"></i> Back to Product
        </a>
        <h1 class="text-2xl font-bold text-gray-900 mt-2">Manage Images: {{ $product->name }}</h1>
        <p class="text-sm text-gray-600 mt-1">Upload and manage product images</p>
    </div>

    @livewire('admin.product.image-uploader', ['productId' => $product->id])
</div>
@endsection
