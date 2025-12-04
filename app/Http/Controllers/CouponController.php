<?php

namespace App\Http\Controllers;

use App\Services\CouponService;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    protected $couponService;

    public function __construct(CouponService $couponService)
    {
        $this->couponService = $couponService;
    }

    /**
     * Display available coupons to customers
     */
    public function index()
    {
        $coupons = $this->couponService->getActiveCoupons();

        return view('frontend.coupons.index', compact('coupons'));
    }
}
