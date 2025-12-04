<?php

namespace App\Livewire\Order;

use App\Modules\Ecommerce\Order\Repositories\OrderRepository;
use Livewire\Component;

class OrderTracker extends Component
{
    public string $orderNumber = '';
    public string $email = '';
    public $order = null;
    public bool $searched = false;

    protected $rules = [
        'orderNumber' => 'required|string',
        'email' => 'required|email',
    ];

    protected $messages = [
        'orderNumber.required' => 'Please enter your order number.',
        'email.required' => 'Please enter your email address.',
        'email.email' => 'Please enter a valid email address.',
    ];

    public function trackOrder()
    {
        $this->validate();

        $orderRepository = app(OrderRepository::class);
        $order = $orderRepository->findByOrderNumber($this->orderNumber);

        $this->searched = true;

        if (!$order || $order->customer_email !== $this->email) {
            $this->order = null;
            session()->flash('error', 'Order not found or email does not match.');
            return;
        }

        $this->order = $order;
    }

    public function resetSearch()
    {
        $this->reset(['orderNumber', 'email', 'order', 'searched']);
    }

    public function render()
    {
        return view('livewire.order.order-tracker');
    }
}
