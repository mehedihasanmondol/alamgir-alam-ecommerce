<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\User\Models\UserActivity;
use App\Modules\Ecommerce\Product\Models\Product;
use App\Modules\Ecommerce\Product\Models\ProductReview;
use App\Modules\Ecommerce\Product\Models\ProductQuestion;
use App\Modules\Ecommerce\Order\Models\Order;
use App\Modules\Ecommerce\Category\Models\Category;
use App\Modules\Ecommerce\Brand\Models\Brand;
use App\Modules\Blog\Models\Post;
use App\Modules\Blog\Models\Comment;
use App\Modules\Stock\Models\StockAlert;
use App\Modules\Stock\Models\StockMovement;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * ModuleName: Admin Dashboard
 * Purpose: Comprehensive dashboard with role-based business metrics
 * Features: Real-time statistics, charts, alerts, recent activities
 * 
 * @category Admin Controllers
 * @package  App\Http\Controllers\Admin
 * @updated  2025-11-24
 */
class DashboardController extends Controller
{
    /**
     * Display the comprehensive admin dashboard
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $data = [];
        
        // Date Range Filter
        $startDate = $request->input('start_date', now()->subDays(30)->startOfDay());
        $endDate = $request->input('end_date', now()->endOfDay());
        
        if (is_string($startDate)) {
            $startDate = Carbon::parse($startDate)->startOfDay();
        }
        if (is_string($endDate)) {
            $endDate = Carbon::parse($endDate)->endOfDay();
        }
        
        $data['startDate'] = $startDate->format('Y-m-d');
        $data['endDate'] = $endDate->format('Y-m-d');

        // ===================================
        // USER MANAGEMENT STATISTICS
        // ===================================
        if ($user->hasPermission('users.view')) {
            $data['totalUsers'] = User::count();
            $data['totalCustomers'] = User::where('role', 'customer')->count();
            $data['totalAdmins'] = User::whereIn('role', ['admin', 'author'])->count();
            $data['activeUsers'] = User::where('is_active', true)->count();
            $data['newUsersThisMonth'] = User::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count();
            $data['newUsersToday'] = User::whereDate('created_at', today())->count();
            
            // Customer-specific statistics
            $data['totalCustomers'] = User::where('role', 'customer')->count();
            $data['newCustomersInRange'] = User::where('role', 'customer')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();
            $data['activeCustomers'] = User::where('role', 'customer')
                ->where('is_active', true)
                ->count();
            
            // Recent Customers (last 10)
            $data['recentCustomers'] = User::where('role', 'customer')
                ->latest()
                ->take(10)
                ->get();
        }

        // ===================================
        // ORDER MANAGEMENT STATISTICS
        // ===================================
        if ($user->hasPermission('orders.view')) {
            // Date range filtered orders
            $data['totalOrders'] = Order::whereBetween('created_at', [$startDate, $endDate])->count();
            $data['pendingOrders'] = Order::where('status', 'pending')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();
            $data['processingOrders'] = Order::where('status', 'processing')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();
            $data['completedOrders'] = Order::where('status', 'delivered')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();
            $data['cancelledOrders'] = Order::where('status', 'cancelled')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();
            $data['todayOrders'] = Order::whereDate('created_at', today())->count();
            $data['monthOrders'] = Order::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count();
            
            // Revenue Statistics (Only Delivered Orders in date range)
            $data['totalRevenue'] = Order::where('status', 'delivered')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('total_amount');
            $data['todayRevenue'] = Order::where('status', 'delivered')
                ->whereDate('created_at', today())
                ->sum('total_amount');
            $data['monthRevenue'] = Order::where('status', 'delivered')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('total_amount');
            
            // Recent Orders
            $data['recentOrders'] = Order::with('user')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->latest()
                ->take(5)
                ->get();
        }

        // ===================================
        // PRODUCT MANAGEMENT STATISTICS
        // ===================================
        if ($user->hasPermission('products.view')) {
            $data['totalProducts'] = Product::count();
            $data['activeProducts'] = Product::where('status', 'published')->count();
            $data['draftProducts'] = Product::where('status', 'draft')->count();
            
            // Out of stock: Products where ALL variants are out of stock
            $data['outOfStockProducts'] = Product::whereHas('variants', function($q) {
                $q->where('stock_status', 'out_of_stock');
            })->whereDoesntHave('variants', function($q) {
                $q->where('stock_status', '!=', 'out_of_stock');
            })->count();
            
            // Low stock: Products where ANY variant has low stock (quantity <= alert level)
            $data['lowStockProducts'] = Product::whereHas('variants', function($q) {
                $q->whereColumn('stock_quantity', '<=', 'low_stock_alert')
                  ->where('stock_quantity', '>', 0);
            })->count();
            
            $data['totalCategories'] = Category::count();
            $data['totalBrands'] = Brand::count();
        }

        // ===================================
        // BLOG STATISTICS
        // ===================================
        if ($user->hasPermission('posts.view')) {
            $data['totalPosts'] = Post::count();
            $data['publishedPosts'] = Post::where('status', 'published')->count();
            $data['draftPosts'] = Post::where('status', 'draft')->count();
            $data['scheduledPosts'] = Post::where('status', 'scheduled')->count();
            $data['totalBlogViews'] = Post::sum('views_count');
        }

        // ===================================
        // REVIEW & Q&A STATISTICS
        // ===================================
        if ($user->hasPermission('reviews.view')) {
            $data['totalReviews'] = ProductReview::count();
            $data['pendingReviews'] = ProductReview::where('status', 'pending')->count();
            $data['approvedReviews'] = ProductReview::where('status', 'approved')->count();
            $data['averageRating'] = ProductReview::where('status', 'approved')->avg('rating');
        }

        if ($user->hasPermission('questions.view')) {
            $data['totalQuestions'] = ProductQuestion::count();
            $data['pendingQuestions'] = ProductQuestion::where('status', 'pending')->count();
            $data['approvedQuestions'] = ProductQuestion::where('status', 'approved')->count();
        }

        // ===================================
        // STOCK MANAGEMENT STATISTICS
        // ===================================
        if ($user->hasPermission('stock.view')) {
            $data['activeStockAlerts'] = StockAlert::where('status', 'active')->count();
            $data['resolvedStockAlerts'] = StockAlert::where('status', 'resolved')->count();
            $data['recentStockMovements'] = StockMovement::with('product', 'warehouse')
                ->latest()
                ->take(5)
                ->get();
        }

        // ===================================
        // BLOG COMMENTS STATISTICS
        // ===================================
        if ($user->hasPermission('blog-comments.view')) {
            $data['totalComments'] = Comment::count();
            $data['pendingComments'] = Comment::where('status', 'pending')->count();
            $data['approvedComments'] = Comment::where('status', 'approved')->count();
        }

        // ===================================
        // COUPON STATISTICS
        // ===================================
        if ($user->hasPermission('coupons.view')) {
            $data['totalCoupons'] = Coupon::count();
            $data['activeCoupons'] = Coupon::where('is_active', true)
                ->where(function($q) {
                    $q->whereNull('starts_at')
                      ->orWhere('starts_at', '<=', now());
                })
                ->where(function($q) {
                    $q->whereNull('expires_at')
                      ->orWhere('expires_at', '>=', now());
                })
                ->count();
            $data['expiredCoupons'] = Coupon::where('expires_at', '<', now())->count();
        }

        // ===================================
        // RECENT ACTIVITIES & ALERTS
        // ===================================
        $data['recentActivities'] = UserActivity::with('user')
            ->latest()
            ->take(10)
            ->get();

        // Critical Alerts
        $data['criticalAlerts'] = [
            'lowStock' => $data['lowStockProducts'] ?? 0,
            'outOfStock' => $data['outOfStockProducts'] ?? 0,
            'pendingOrders' => $data['pendingOrders'] ?? 0,
            'pendingReviews' => $data['pendingReviews'] ?? 0,
            'pendingQuestions' => $data['pendingQuestions'] ?? 0,
            'pendingComments' => $data['pendingComments'] ?? 0,
            'activeStockAlerts' => $data['activeStockAlerts'] ?? 0,
        ];

        // ===================================
        // CHARTS DATA
        // ===================================
        
        // Sales Chart (Date Range Based)
        if ($user->hasPermission('orders.view')) {
            $salesChart = [];
            $rangeDays = $startDate->diffInDays($endDate);
            
            // If range is more than 30 days, group by week; otherwise by day
            if ($rangeDays > 30) {
                // Group by week
                $weeks = ceil($rangeDays / 7);
                for ($i = $weeks - 1; $i >= 0; $i--) {
                    $weekStart = $endDate->copy()->subWeeks($i)->startOfWeek();
                    $weekEnd = $weekStart->copy()->endOfWeek();
                    
                    if ($weekEnd->gt($endDate)) $weekEnd = $endDate->copy();
                    if ($weekStart->lt($startDate)) $weekStart = $startDate->copy();
                    
                    $salesChart[] = [
                        'date' => $weekStart->format('M d') . '-' . $weekEnd->format('d'),
                        'orders' => Order::whereBetween('created_at', [$weekStart, $weekEnd])->count(),
                        'revenue' => Order::where('status', 'delivered')
                            ->whereBetween('created_at', [$weekStart, $weekEnd])
                            ->sum('total_amount'),
                    ];
                }
            } else {
                // Group by day
                $currentDate = $startDate->copy();
                while ($currentDate->lte($endDate)) {
                    $salesChart[] = [
                        'date' => $currentDate->format('M d'),
                        'orders' => Order::whereDate('created_at', $currentDate->toDateString())->count(),
                        'revenue' => Order::where('status', 'delivered')
                            ->whereDate('created_at', $currentDate->toDateString())
                            ->sum('total_amount'),
                    ];
                    $currentDate->addDay();
                }
            }
            
            $data['salesChart'] = $salesChart;
        }

        // Order Status Distribution (Pie Chart)
        if ($user->hasPermission('orders.view')) {
            $data['orderStatusChart'] = [
                ['status' => 'Pending', 'count' => $data['pendingOrders'] ?? 0, 'color' => '#f59e0b'],
                ['status' => 'Processing', 'count' => $data['processingOrders'] ?? 0, 'color' => '#3b82f6'],
                ['status' => 'Delivered', 'count' => $data['completedOrders'] ?? 0, 'color' => '#10b981'],
                ['status' => 'Cancelled', 'count' => $data['cancelledOrders'] ?? 0, 'color' => '#ef4444'],
            ];
        }

        // User Growth (Last 30 days)
        if ($user->hasPermission('users.view')) {
            $userGrowth = [];
            for ($i = 29; $i >= 0; $i--) {
                $date = now()->subDays($i);
                $userGrowth[] = [
                    'date' => $date->format('M d'),
                    'count' => User::whereDate('created_at', $date->toDateString())->count(),
                ];
            }
            $data['userGrowth'] = $userGrowth;
        }

        // Top Products by Sales (Date Range Filtered)
        if ($user->hasPermission('products.view') && $user->hasPermission('orders.view')) {
            $topProductIds = DB::table('order_items')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->where('orders.status', 'delivered')
                ->whereBetween('orders.created_at', [$startDate, $endDate])
                ->select('order_items.product_id', DB::raw('COUNT(order_items.id) as sales_count'))
                ->groupBy('order_items.product_id')
                ->orderBy('sales_count', 'desc')
                ->limit(5)
                ->pluck('sales_count', 'product_id');
            
            if ($topProductIds->isNotEmpty()) {
                $data['topProducts'] = Product::with(['images' => function($query) {
                        $query->where('is_primary', true)
                              ->orWhere('sort_order', 1)
                              ->with('media')
                              ->orderBy('is_primary', 'desc')
                              ->orderBy('sort_order');
                    }])
                    ->whereIn('id', $topProductIds->keys())
                    ->get()
                    ->map(function($product) use ($topProductIds) {
                        $product->sales_count = $topProductIds[$product->id];
                        return $product;
                    })
                    ->sortByDesc('sales_count')
                    ->values();
            } else {
                $data['topProducts'] = collect();
            }
        }

        return view('admin.dashboard', $data);
    }
}
