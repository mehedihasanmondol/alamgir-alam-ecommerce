<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    /**
     * Display coupon index page with Livewire component
     */
    public function index()
    {
        return view('admin.coupons.index-livewire');
    }

    /**
     * Display coupon create page with Livewire component
     */
    public function create()
    {
        return view('admin.coupons.create-livewire');
    }

    /**
     * Display coupon edit page with Livewire component
     */
    public function edit($couponId)
    {
        $coupon = \App\Models\Coupon::findOrFail($couponId);
        return view('admin.coupons.edit-livewire', compact('coupon'));
    }

    /**
     * Display coupon statistics page with Livewire component
     */
    public function statistics($couponId)
    {
        $coupon = \App\Models\Coupon::findOrFail($couponId);
        return view('admin.coupons.statistics-livewire', compact('coupon'));
    }
}
