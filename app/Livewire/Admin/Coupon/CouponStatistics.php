<?php

namespace App\Livewire\Admin\Coupon;

use App\Models\Coupon;
use App\Services\CouponService;
use Livewire\Component;

class CouponStatistics extends Component
{
    public Coupon $coupon;
    public $statistics = [];

    public function mount(Coupon $coupon, CouponService $couponService)
    {
        $this->coupon = $coupon;
        $this->statistics = $couponService->getStatistics($coupon);
    }

    public function render()
    {
        // Get recent usage
        $recentUsage = $this->coupon->users()
            ->with('user')
            ->orderBy('coupon_user.used_at', 'desc')
            ->limit(10)
            ->get();

        return view('livewire.admin.coupon.coupon-statistics', [
            'recentUsage' => $recentUsage,
        ]);
    }
}
