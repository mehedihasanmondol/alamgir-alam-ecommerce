<?php

namespace App\Services;

use App\Modules\Ecommerce\Order\Models\Order;
use App\Modules\Ecommerce\Order\Models\OrderItem;
use App\Modules\Ecommerce\Product\Models\Product;
use App\Modules\Ecommerce\Product\Models\ProductVariant;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Report Service
 * 
 * Handles all business reporting logic
 * - Sales reports
 * - Product performance
 * - Inventory reports
 * - Customer reports
 * - Revenue analysis
 */
class ReportService
{
    /**
     * Get dashboard overview statistics
     */
    public function getDashboardStats($startDate = null, $endDate = null)
    {
        $startDate = $startDate ? Carbon::parse($startDate) : Carbon::now()->startOfMonth();
        $endDate = $endDate ? Carbon::parse($endDate) : Carbon::now()->endOfDay();

        // Total revenue
        $totalRevenue = Order::whereBetween('created_at', [$startDate, $endDate])
            ->whereIn('status', ['completed', 'processing', 'shipped', 'delivered'])
            ->sum('total_amount');

        // Total orders
        $totalOrders = Order::whereBetween('created_at', [$startDate, $endDate])->count();

        // Pending orders
        $pendingOrders = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'pending')
            ->count();

        // Total customers (with orders)
        $totalCustomers = Order::whereBetween('created_at', [$startDate, $endDate])
            ->distinct('user_id')
            ->whereNotNull('user_id')
            ->count('user_id');

        // Average order value
        $avgOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        // Products sold
        $productsSold = OrderItem::whereHas('order', function($q) use ($startDate, $endDate) {
            $q->whereBetween('created_at', [$startDate, $endDate]);
        })->sum('quantity');

        // Low stock products
        $lowStockCount = ProductVariant::where('stock_quantity', '<=', 10)
            ->where('stock_quantity', '>', 0)
            ->count();

        // Out of stock products
        $outOfStockCount = ProductVariant::where('stock_quantity', 0)->count();

