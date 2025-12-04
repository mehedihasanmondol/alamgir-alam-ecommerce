<?php

namespace App\Livewire\Wishlist;

use Livewire\Component;
use Livewire\Attributes\On;

/**
 * ModuleName: Wishlist Counter Component
 * Purpose: Display wishlist item count in header
 * 
 * @category Livewire
 * @package  Wishlist
 * @created  2025-11-09
 */
class WishlistCounter extends Component
{
    public $wishlistCount = 0;

    /**
     * Mount component
     */
    public function mount()
    {
        $this->updateCount();
    }

    /**
     * Listen for wishlist-updated event
     */
    #[On('wishlist-updated')]
    public function updateCount()
    {
        $wishlist = session()->get('wishlist', []);
        $this->wishlistCount = count($wishlist);
    }

    /**
     * Render component
     */
    public function render()
    {
        return view('livewire.wishlist.wishlist-counter');
    }
}
