@extends('layouts.app')

@section('title', 'Customer Feedback')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-7xl">
    <div class="mb-12">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Customer Feedback</h1>
        <p class="text-gray-600">Share your experience and read what others have to say</p>
    </div>
    
    {{-- Feedback List --}}
    <div class="mt-12 mb-6">
        @livewire('feedback.feedback-list')
    </div>
    {{-- Feedback Form --}}
    <div class="mb-12">
        @livewire('feedback.feedback-form')
    </div>
</div>
@endsection