        return [
            'total_revenue' => round($totalRevenue, 2),
            'total_orders' => $totalOrders,
            'pending_orders' => $pendingOrders,
            'total_customers' => $totalCustomers,
            'avg_order_value' => round($avgOrderValue, 2),
            'products_sold' => $productsSold,
            'low_stock_count' => $lowStockCount,
            'out_of_stock_count' => $outOfStockCount,
        ];
    }

    /**
     * Get sales report by date range
     */
    public function getSalesReport($startDate, $endDate, $groupBy = 'day')
    {
        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate)->endOfDay();

        $dateFormat = match($groupBy) {
            'day' => '%Y-%m-%d',
            'week' => '%Y-%u',
            'month' => '%Y-%m',
            'year' => '%Y',
            default => '%Y-%m-%d',
        };

        $sales = Order::selectRaw("
                DATE_FORMAT(created_at, '{$dateFormat}') as period,
                COUNT(*) as total_orders,
                SUM(total_amount) as total_revenue,
                SUM(subtotal) as subtotal,
                SUM(shipping_cost) as shipping_revenue,
                SUM(discount_amount) as total_discounts,
                AVG(total_amount) as avg_order_value
            ")
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereIn('status', ['completed', 'processing', 'shipped', 'delivered'])
            ->groupBy('period')
            ->orderBy('period')
            ->get();

        return [
            'period_data' => $sales,
            'summary' => [
                'total_orders' => $sales->sum('total_orders'),
                'total_revenue' => round($sales->sum('total_revenue'), 2),
                'total_discounts' => round($sales->sum('total_discounts'), 2),
                'avg_order_value' => round($sales->avg('avg_order_value'), 2),
            ]
        ];
    }

    /**
     * Get top selling products
     */
    public function getTopSellingProducts($startDate, $endDate, $limit = 10)
    {
        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate)->endOfDay();

        $topProducts = OrderItem::select(
                'products.id',
                'products.name',
                'products.slug',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.price * order_items.quantity) as total_revenue'),
                DB::raw('COUNT(DISTINCT order_items.order_id) as order_count')
            )
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->whereIn('orders.status', ['completed', 'processing', 'shipped', 'delivered'])
            ->groupBy('products.id', 'products.name', 'products.slug')
            ->orderByDesc('total_revenue')
            ->limit($limit)
            ->get();

        return $topProducts;
    }

    /**
     * Get product performance report
     */
    public function getProductPerformanceReport($startDate, $endDate)
    {
        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate)->endOfDay();

        $performance = OrderItem::select(
                'products.id',
                'products.name',
                'categories.name as category_name',
                'brands.name as brand_name',
                DB::raw('SUM(order_items.quantity) as units_sold'),
                DB::raw('SUM(order_items.price * order_items.quantity) as revenue'),
                DB::raw('COUNT(DISTINCT order_items.order_id) as order_count'),
                DB::raw('AVG(order_items.price) as avg_price')
            )
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->leftJoin('category_product', function($join) {
                $join->on('category_product.product_id', '=', 'products.id')
                     ->whereRaw('category_product.id = (SELECT MIN(id) FROM category_product WHERE product_id = products.id)');
            })
            ->leftJoin('categories', 'category_product.category_id', '=', 'categories.id')
            ->leftJoin('brands', 'products.brand_id', '=', 'brands.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->whereIn('orders.status', ['completed', 'processing', 'shipped', 'delivered'])
            ->groupBy('products.id', 'products.name', 'categories.name', 'brands.name')
            ->orderByDesc('revenue')
            ->get();

        return $performance;
    }

    /**
     * Get category performance report
     */
    public function getCategoryPerformance($startDate, $endDate)
    {
        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate)->endOfDay();

        $categoryPerformance = OrderItem::select(
                'categories.id as category_id',
                'categories.name as category_name',
                DB::raw('COUNT(DISTINCT products.id) as product_count'),
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.quantity * order_items.price) as total_revenue'),
                DB::raw('COUNT(DISTINCT order_items.order_id) as order_count')
            )
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('category_product', 'category_product.product_id', '=', 'products.id')
            ->join('categories', 'category_product.category_id', '=', 'categories.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->whereIn('orders.status', ['completed', 'processing', 'shipped', 'delivered'])
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total_revenue')
            ->get();

        return $categoryPerformance;
    }

    /**
     * Get inventory report
     */
    public function getInventoryReport()
    {
        $inventory = Product::select(
                'products.id',
                'products.name',
                'categories.name as category_name',
                'brands.name as brand_name',
                DB::raw('SUM(product_variants.stock_quantity) as total_stock'),
                DB::raw('COUNT(product_variants.id) as variant_count'),
                DB::raw('MIN(product_variants.stock_quantity) as min_stock'),
                DB::raw('MAX(product_variants.stock_quantity) as max_stock'),
                DB::raw('AVG(product_variants.price) as avg_price')
            )
            ->leftJoin('category_product', function($join) {
                $join->on('category_product.product_id', '=', 'products.id')
                     ->whereRaw('category_product.id = (SELECT MIN(id) FROM category_product WHERE product_id = products.id)');
            })
            ->leftJoin('categories', 'category_product.category_id', '=', 'categories.id')
            ->leftJoin('brands', 'products.brand_id', '=', 'brands.id')
            ->leftJoin('product_variants', 'products.id', '=', 'product_variants.product_id')
            ->where('products.status', 'published')
            ->groupBy('products.id', 'products.name', 'categories.name', 'brands.name')
            ->orderBy('total_stock', 'asc')
            ->get();

        return $inventory;
    }

    /**
     * Get low stock products
     */
    public function getLowStockProducts($threshold = 10)
    {
        $lowStock = ProductVariant::select(
                'products.id',
                'products.name',
                'product_variants.id as variant_id',
                'product_variants.sku as variant_sku',
                'product_variants.stock_quantity',
                'product_variants.price',
                'categories.name as category_name'
            )
            ->join('products', 'product_variants.product_id', '=', 'products.id')
            ->leftJoin('category_product', function($join) {
                $join->on('category_product.product_id', '=', 'products.id')
                     ->whereRaw('category_product.id = (SELECT MIN(id) FROM category_product WHERE product_id = products.id)');
            })
            ->leftJoin('categories', 'category_product.category_id', '=', 'categories.id')
            ->where('product_variants.stock_quantity', '<=', $threshold)
            ->where('product_variants.stock_quantity', '>', 0)
            ->where('products.status', 'published')
            ->orderBy('product_variants.stock_quantity', 'asc')
            ->get();

        return $lowStock;
    }

    /**
     * Get out of stock products
     */
    public function getOutOfStockProducts()
    {
        $outOfStock = ProductVariant::select(
                'products.id',
                'products.name',
                'product_variants.id as variant_id',
                'product_variants.sku as variant_sku',
                'product_variants.price',
                'categories.name as category_name'
            )
            ->join('products', 'product_variants.product_id', '=', 'products.id')
            ->leftJoin('category_product', function($join) {
                $join->on('category_product.product_id', '=', 'products.id')
                     ->whereRaw('category_product.id = (SELECT MIN(id) FROM category_product WHERE product_id = products.id)');
            })
            ->leftJoin('categories', 'category_product.category_id', '=', 'categories.id')
            ->where('product_variants.stock_quantity', 0)
            ->where('products.status', 'published')
            ->get();

        return $outOfStock;
    }

    /**
     * Get customer report
     */
    public function getCustomerReport($startDate, $endDate)
    {
        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate)->endOfDay();

        $customers = Order::select(
                'users.id',
                'users.name',
                'users.email',
                DB::raw('COUNT(orders.id) as total_orders'),
                DB::raw('SUM(orders.total_amount) as total_spent'),
                DB::raw('AVG(orders.total_amount) as avg_order_value'),
                DB::raw('MAX(orders.created_at) as last_order_date')
            )
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->whereIn('orders.status', ['completed', 'processing', 'shipped', 'delivered'])
            ->groupBy('users.id', 'users.name', 'users.email')
            ->orderByDesc('total_spent')
            ->get();

        return $customers;
    }

    /**
     * Get payment method report
     */
    public function getPaymentMethodReport($startDate, $endDate)
    {
        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate)->endOfDay();

        $paymentMethods = Order::select(
                'payment_method',
                DB::raw('COUNT(*) as order_count'),
                DB::raw('SUM(total_amount) as total_revenue'),
                DB::raw('AVG(total_amount) as avg_order_value')
            )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereIn('status', ['completed', 'processing', 'shipped', 'delivered'])
            ->groupBy('payment_method')
            ->orderByDesc('total_revenue')
            ->get();

        return $paymentMethods;
    }

    /**
     * Get order status report
     */
    public function getOrderStatusReport($startDate, $endDate)
    {
        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate)->endOfDay();

        $statuses = Order::select(
                'status',
                DB::raw('COUNT(*) as order_count'),
                DB::raw('SUM(total_amount) as total_amount')
            )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('status')
            ->get();

        return $statuses;
    }

    /**
     * Get category performance report
     */
    public function getCategoryPerformanceReport($startDate, $endDate)
    {
        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate)->endOfDay();

        $categories = OrderItem::select(
                'categories.id',
                'categories.name',
                'categories.slug',
                DB::raw('COUNT(DISTINCT order_items.product_id) as product_count'),
                DB::raw('SUM(order_items.quantity) as units_sold'),
                DB::raw('SUM(order_items.price * order_items.quantity) as revenue'),
                DB::raw('COUNT(DISTINCT order_items.order_id) as order_count')
            )
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('category_product', 'category_product.product_id', '=', 'products.id')
            ->join('categories', 'category_product.category_id', '=', 'categories.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->whereIn('orders.status', ['completed', 'processing', 'shipped', 'delivered'])
            ->groupBy('categories.id', 'categories.name', 'categories.slug')
            ->orderByDesc('revenue')
            ->get();

        return $categories;
    }

    /**
     * Get delivery zone performance
     */
    public function getDeliveryZoneReport($startDate, $endDate)
    {
        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate)->endOfDay();

        $zones = Order::select(
                'delivery_zone_name',
                'delivery_method_name',
                DB::raw('COUNT(*) as order_count'),
                DB::raw('SUM(total_amount) as total_revenue'),
                DB::raw('SUM(shipping_cost) as shipping_revenue'),
                DB::raw('AVG(shipping_cost) as avg_shipping_cost')
            )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereIn('status', ['confirmed', 'processing', 'shipped', 'delivered'])
            ->whereNotNull('delivery_zone_name') // Filter out NULL zones
            ->where('delivery_zone_name', '!=', '') // Filter out empty zones
            ->groupBy('delivery_zone_name', 'delivery_method_name')
            ->orderByDesc('order_count')
            ->get();

        return $zones;
    }
}
