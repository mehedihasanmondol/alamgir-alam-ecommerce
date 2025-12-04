<?php

namespace App\Modules\Stock\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Ecommerce\Product\Models\Product;
use App\Modules\Ecommerce\Product\Models\ProductVariant;
use App\Modules\Ecommerce\Category\Models\Category;
use App\Modules\Ecommerce\Brand\Models\Brand;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

/**
 * ModuleName: Stock Report Controller
 * Purpose: Generate current stock reports with filters and export options
 * 
 * @category Controllers
 * @package  App\Modules\Stock\Controllers
 * @created  2025-11-24
 */
class StockReportController extends Controller
{
    /**
     * Display current stock report
     */
    public function index(Request $request)
    {
        $query = $this->buildStockQuery($request);
        
        // Get filter options
        $categories = Category::orderBy('name')->get();
        $brands = Brand::orderBy('name')->get();
        
        // Paginate results
        $stocks = $query->paginate(50)->withQueryString();
        
        // Calculate summary statistics
        $summary = $this->calculateSummary($request);
        
        return view('admin.stock.reports.index', compact('stocks', 'categories', 'brands', 'summary'));
    }

    /**
     * Export stock report to PDF
     */
    public function exportPdf(Request $request)
    {
        $query = $this->buildStockQuery($request);
        $stocks = $query->get();
        $summary = $this->calculateSummary($request);
        
        $filters = [
            'category' => $request->category_id ? Category::find($request->category_id)?->name : 'All',
            'brand' => $request->brand_id ? Brand::find($request->brand_id)?->name : 'All',
            'stock_status' => $this->getStockStatusLabel($request->stock_status),
            'search' => $request->search ?? '-',
        ];
        
        $pdf = Pdf::loadView('admin.stock.reports.pdf', compact('stocks', 'summary', 'filters'));
        $pdf->setPaper('a4', 'landscape');
        
        $filename = 'stock-report-' . now()->format('Y-m-d-His') . '.pdf';
        return $pdf->download($filename);
    }

