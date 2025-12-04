<?php

namespace App\Livewire\Stock;

use Livewire\Component;
use App\Modules\Ecommerce\Category\Models\Category;
use App\Modules\Ecommerce\Brand\Models\Brand;

/**
 * Stock Report Filter Component
 * Real-time filtering for stock reports
 */
class StockReportFilter extends Component
{
    public $category_id = '';
    public $brand_id = '';
    public $stock_status = '';
    public $search = '';
    public $sort_by = 'product_name';
    public $sort_order = 'asc';

    protected $queryString = [
        'category_id' => ['except' => ''],
        'brand_id' => ['except' => ''],
        'stock_status' => ['except' => ''],
        'search' => ['except' => ''],
        'sort_by' => ['except' => 'product_name'],
        'sort_order' => ['except' => 'asc'],
    ];

    public function applyFilters()
    {
        $params = array_filter([
            'category_id' => $this->category_id,
            'brand_id' => $this->brand_id,
            'stock_status' => $this->stock_status,
            'search' => $this->search,
            'sort_by' => $this->sort_by,
            'sort_order' => $this->sort_order,
        ]);

        return redirect()->route('admin.stock.reports.index', $params);
    }

    public function clearFilters()
    {
        $this->reset([
            'category_id',
            'brand_id',
            'stock_status',
            'search',
            'sort_by',
            'sort_order'
        ]);

        return redirect()->route('admin.stock.reports.index');
    }

    public function render()
    {
        $categories = Category::orderBy('name')->get();
        $brands = Brand::orderBy('name')->get();

        return view('livewire.stock.stock-report-filter', compact('categories', 'brands'));
    }
}
