@extends('layouts.admin')

@section('title', 'Product Reviews Management')

@section('content')
    {{-- Livewire Component --}}
    @livewire('admin.product-review-table')
@endsection