    /**
     * Export stock report to Excel (CSV)
     */
    public function exportExcel(Request $request)
    {
        $query = $this->buildStockQuery($request);
        $stocks = $query->get();
        
        $filename = 'stock-report-' . now()->format('Y-m-d-His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];
        
        $callback = function() use ($stocks) {
            $file = fopen('php://output', 'w');
            
            // CSV Headers
            fputcsv($file, [
                'Product Name',
                'SKU',
                'Variation',
                'Category',
                'Brand',
                'Stock Quantity',
                'Low Stock Alert',
                'Stock Status',
                'Price',
                'Cost Price',
                'Stock Value',
            ]);
            
            // CSV Rows
            foreach ($stocks as $stock) {
                fputcsv($file, [
                    $stock->product_name,
                    $stock->sku,
                    $stock->variation_name ?? '-',
                    $stock->category_names,
                    $stock->brand_name ?? '-',
                    $stock->stock_quantity,
                    $stock->low_stock_alert,
                    $this->formatStockStatus($stock),
                    number_format($stock->price, 2),
                    number_format($stock->cost_price ?? 0, 2),
                    number_format($stock->stock_value, 2),
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Build stock query with filters
     */
    protected function buildStockQuery(Request $request)
    {
        $query = ProductVariant::query()
            ->select([
                'product_variants.id as variant_id',
                'product_variants.sku',
                'product_variants.name as variation_name',
                'product_variants.stock_quantity',
                'product_variants.low_stock_alert',
                'product_variants.stock_status',
                'product_variants.price',
                'product_variants.cost_price',
                'product_variants.manage_stock',
                'products.id as product_id',
                'products.name as product_name',
                'products.is_active as product_active',
                'brands.name as brand_name',
                DB::raw('(product_variants.stock_quantity * COALESCE(product_variants.cost_price, product_variants.price)) as stock_value')
            ])
            ->join('products', 'product_variants.product_id', '=', 'products.id')
            ->leftJoin('brands', 'products.brand_id', '=', 'brands.id')
            ->where('product_variants.is_active', true)
            ->where('product_variants.manage_stock', true);
        
        // Filter by category
        if ($request->filled('category_id')) {
            $query->whereHas('product.categories', function($q) use ($request) {
                $q->where('categories.id', $request->category_id);
            });
        }
        
        // Filter by brand
        if ($request->filled('brand_id')) {
            $query->where('products.brand_id', $request->brand_id);
        }
        
        // Filter by stock status
        if ($request->filled('stock_status')) {
            switch ($request->stock_status) {
                case 'in_stock':
                    $query->where('product_variants.stock_quantity', '>', 0);
                    break;
                case 'low_stock':
                    $query->whereColumn('product_variants.stock_quantity', '<=', 'product_variants.low_stock_alert')
                          ->where('product_variants.stock_quantity', '>', 0);
                    break;
                case 'out_of_stock':
                    $query->where('product_variants.stock_quantity', '<=', 0);
                    break;
            }
        }
        
        // Search by product name or SKU
        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where(function($q) use ($search) {
                $q->where('products.name', 'like', $search)
                  ->orWhere('product_variants.sku', 'like', $search)
                  ->orWhere('product_variants.name', 'like', $search);
            });
        }
        
        // Add category names (aggregate)
        $query->addSelect([
            'category_names' => Category::select(DB::raw('GROUP_CONCAT(categories.name SEPARATOR ", ")'))
                ->join('category_product', 'categories.id', '=', 'category_product.category_id')
                ->whereColumn('category_product.product_id', 'products.id')
                ->limit(1)
        ]);
        
        // Sort
        $sortBy = $request->get('sort_by', 'product_name');
        $sortOrder = $request->get('sort_order', 'asc');
        
        switch ($sortBy) {
            case 'stock_quantity':
                $query->orderBy('product_variants.stock_quantity', $sortOrder);
                break;
            case 'stock_value':
                $query->orderBy('stock_value', $sortOrder);
                break;
            case 'sku':
                $query->orderBy('product_variants.sku', $sortOrder);
                break;
            default:
                $query->orderBy('products.name', $sortOrder);
        }
        
        return $query;
    }

    /**
     * Calculate summary statistics
     */
    protected function calculateSummary(Request $request)
    {
        // Build a fresh query for aggregation only
        $query = ProductVariant::query()
            ->join('products', 'product_variants.product_id', '=', 'products.id')
            ->leftJoin('brands', 'products.brand_id', '=', 'brands.id')
            ->where('product_variants.is_active', true)
            ->where('product_variants.manage_stock', true);
        
        // Apply same filters as main query
        if ($request->filled('category_id')) {
            $query->whereHas('product.categories', function($q) use ($request) {
                $q->where('categories.id', $request->category_id);
            });
        }
        
        if ($request->filled('brand_id')) {
            $query->where('products.brand_id', $request->brand_id);
        }
        
        if ($request->filled('stock_status')) {
            switch ($request->stock_status) {
                case 'in_stock':
                    $query->where('product_variants.stock_quantity', '>', 0);
                    break;
                case 'low_stock':
                    $query->whereColumn('product_variants.stock_quantity', '<=', 'product_variants.low_stock_alert')
                          ->where('product_variants.stock_quantity', '>', 0);
                    break;
                case 'out_of_stock':
                    $query->where('product_variants.stock_quantity', '<=', 0);
                    break;
            }
        }
        
        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where(function($q) use ($search) {
                $q->where('products.name', 'like', $search)
                  ->orWhere('product_variants.sku', 'like', $search)
                  ->orWhere('product_variants.name', 'like', $search);
            });
        }
        
        // Get aggregate statistics
        $stats = $query->selectRaw('
            COUNT(*) as total_products,
            SUM(product_variants.stock_quantity) as total_quantity,
            SUM(CASE WHEN product_variants.stock_quantity > 0 THEN 1 ELSE 0 END) as in_stock_count,
            SUM(CASE WHEN product_variants.stock_quantity <= 0 THEN 1 ELSE 0 END) as out_of_stock_count,
            SUM(CASE WHEN product_variants.stock_quantity <= product_variants.low_stock_alert AND product_variants.stock_quantity > 0 THEN 1 ELSE 0 END) as low_stock_count,
            SUM(product_variants.stock_quantity * COALESCE(product_variants.cost_price, product_variants.price)) as total_stock_value
        ')->first();
        
        return [
            'total_products' => $stats->total_products ?? 0,
            'total_quantity' => $stats->total_quantity ?? 0,
            'in_stock_count' => $stats->in_stock_count ?? 0,
            'out_of_stock_count' => $stats->out_of_stock_count ?? 0,
            'low_stock_count' => $stats->low_stock_count ?? 0,
            'total_stock_value' => $stats->total_stock_value ?? 0,
        ];
    }

    /**
     * Format stock status for display
     */
    protected function formatStockStatus($stock)
    {
        if ($stock->stock_quantity <= 0) {
            return 'Out of Stock';
        }
        
        if ($stock->stock_quantity <= $stock->low_stock_alert) {
            return 'Low Stock';
        }
        
        return 'In Stock';
    }

    /**
     * Get stock status label
     */
    protected function getStockStatusLabel($status)
    {
        return match($status) {
            'in_stock' => 'In Stock',
            'low_stock' => 'Low Stock',
            'out_of_stock' => 'Out of Stock',
            default => 'All'
        };
    }
}
