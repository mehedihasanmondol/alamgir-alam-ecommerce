@extends('layouts.admin')

@section('title', 'Feedback Management')

@section('content')
<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Customer Feedback</h1>
            <p class="mt-1 text-sm text-gray-600">Manage and review customer feedback submissions</p>
        </div>
    </div>
</div>

@livewire('admin.feedback-table')
@endsection
