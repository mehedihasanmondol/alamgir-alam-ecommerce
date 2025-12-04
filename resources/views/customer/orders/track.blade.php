@extends('layouts.app')

@section('title', 'Track Order')

@section('content')
<div class="container mx-auto px-4 py-12">
    <div class="max-w-4xl mx-auto">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Track Your Order</h1>
            <p class="text-gray-600">Enter your order number and email to track your order status</p>
        </div>

        @livewire('order.order-tracker')
    </div>
</div>
@endsection
