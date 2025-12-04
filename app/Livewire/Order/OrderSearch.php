<?php

namespace App\Livewire\Order;

use App\Modules\Ecommerce\Order\Repositories\OrderRepository;
use Livewire\Component;
use Livewire\WithPagination;

class OrderSearch extends Component
{
    use WithPagination;

    public string $search = '';
    public string $status = '';
    public string $paymentStatus = '';
    public string $dateFrom = '';
    public string $dateTo = '';
    public int $perPage = 15;
    public bool $showFilters = false;
    public $gotoPage = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
        'paymentStatus' => ['except' => ''],
        'dateFrom' => ['except' => ''],
        'dateTo' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function updatingPaymentStatus()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function updatedGotoPage($value)
    {
        if ($value && is_numeric($value) && $value > 0) {
            $this->setPage((int) $value);
            $this->gotoPage = null;
        }
    }

    public function clearFilters()
    {
        $this->reset(['search', 'status', 'paymentStatus', 'dateFrom', 'dateTo']);
        $this->resetPage();
    }

    public function render()
    {
        $orderRepository = app(OrderRepository::class);

        $filters = [
            'search' => $this->search,
            'status' => $this->status,
            'payment_status' => $this->paymentStatus,
            'date_from' => $this->dateFrom,
            'date_to' => $this->dateTo,
        ];

        $orders = $orderRepository->paginate($this->perPage, $filters);

        return view('livewire.order.order-search', [
            'orders' => $orders,
        ]);
    }
}
