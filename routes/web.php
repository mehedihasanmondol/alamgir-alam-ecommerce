<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\SocialLoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\CouponController;
use App\Modules\Ecommerce\Order\Controllers\Customer\OrderController as CustomerOrderController;
use App\Http\Controllers\Customer\CustomerController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RobotsTxtController;
use App\Http\Controllers\SitemapController;
use App\Modules\Contact\Controllers\ContactController;
use App\Modules\Contact\Controllers\Admin\ContactSettingController;
use App\Modules\Contact\Controllers\Admin\ContactFaqController;
use App\Modules\Contact\Controllers\Admin\ContactMessageController;

// CSRF Token Refresh Route (for Livewire session maintenance)
Route::get('/refresh-csrf', function() {
    return response()->json(['token' => csrf_token()]);
})->name('refresh-csrf');

// SEO Routes
Route::get('/robots.txt', [RobotsTxtController::class, 'index'])->name('robots.txt');
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap.xml');

// Public Homepage
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/ecommerce', function() {
    return app(HomeController::class)->showDefaultHomepage();
})->name('ecommerce');
Route::get('/shop', \App\Livewire\Shop\ProductList::class)->name('shop');
// Route::get('/about', [HomeController::class, 'about'])->name('about');

// Contact Routes
Route::get('/contact', [ContactController::class, 'index'])->name('contact.index');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

// TEMPORARY: Test contact email
Route::get('/test-contact-email', function() {
    $message = \App\Models\ContactMessage::latest()->first();
    if ($message) {
        try {
            \Illuminate\Support\Facades\Mail::to('mhnoyonmondol@gmail.com')->send(new \App\Mail\ContactMessageReceived($message));
            return 'Email sent successfully! Check logs for details.';
        } catch (\Exception $e) {
            return 'Error: ' . $e->getMessage();
        }
    }
    return 'No contact messages found in database';
});

// Cart Routes
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/remove-multiple', [CartController::class, 'removeMultiple'])->name('cart.remove-multiple');

// Checkout Routes
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout/calculate-shipping', [CheckoutController::class, 'calculateShipping'])->name('checkout.calculate-shipping');
Route::get('/checkout/zone-methods', [CheckoutController::class, 'getZoneMethods'])->name('checkout.zone-methods');
Route::post('/checkout/place-order', [CheckoutController::class, 'placeOrder'])->name('checkout.place-order');

// Wishlist Routes
Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
Route::post('/wishlist/add', [WishlistController::class, 'add'])->name('wishlist.add');
Route::post('/wishlist/remove', [WishlistController::class, 'remove'])->name('wishlist.remove');
Route::post('/wishlist/move-to-cart', [WishlistController::class, 'moveToCart'])->name('wishlist.move-to-cart');
Route::get('/wishlist/count', [WishlistController::class, 'count'])->name('wishlist.count');

// Coupon Routes
Route::get('/coupons', [CouponController::class, 'index'])->name('coupons.index');

// Promotional Banner Routes
Route::post('/promo-banners/dismiss', function(\Illuminate\Http\Request $request) {
    $dismissedBanners = session()->get('dismissed_banners', []);
    $dismissedBanners[] = $request->input('banner_id');
    session()->put('dismissed_banners', $dismissedBanners);
    return response()->json(['success' => true]);
})->name('promo-banners.dismiss');

// Public Category Routes
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/categories/{slug}', \App\Livewire\Shop\ProductList::class)->name('categories.show');

// Public Brand Routes
Route::get('/brands', [\App\Http\Controllers\BrandController::class, 'index'])->name('brands.index');
Route::get('/brands/{slug}', \App\Livewire\Shop\ProductList::class)->name('brands.show');

// Authentication Routes (must be before catch-all product route)
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Social Login Routes
Route::get('/login/{provider}', [SocialLoginController::class, 'redirectToProvider'])->name('social.login');
Route::get('/login/{provider}/callback', [SocialLoginController::class, 'handleProviderCallback'])->name('social.callback');

// Password Reset Routes
Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');

// Search Routes
Route::get('/search', \App\Livewire\Search\SearchResults::class)->name('search.results');

// Feedback Routes (must be before catch-all route)
Route::get('/feedback', [FeedbackController::class, 'index'])->name('feedback.index');
Route::post('/feedback/{feedback}/helpful', [FeedbackController::class, 'helpful'])->name('feedback.helpful');
Route::post('/feedback/{feedback}/not-helpful', [FeedbackController::class, 'notHelpful'])->name('feedback.notHelpful');

// Blog Routes (must be before catch-all product route)
require __DIR__.'/blog.php';

