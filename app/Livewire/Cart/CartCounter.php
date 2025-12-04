<?php

namespace App\Livewire\Cart;

use Livewire\Component;
use Livewire\Attributes\On;

/**
 * ModuleName: Cart Counter Component
 * Purpose: Display and update cart item count in header
 * 
 * Key Methods:
 * - updateCartCount(): Update cart count when items are added/removed
 * - getCartCount(): Calculate total items in cart
 * 
 * Dependencies:
 * - Session (for cart storage)
 * 
 * @category Livewire
 * @package  Cart
 * @author   Windsurf AI
 * @created  2025-11-09
 */
class CartCounter extends Component
{
    public $cartCount = 0;

    /**
     * Mount component and initialize cart count
     */
    public function mount()
    {
        $this->cartCount = $this->getCartCount();
    }

    /**
     * Listen for cart-updated event
     */
    #[On('cart-updated')]
    public function updateCartCount()
    {
        $this->cartCount = $this->getCartCount();
    }

    /**
     * Get total cart count from session
     */
    protected function getCartCount(): int
    {
        $cart = session()->get('cart', []);
        return count($cart);
    }

    /**
     * Render component
     */
    public function render()
    {
        return view('livewire.cart.cart-counter');
    }
}
