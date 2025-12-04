@extends('layouts.admin')

@section('title', 'Edit Coupon')

@section('content')
    @livewire('admin.coupon.coupon-edit', ['coupon' => $coupon])
@endsection
