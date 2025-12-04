@extends('layouts.admin')

@section('title', 'Contact Messages')

@section('content')
<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Contact Messages</h1>
            <p class="mt-1 text-sm text-gray-600">Manage and respond to customer inquiries</p>
        </div>
        <a href="{{ route('admin.contact.settings.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
            <i class="fas fa-cog mr-2"></i>Contact Settings
        </a>
    </div>
</div>

@livewire('admin.contact-message-table')
@endsection
