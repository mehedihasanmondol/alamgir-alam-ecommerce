<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\TrendingProductController;
use App\Http\Controllers\Admin\BestSellerProductController;
use App\Http\Controllers\Admin\NewArrivalProductController;
use App\Http\Controllers\Admin\FooterManagementController;
use App\Http\Controllers\Admin\ProductQuestionController;
use App\Http\Controllers\Admin\ReviewController;
use App\Modules\User\Controllers\UserController;
use App\Modules\User\Controllers\RoleController;
use App\Modules\Ecommerce\Brand\Controllers\BrandController;
use App\Modules\Ecommerce\Order\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DeliveryZoneController;
use App\Http\Controllers\Admin\DeliveryMethodController;
use App\Http\Controllers\Admin\DeliveryRateController;
use App\Http\Controllers\Admin\CouponController as AdminCouponController;
use App\Http\Controllers\Admin\SiteSettingController;
use App\Modules\Blog\Controllers\Admin\TickMarkController;
use Illuminate\Support\Facades\Route;

/**
 * ModuleName: Admin Panel
 * Purpose: Admin routes for dashboard, user, role, category, and brand management
 * 
 * Access Control:
 * - admin.access: Allows admin and author roles (not customer)
 * - Public routes: Accessible to all users (defined in web.php and blog.php)
 * 
 * @author AI Assistant
 * @date 2025-11-04
 * @updated 2025-11-17
 */