// Admin Dashboard (Protected)
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized access');
        }
        return view('admin.dashboard');
    })->name('dashboard');
    
    // Admin Profile
    Route::get('/profile', function () {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized access');
        }
        $user = auth()->user();
        return view('admin.users.show', compact('user'));
    })->name('profile');

    // Product Management Routes
    Route::get('/products', [\App\Http\Controllers\Admin\ProductController::class, 'index'])->name('products.index');
    Route::get('/products/create', [\App\Http\Controllers\Admin\ProductController::class, 'create'])->name('products.create');
    Route::get('/products/{product}/edit', [\App\Http\Controllers\Admin\ProductController::class, 'edit'])->name('products.edit');
    Route::get('/products/{product}/images', function(\App\Modules\Ecommerce\Product\Models\Product $product) {
        return view('admin.product.images', compact('product'));
    })->name('products.images');
    
    // Product Attributes Routes
    Route::resource('attributes', \App\Modules\Ecommerce\Product\Controllers\AttributeController::class)->except(['show']);
    
    // Homepage Settings Routes
    Route::get('/homepage-settings', [\App\Http\Controllers\Admin\HomepageSettingController::class, 'index'])->name('homepage-settings.index');
    Route::put('/homepage-settings', [\App\Http\Controllers\Admin\HomepageSettingController::class, 'update'])->name('homepage-settings.update');
    Route::put('/homepage-settings/group/{group}', [\App\Http\Controllers\Admin\HomepageSettingController::class, 'updateGroup'])->name('homepage-settings.update-group');
    
    // Theme Settings Routes
    Route::get('/theme-settings', [\App\Http\Controllers\Admin\ThemeController::class, 'index'])->name('theme-settings.index');
    Route::get('/theme-settings/{theme}/edit', [\App\Http\Controllers\Admin\ThemeController::class, 'edit'])->name('theme-settings.edit');
    Route::put('/theme-settings/{theme}', [\App\Http\Controllers\Admin\ThemeController::class, 'update'])->name('theme-settings.update');
    Route::patch('/theme-settings/{theme}/activate', [\App\Http\Controllers\Admin\ThemeController::class, 'activate'])->name('theme-settings.activate');
    Route::post('/theme-settings/{theme}/duplicate', [\App\Http\Controllers\Admin\ThemeController::class, 'duplicate'])->name('theme-settings.duplicate');
    Route::patch('/theme-settings/{theme}/reset', [\App\Http\Controllers\Admin\ThemeController::class, 'reset'])->name('theme-settings.reset');
    Route::delete('/theme-settings/{theme}', [\App\Http\Controllers\Admin\ThemeController::class, 'destroy'])->name('theme-settings.destroy');
    
    // Hero Slider Routes
    Route::post('/homepage-settings/slider', [\App\Http\Controllers\Admin\HomepageSettingController::class, 'storeSlider'])->name('homepage-settings.slider.store');
    Route::put('/homepage-settings/slider/{slider}', [\App\Http\Controllers\Admin\HomepageSettingController::class, 'updateSlider'])->name('homepage-settings.slider.update');
    Route::delete('/homepage-settings/slider/{slider}', [\App\Http\Controllers\Admin\HomepageSettingController::class, 'destroySlider'])->name('homepage-settings.slider.destroy');
    Route::post('/homepage-settings/slider/reorder', [\App\Http\Controllers\Admin\HomepageSettingController::class, 'reorderSliders'])->name('homepage-settings.slider.reorder');
    
    // Secondary Menu Routes
    Route::get('/secondary-menu', [\App\Http\Controllers\Admin\SecondaryMenuController::class, 'index'])->name('secondary-menu.index');
    Route::post('/secondary-menu', [\App\Http\Controllers\Admin\SecondaryMenuController::class, 'store'])->name('secondary-menu.store');
    Route::put('/secondary-menu/{secondaryMenu}', [\App\Http\Controllers\Admin\SecondaryMenuController::class, 'update'])->name('secondary-menu.update');
    Route::delete('/secondary-menu/{secondaryMenu}', [\App\Http\Controllers\Admin\SecondaryMenuController::class, 'destroy'])->name('secondary-menu.destroy');
    Route::post('/secondary-menu/reorder', [\App\Http\Controllers\Admin\SecondaryMenuController::class, 'reorder'])->name('secondary-menu.reorder');
    
    // Sale Offers Routes
    Route::get('/sale-offers', [\App\Http\Controllers\Admin\SaleOfferController::class, 'index'])->name('sale-offers.index');
    Route::post('/sale-offers', [\App\Http\Controllers\Admin\SaleOfferController::class, 'store'])->name('sale-offers.store');
    Route::delete('/sale-offers/{saleOffer}', [\App\Http\Controllers\Admin\SaleOfferController::class, 'destroy'])->name('sale-offers.destroy');
    Route::post('/sale-offers/reorder', [\App\Http\Controllers\Admin\SaleOfferController::class, 'reorder'])->name('sale-offers.reorder');
    Route::patch('/sale-offers/{saleOffer}/toggle', [\App\Http\Controllers\Admin\SaleOfferController::class, 'toggleStatus'])->name('sale-offers.toggle');
    Route::post('/sale-offers/toggle-section', [\App\Http\Controllers\Admin\SaleOfferController::class, 'toggleSection'])->name('sale-offers.toggle-section');
    Route::post('/sale-offers/update-title', [\App\Http\Controllers\Admin\SaleOfferController::class, 'updateSectionTitle'])->name('sale-offers.update-title');
    
    // Category Management Routes
    Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class);
    
    // Payment Gateway Management Routes
    Route::get('/payment-gateways', [\App\Http\Controllers\Admin\PaymentGatewayController::class, 'index'])->name('payment-gateways.index');
    Route::get('/payment-gateways/{gateway}/edit', [\App\Http\Controllers\Admin\PaymentGatewayController::class, 'edit'])->name('payment-gateways.edit');
    Route::put('/payment-gateways/{gateway}', [\App\Http\Controllers\Admin\PaymentGatewayController::class, 'update'])->name('payment-gateways.update');
    Route::patch('/payment-gateways/{gateway}/toggle', [\App\Http\Controllers\Admin\PaymentGatewayController::class, 'toggleStatus'])->name('payment-gateways.toggle');
    
    // Stock Report Routes
    Route::prefix('stock')->name('stock.')->group(function () {
        Route::get('/reports', [\App\Modules\Stock\Controllers\StockReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/export-pdf', [\App\Modules\Stock\Controllers\StockReportController::class, 'exportPdf'])->name('reports.pdf');
        Route::get('/reports/export-excel', [\App\Modules\Stock\Controllers\StockReportController::class, 'exportExcel'])->name('reports.excel');
    });
    
    // Email Preferences Management Routes
    Route::prefix('email-preferences')->name('email-preferences.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\EmailPreferenceController::class, 'index'])->name('index');
        Route::get('/guideline', function () { return view('admin.email-preferences.guideline'); })->name('guideline');
        Route::get('/schedule-setup', [\App\Http\Controllers\Admin\EmailPreferenceController::class, 'scheduleSetup'])->name('schedule-setup');
        Route::get('/mail-setup', [\App\Http\Controllers\Admin\EmailPreferenceController::class, 'mailSetup'])->name('mail-setup');
        Route::post('/update-schedule', [\App\Http\Controllers\Admin\EmailPreferenceController::class, 'updateSchedule'])->name('update-schedule');
        Route::post('/send-test-email', [\App\Http\Controllers\Admin\EmailPreferenceController::class, 'sendTestEmail'])->name('send-test-email');
        Route::put('/{user}', [\App\Http\Controllers\Admin\EmailPreferenceController::class, 'update'])->name('update');
        Route::post('/bulk-update', [\App\Http\Controllers\Admin\EmailPreferenceController::class, 'bulkUpdate'])->name('bulk-update');
        Route::get('/export', [\App\Http\Controllers\Admin\EmailPreferenceController::class, 'export'])->name('export');
        Route::get('/newsletter-subscribers', [\App\Http\Controllers\Admin\EmailPreferenceController::class, 'newsletterSubscribers'])->name('newsletter-subscribers');
    });
    
    // Contact Management Routes
    Route::prefix('contact')->name('contact.')->group(function () {
        // Contact Settings (includes FAQs management)
        Route::get('/settings', [ContactSettingController::class, 'index'])->name('settings.index');
        Route::put('/settings', [ContactSettingController::class, 'update'])->name('settings.update');
        
        // FAQ Management (within settings)
        Route::post('/settings/faqs', [ContactSettingController::class, 'storeFaq'])->name('settings.faqs.store');
        Route::put('/settings/faqs/{faq}', [ContactSettingController::class, 'updateFaq'])->name('settings.faqs.update');
        Route::delete('/settings/faqs/{faq}', [ContactSettingController::class, 'destroyFaq'])->name('settings.faqs.destroy');
        Route::post('/settings/faqs/{faq}/toggle', [ContactSettingController::class, 'toggleFaq'])->name('settings.faqs.toggle');
        
        // Contact Messages
        Route::get('/messages', [ContactMessageController::class, 'index'])->name('messages.index');
        Route::get('/messages/{message}', [ContactMessageController::class, 'show'])->name('messages.show');
        Route::put('/messages/{message}/status', [ContactMessageController::class, 'updateStatus'])->name('messages.update-status');
        Route::delete('/messages/{message}', [ContactMessageController::class, 'destroy'])->name('messages.destroy');
        Route::post('/messages/bulk-action', [ContactMessageController::class, 'bulkAction'])->name('messages.bulk-action');
    });
});

