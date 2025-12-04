@extends('layouts.admin')

@section('title', 'Product Questions Management')

@section('content')
    {{-- Livewire Component --}}
    @livewire('admin.product-question-table')
@endsection