Route::middleware(['auth', 'admin.access'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard - Accessible to all admin panel users
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // User Management Routes - Only Super Admin
    Route::middleware(['permission:users.view'])->group(function () {
        Route::resource('users', UserController::class);
        Route::post('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])
            ->name('users.toggle-status');
    });

    // Role Management Routes - Only Super Admin
    Route::middleware(['permission:roles.view'])->group(function () {
        Route::resource('roles', RoleController::class);
    });

    // Category Management Routes - Requires product permissions
    Route::middleware(['permission:products.view'])->group(function () {
        Route::resource('categories', CategoryController::class);
        Route::post('categories/{category}/toggle-status', [CategoryController::class, 'toggleStatus'])
            ->name('categories.toggle-status');
        Route::post('categories/{category}/duplicate', [CategoryController::class, 'duplicate'])
            ->name('categories.duplicate');
    });

    // Brand Management Routes - Requires product permissions
    Route::middleware(['permission:products.view'])->group(function () {
        Route::resource('brands', BrandController::class);
        Route::post('brands/{brand}/toggle-status', [BrandController::class, 'toggleStatus'])
            ->name('brands.toggle-status');
        Route::post('brands/{brand}/toggle-featured', [BrandController::class, 'toggleFeatured'])
            ->name('brands.toggle-featured');
        Route::post('brands/{brand}/duplicate', [BrandController::class, 'duplicate'])
            ->name('brands.duplicate');
    });

    // Order Management Routes - Requires order permissions
    Route::middleware(['permission:orders.view'])->group(function () {
        Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('orders/create', [OrderController::class, 'create'])->name('orders.create');
        Route::post('orders', [OrderController::class, 'store'])->name('orders.store');
        Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::get('orders/{order}/edit', function (\App\Modules\Ecommerce\Order\Models\Order $order) {
            return view('admin.orders.edit-livewire', compact('order'));
        })->name('orders.edit');
        Route::put('orders/{order}', [OrderController::class, 'update'])->name('orders.update');
        Route::delete('orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');
        Route::post('orders/{order}/update-status', [OrderController::class, 'updateStatus'])
            ->name('orders.update-status');
        Route::post('orders/{order}/cancel', [OrderController::class, 'cancel'])
            ->name('orders.cancel');
        Route::get('orders/{order}/invoice', [OrderController::class, 'invoice'])
            ->name('orders.invoice');
    });

    // Customer Management Routes
    Route::post('customers/{id}/update-info', [CustomerController::class, 'updateInfo'])
        ->name('customers.update-info');

    // Trending Products Management Routes - Requires product permissions
    Route::middleware(['permission:products.view'])->group(function () {
        Route::get('trending-products/search', [TrendingProductController::class, 'searchProducts'])->name('trending-products.search');
        Route::get('trending-products', [TrendingProductController::class, 'index'])->name('trending-products.index');
        Route::post('trending-products', [TrendingProductController::class, 'store'])->name('trending-products.store');
        Route::post('trending-products/update-order', [TrendingProductController::class, 'updateOrder'])->name('trending-products.update-order');
        Route::post('trending-products/{trendingProduct}/toggle-status', [TrendingProductController::class, 'toggleStatus'])->name('trending-products.toggle-status');
        Route::post('trending-products/toggle-section', [TrendingProductController::class, 'toggleSection'])->name('trending-products.toggle-section');
        Route::post('trending-products/update-title', [TrendingProductController::class, 'updateSectionTitle'])->name('trending-products.update-title');
        Route::delete('trending-products/{trendingProduct}', [TrendingProductController::class, 'destroy'])->name('trending-products.destroy');
    });

    // Best Seller Products Management Routes - Requires product permissions
    Route::middleware(['permission:products.view'])->group(function () {
        Route::get('best-seller-products/search', [BestSellerProductController::class, 'searchProducts'])->name('best-seller-products.search');
        Route::get('best-seller-products', [BestSellerProductController::class, 'index'])->name('best-seller-products.index');
        Route::post('best-seller-products', [BestSellerProductController::class, 'store'])->name('best-seller-products.store');
        Route::post('best-seller-products/update-order', [BestSellerProductController::class, 'updateOrder'])->name('best-seller-products.update-order');
        Route::post('best-seller-products/{bestSellerProduct}/toggle-status', [BestSellerProductController::class, 'toggleStatus'])->name('best-seller-products.toggle-status');
        Route::post('best-seller-products/toggle-section', [BestSellerProductController::class, 'toggleSection'])->name('best-seller-products.toggle-section');
        Route::post('best-seller-products/update-title', [BestSellerProductController::class, 'updateSectionTitle'])->name('best-seller-products.update-title');
        Route::delete('best-seller-products/{bestSellerProduct}', [BestSellerProductController::class, 'destroy'])->name('best-seller-products.destroy');
    });

    // New Arrival Products Management Routes - Requires product permissions
    Route::middleware(['permission:products.view'])->group(function () {
        Route::get('new-arrival-products/search', [NewArrivalProductController::class, 'searchProducts'])->name('new-arrival-products.search');
        Route::get('new-arrival-products', [NewArrivalProductController::class, 'index'])->name('new-arrival-products.index');
        Route::post('new-arrival-products', [NewArrivalProductController::class, 'store'])->name('new-arrival-products.store');
        Route::post('new-arrival-products/update-order', [NewArrivalProductController::class, 'updateOrder'])->name('new-arrival-products.update-order');
        Route::post('new-arrival-products/{newArrivalProduct}/toggle-status', [NewArrivalProductController::class, 'toggleStatus'])->name('new-arrival-products.toggle-status');
        Route::post('new-arrival-products/toggle-section', [NewArrivalProductController::class, 'toggleSection'])->name('new-arrival-products.toggle-section');
        Route::post('new-arrival-products/update-title', [NewArrivalProductController::class, 'updateSectionTitle'])->name('new-arrival-products.update-title');
        Route::delete('new-arrival-products/{newArrivalProduct}', [NewArrivalProductController::class, 'destroy'])->name('new-arrival-products.destroy');
    });

    // Product Q&A Management Routes - Requires product permissions
    Route::middleware(['permission:products.view'])->group(function () {
        Route::resource('product-questions', ProductQuestionController::class);
        Route::post('questions/{id}/approve', [ProductQuestionController::class, 'approve'])->name('questions.approve');
        Route::post('questions/{id}/reject', [ProductQuestionController::class, 'reject'])->name('questions.reject');
        Route::post('answers/{id}/approve', [ProductQuestionController::class, 'approveAnswer'])->name('answers.approve');
        Route::post('answers/{id}/reject', [ProductQuestionController::class, 'rejectAnswer'])->name('answers.reject');
        Route::post('answers/{id}/best', [ProductQuestionController::class, 'markBestAnswer'])->name('answers.best');
    });

    // Product Review Management Routes - Requires product permissions
    Route::middleware(['permission:products.view'])->group(function () {
        Route::get('reviews', [ReviewController::class, 'index'])->name('reviews.index');
        Route::get('reviews/pending', [ReviewController::class, 'pending'])->name('reviews.pending');
        Route::get('reviews/{id}', [ReviewController::class, 'show'])->name('reviews.show');
        Route::post('reviews/{id}/approve', [ReviewController::class, 'approve'])->name('reviews.approve');
        Route::post('reviews/{id}/reject', [ReviewController::class, 'reject'])->name('reviews.reject');
        Route::delete('reviews/{id}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
        Route::post('reviews/bulk-approve', [ReviewController::class, 'bulkApprove'])->name('reviews.bulk-approve');
        Route::post('reviews/bulk-delete', [ReviewController::class, 'bulkDelete'])->name('reviews.bulk-delete');
    });

    // Footer Management Routes - Only Super Admin
    Route::middleware(['permission:users.view'])->group(function () {
        Route::get('footer-management', [FooterManagementController::class, 'index'])->name('footer-management.index');
        Route::post('footer-management/settings', [FooterManagementController::class, 'updateSettings'])->name('footer-management.update-settings');
        Route::post('footer-management/toggle-section', [FooterManagementController::class, 'toggleSection'])->name('footer-management.toggle-section');
        Route::post('footer-management/links', [FooterManagementController::class, 'storeLink'])->name('footer-management.store-link');
        Route::put('footer-management/links/{link}', [FooterManagementController::class, 'updateLink'])->name('footer-management.update-link');
        Route::delete('footer-management/links/{link}', [FooterManagementController::class, 'deleteLink'])->name('footer-management.delete-link');
        Route::post('footer-management/blog-posts', [FooterManagementController::class, 'storeBlogPost'])->name('footer-management.store-blog');
        Route::delete('footer-management/blog-posts/{blogPost}', [FooterManagementController::class, 'deleteBlogPost'])->name('footer-management.delete-blog');
    });

    // Blog Tick Marks Management Routes - Accessible to authors (posts.view permission)
    Route::middleware(['permission:posts.view'])->prefix('blog')->name('blog.')->group(function () {
        Route::resource('tick-marks', TickMarkController::class);
        Route::patch('tick-marks/{tick_mark}/toggle-active', [TickMarkController::class, 'toggleActive'])
            ->name('tick-marks.toggle-active');
        Route::post('tick-marks/update-sort-order', [TickMarkController::class, 'updateSortOrder'])
            ->name('tick-marks.update-sort-order');
    });

    // Feedback Management Routes - Requires feedback permissions
    Route::middleware(['permission:feedback.view'])->prefix('feedback')->name('feedback.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\FeedbackController::class, 'index'])->name('index');

        // YouTube Import Routes - MUST be before {feedback} route to avoid conflicts
        Route::get('youtube', [\App\Http\Controllers\Admin\YouTubeFeedbackController::class, 'index'])->name('youtube.index');
        Route::get('youtube/setup', [\App\Http\Controllers\Admin\YouTubeSetupController::class, 'index'])->name('youtube.setup');
        Route::put('youtube/settings', [\App\Http\Controllers\Admin\YouTubeFeedbackController::class, 'updateSettings'])->name('youtube.update-settings');
        Route::post('youtube/test', [\App\Http\Controllers\Admin\YouTubeFeedbackController::class, 'testConnection'])->name('youtube.test');
        Route::post('youtube/import', [\App\Http\Controllers\Admin\YouTubeFeedbackController::class, 'import'])->name('youtube.import');
        Route::get('youtube/import-progress', [\App\Http\Controllers\Admin\YouTubeFeedbackController::class, 'importProgress'])->name('youtube.import-progress');
        Route::get('youtube/history', [\App\Http\Controllers\Admin\YouTubeFeedbackController::class, 'importHistory'])->name('youtube.history');

        // Single feedback route - must be after specific routes like 'youtube'
        Route::get('{feedback}', [\App\Http\Controllers\Admin\FeedbackController::class, 'show'])->name('show');

        Route::middleware(['permission:feedback.approve'])->group(function () {
            Route::post('{feedback}/approve', [\App\Http\Controllers\Admin\FeedbackController::class, 'approve'])->name('approve');
        });

        Route::middleware(['permission:feedback.reject'])->group(function () {
            Route::post('{feedback}/reject', [\App\Http\Controllers\Admin\FeedbackController::class, 'reject'])->name('reject');
        });

        Route::middleware(['permission:feedback.feature'])->group(function () {
            Route::post('{feedback}/feature', [\App\Http\Controllers\Admin\FeedbackController::class, 'toggleFeature'])->name('feature');
        });

        Route::middleware(['permission:feedback.delete'])->group(function () {
            Route::delete('{feedback}', [\App\Http\Controllers\Admin\FeedbackController::class, 'destroy'])->name('destroy');
        });
    });

    // Appointment Management Routes
    Route::middleware(['permission:appointments.view'])->prefix('appointments')->name('appointments.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\AppointmentController::class, 'index'])->name('index');
    });

    // Chamber Management Routes
    Route::middleware(['permission:chambers.manage'])->prefix('chambers')->name('chambers.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\ChamberController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\ChamberController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\ChamberController::class, 'store'])->name('store');
        Route::get('/{chamber}/edit', [\App\Http\Controllers\Admin\ChamberController::class, 'edit'])->name('edit');
        Route::put('/{chamber}', [\App\Http\Controllers\Admin\ChamberController::class, 'update'])->name('update');
        Route::post('/{chamber}/toggle-status', [\App\Http\Controllers\Admin\ChamberController::class, 'toggleStatus'])->name('toggle-status');
        Route::delete('/{chamber}', [\App\Http\Controllers\Admin\ChamberController::class, 'destroy'])->name('destroy');
    });

    // Delivery Management Routes - Requires order permissions
    Route::middleware(['permission:orders.view'])->prefix('delivery')->name('delivery.')->group(function () {
        // Delivery Zones
        Route::resource('zones', DeliveryZoneController::class);
        Route::post('zones/{zone}/toggle-status', [DeliveryZoneController::class, 'toggleStatus'])
            ->name('zones.toggle-status');

        // Delivery Methods
        Route::resource('methods', DeliveryMethodController::class);
        Route::post('methods/{method}/toggle-status', [DeliveryMethodController::class, 'toggleStatus'])
            ->name('methods.toggle-status');

        // Delivery Rates
        Route::resource('rates', DeliveryRateController::class);
        Route::post('rates/{rate}/toggle-status', [DeliveryRateController::class, 'toggleStatus'])
            ->name('rates.toggle-status');
    });

    // Coupon Management Routes - Requires order permissions
    Route::middleware(['permission:orders.view'])->group(function () {
        Route::get('coupons', [AdminCouponController::class, 'index'])->name('coupons.index');
        Route::get('coupons/create', [AdminCouponController::class, 'create'])->name('coupons.create');
        Route::get('coupons/{coupon}/edit', [AdminCouponController::class, 'edit'])->name('coupons.edit');
        Route::get('coupons/{coupon}/statistics', [AdminCouponController::class, 'statistics'])->name('coupons.statistics');
    });

    // Site Settings Routes - Only Super Admin
    Route::middleware(['permission:users.view'])->group(function () {
        Route::get('site-settings', [SiteSettingController::class, 'index'])->name('site-settings.index');
        Route::put('site-settings', [SiteSettingController::class, 'update'])->name('site-settings.update');
        Route::put('site-settings/{group}', [SiteSettingController::class, 'updateGroup'])->name('site-settings.update-group');
        Route::post('site-settings/remove-logo', [SiteSettingController::class, 'removeLogo'])->name('site-settings.remove-logo');
    });

    // System Settings Routes - Requires system settings permission
    Route::middleware(['permission:system.settings.view'])->group(function () {
        Route::get('system-settings', [\App\Http\Controllers\Admin\SystemSettingsController::class, 'index'])->name('system-settings.index');
    });

    // Stock Management Routes - Requires stock permissions
    Route::middleware(['permission:stock.view'])->prefix('stock')->name('stock.')->group(function () {
        Route::get('/', [\App\Modules\Stock\Controllers\StockController::class, 'index'])->name('index');
        Route::get('/movements', [\App\Modules\Stock\Controllers\StockController::class, 'movements'])->name('movements');
        Route::get('/add', [\App\Modules\Stock\Controllers\StockController::class, 'createAddStock'])->name('add');
        Route::post('/add', [\App\Modules\Stock\Controllers\StockController::class, 'storeAddStock'])->name('add.store');
        Route::get('/remove', [\App\Modules\Stock\Controllers\StockController::class, 'createRemoveStock'])->name('remove');
        Route::post('/remove', [\App\Modules\Stock\Controllers\StockController::class, 'storeRemoveStock'])->name('remove.store');
        Route::get('/adjust', [\App\Modules\Stock\Controllers\StockController::class, 'createAdjustStock'])->name('adjust');
        Route::post('/adjust', [\App\Modules\Stock\Controllers\StockController::class, 'storeAdjustStock'])->name('adjust.store');
        Route::get('/transfer', [\App\Modules\Stock\Controllers\StockController::class, 'createTransfer'])->name('transfer');
        Route::post('/transfer', [\App\Modules\Stock\Controllers\StockController::class, 'storeTransfer'])->name('transfer.store');
        Route::get('/alerts', [\App\Modules\Stock\Controllers\StockController::class, 'alerts'])->name('alerts');
        Route::post('/alerts/{id}/resolve', [\App\Modules\Stock\Controllers\StockController::class, 'resolveAlert'])->name('alerts.resolve');
        Route::get('/current-stock', [\App\Modules\Stock\Controllers\StockController::class, 'getCurrentStock'])->name('current-stock');
    });

    // Warehouse Management Routes - Requires stock permissions
    Route::middleware(['permission:stock.view'])->group(function () {
        Route::resource('warehouses', \App\Modules\Stock\Controllers\WarehouseController::class)->names('warehouses');
        Route::post('warehouses/{id}/set-default', [\App\Modules\Stock\Controllers\WarehouseController::class, 'setDefault'])->name('warehouses.set-default');
    });

    // Supplier Management Routes - Requires stock permissions
    Route::middleware(['permission:stock.view'])->group(function () {
        Route::resource('suppliers', \App\Modules\Stock\Controllers\SupplierController::class)->names('suppliers');
    });

    // Reports Routes - Accessible to all admin users
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('index');
        Route::get('/sales', [\App\Http\Controllers\Admin\ReportController::class, 'sales'])->name('sales');
        Route::get('/products', [\App\Http\Controllers\Admin\ReportController::class, 'products'])->name('products');
        Route::get('/inventory', [\App\Http\Controllers\Admin\ReportController::class, 'inventory'])->name('inventory');
        Route::get('/customers', [\App\Http\Controllers\Admin\ReportController::class, 'customers'])->name('customers');
        Route::get('/delivery', [\App\Http\Controllers\Admin\ReportController::class, 'delivery'])->name('delivery');

        // Export routes
        Route::get('/export/sales-pdf', [\App\Http\Controllers\Admin\ReportController::class, 'exportSalesPdf'])->name('export-sales-pdf');
        Route::get('/export/inventory-pdf', [\App\Http\Controllers\Admin\ReportController::class, 'exportInventoryPdf'])->name('export-inventory-pdf');
        Route::get('/export/products-pdf', [\App\Http\Controllers\Admin\ReportController::class, 'exportProductsPdf'])->name('export-products-pdf');
        Route::get('/export/customers-pdf', [\App\Http\Controllers\Admin\ReportController::class, 'exportCustomersPdf'])->name('export-customers-pdf');
        Route::get('/export/delivery-pdf', [\App\Http\Controllers\Admin\ReportController::class, 'exportDeliveryPdf'])->name('export-delivery-pdf');
    });
});
