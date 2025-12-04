@extends('layouts.admin')

@section('title', 'Coupon Statistics')

@section('content')
    @livewire('admin.coupon.coupon-statistics', ['coupon' => $coupon])
@endsection
