@extends('layouts.admin')

@section('title', 'Appointment Management')

@section('content')
<div class="container-fluid px-4 py-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Appointment Management</h1>
        <p class="text-gray-600 mt-1">Manage customer appointments and bookings</p>
    </div>

    <!-- Livewire Appointment Table Component -->
    @livewire('admin.appointment-table')
</div>
@endsection
