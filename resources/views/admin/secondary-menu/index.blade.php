@extends('layouts.admin')

@section('title', 'Secondary Menu Settings')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Livewire Component -->
    @livewire('admin.secondary-menu.secondary-menu-list')
</div>
@endsection