// Customer Dashboard and Profile Routes (Protected)
Route::middleware(['auth'])->prefix('my')->name('customer.')->group(function () {
    // Dashboard
    Route::get('dashboard', [CustomerController::class, 'dashboard'])->name('dashboard');
    
    // Profile Management
    Route::get('profile', [CustomerController::class, 'profile'])->name('profile');
    Route::put('profile', [CustomerController::class, 'updateProfile'])->name('profile.update');
    
    // Address Management
    Route::get('addresses', [CustomerController::class, 'addresses'])->name('addresses.index');
    
    // Account Settings
    Route::get('settings', [CustomerController::class, 'settings'])->name('settings');
    Route::put('password', [CustomerController::class, 'updatePassword'])->name('password.update');
    Route::put('preferences', [CustomerController::class, 'updatePreferences'])->name('preferences.update');
    Route::delete('account', [CustomerController::class, 'deleteAccount'])->name('account.delete');
    
    // Order Management
    Route::get('orders', [CustomerOrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [CustomerOrderController::class, 'show'])->name('orders.show');
    Route::post('orders/{order}/cancel', [CustomerOrderController::class, 'cancel'])->name('orders.cancel');
    Route::get('orders/{order}/invoice', [CustomerOrderController::class, 'invoice'])->name('orders.invoice');
    
    // Appointment Management
    Route::get('appointments', [App\Http\Controllers\Customer\AppointmentController::class, 'index'])->name('appointments.index');
    Route::get('appointments/{appointment}', [App\Http\Controllers\Customer\AppointmentController::class, 'show'])->name('appointments.show');
    Route::post('appointments/{appointment}/cancel', [App\Http\Controllers\Customer\AppointmentController::class, 'cancel'])->name('appointments.cancel');
    
    // Feedback Management
    Route::get('feedback', [App\Http\Controllers\Customer\FeedbackController::class, 'index'])->name('feedback.index');
    Route::get('feedback/{feedback}', [App\Http\Controllers\Customer\FeedbackController::class, 'show'])->name('feedback.show');
    Route::put('feedback/{feedback}', [App\Http\Controllers\Customer\FeedbackController::class, 'update'])->name('feedback.update');
    Route::delete('feedback/{feedback}', [App\Http\Controllers\Customer\FeedbackController::class, 'destroy'])->name('feedback.destroy');
});

// Public Order Tracking
Route::get('track-order', [CustomerOrderController::class, 'track'])->name('orders.track');
Route::post('track-order', [CustomerOrderController::class, 'track']);

// Payment Routes
Route::get('/payment/process/{gateway}/{order}', [PaymentController::class, 'process'])->name('payment.process');
Route::post('/payment/initiate/{order}', [PaymentController::class, 'initiate'])->name('payment.initiate');
Route::get('/payment/bkash/callback', [PaymentController::class, 'bkashCallback'])->name('payment.bkash.callback');
Route::post('/payment/bkash/callback', [PaymentController::class, 'bkashCallback']);
Route::get('/payment/nagad/callback', [PaymentController::class, 'nagadCallback'])->name('payment.nagad.callback');
Route::post('/payment/nagad/callback', [PaymentController::class, 'nagadCallback']);
Route::post('/payment/sslcommerz/success', [PaymentController::class, 'sslcommerzSuccess'])->name('payment.sslcommerz.success');
Route::post('/payment/sslcommerz/fail', [PaymentController::class, 'sslcommerzFail'])->name('payment.sslcommerz.fail');
Route::post('/payment/sslcommerz/cancel', [PaymentController::class, 'sslcommerzCancel'])->name('payment.sslcommerz.cancel');



// Public Product and Blog Post Routes (must be last to avoid conflicts)
// This route handles both products and blog posts by slug
// Named 'products.show' as primary, but works for both products and blog posts
Route::get('/{slug}', function($slug) {
    // Try to find product first
    $product = \App\Modules\Ecommerce\Product\Models\Product::where('slug', $slug)->first();
    if ($product) {
        return app(\App\Http\Controllers\ProductController::class)->show($slug);
    }
    
    // Then try to find blog post (published or unlisted)
    $post = \App\Modules\Blog\Models\Post::where('slug', $slug)
        ->whereIn('status', ['published', 'unlisted'])
        ->first();
    if ($post) {
        return app(\App\Modules\Blog\Controllers\Frontend\BlogController::class)->show($slug);
    }
    
    // Neither found
    abort(404);
})->name('products.show');

