<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/storage/logs/error.log');
require __DIR__ . '/app/autoload.php';

use App\Core\Session;
use App\Core\Router;
use App\Core\Middleware;

$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (str_starts_with(trim($line), '#')) continue;
        if (str_contains($line, '=')) {
            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            if (!empty($key)) {
                putenv("{$key}={$value}");
                $_ENV[$key] = $value;
            }
        }
    }
}

// Import server-provided env vars (Railway, PHP-FPM, etc.)
$envVars = ['DB_HOST', 'DB_PORT', 'DB_DATABASE', 'DB_USERNAME', 'DB_PASSWORD',
            'DATABASE_URL', 'MYSQL_URL', 'JAWSDB_URL', 'CLEARDB_DATABASE_URL',
            'APP_ENV', 'APP_DEBUG', 'APP_URL',
            'PAYSTACK_PUBLIC_KEY', 'PAYSTACK_SECRET_KEY', 'PAYSTACK_WEBHOOK_SECRET',
            'MAIL_HOST', 'MAIL_PORT', 'MAIL_USERNAME', 'MAIL_PASSWORD',
            'MAIL_FROM_ADDRESS', 'MAIL_FROM_NAME',
            'GOOGLE_CLIENT_ID', 'GOOGLE_CLIENT_SECRET', 'GOOGLE_REDIRECT_URI'];
foreach ($envVars as $key) {
    $value = $_SERVER[$key] ?? $_ENV[$key] ?? null;
    if ($value !== null && $value !== '' && getenv($key) === false) {
        putenv("{$key}={$value}");
        $_ENV[$key] = $value;
    }
}

$session = Session::getInstance();
$session->start();

$router = new Router();

// Home
$router->get('/', 'HomeController@index');
$router->get('/home', 'HomeController@index');

// Auth
$router->get('/login', 'AuthController@loginForm', 'guest');
$router->post('/login', 'AuthController@login');
$router->get('/register', 'AuthController@registerForm', 'guest');
$router->post('/register', 'AuthController@register');
$router->get('/logout', 'AuthController@logout', 'auth');
$router->get('/forgot-password', 'AuthController@forgotPasswordForm', 'guest');
$router->post('/forgot-password', 'AuthController@forgotPassword');
$router->get('/reset-password/{token}', 'AuthController@resetPasswordForm', 'guest');
$router->post('/reset-password', 'AuthController@resetPassword');
$router->get('/auth/google', 'AuthController@redirectToGoogle');
$router->get('/auth/google/callback', 'AuthController@handleGoogleCallback');

// Shop
$router->get('/shop', 'ShopController@index');
$router->get('/shop/category/{slug}', 'ShopController@category');
$router->get('/shop/search', 'ShopController@search');
$router->get('/product/{slug}', 'ShopController@show');
$router->get('/store/{slug}', 'ShopController@store');

// Cart
$router->get('/cart', 'CartController@index');
$router->post('/cart/add', 'CartController@add');
$router->post('/cart/update', 'CartController@update');
$router->post('/cart/remove', 'CartController@remove');
$router->post('/cart/apply-coupon', 'CartController@applyCoupon');

// Wishlist
$router->get('/wishlist', 'WishlistController@index', 'auth');
$router->post('/wishlist/toggle', 'WishlistController@toggle', 'auth');

// Checkout
$router->get('/checkout', 'CheckoutController@index', 'auth');
$router->post('/checkout/place-order', 'CheckoutController@placeOrder', 'auth');
$router->get('/checkout/callback', 'CheckoutController@callback');
$router->post('/checkout/webhook', 'CheckoutController@webhook');

// Customer Dashboard
$router->get('/dashboard', 'DashboardController@index', 'auth');
$router->get('/dashboard/orders', 'DashboardController@orders', 'auth');
$router->get('/dashboard/orders/{id}', 'DashboardController@orderDetail', 'auth');
$router->get('/dashboard/wishlist', 'DashboardController@wishlist', 'auth');
$router->get('/dashboard/addresses', 'DashboardController@addresses', 'auth');
$router->post('/dashboard/addresses', 'DashboardController@saveAddress', 'auth');
$router->post('/dashboard/addresses/delete', 'DashboardController@deleteAddress', 'auth');
$router->get('/dashboard/profile', 'DashboardController@profile', 'auth');
$router->post('/dashboard/profile', 'DashboardController@updateProfile', 'auth');
$router->get('/dashboard/reviews', 'DashboardController@reviews', 'auth');
$router->post('/dashboard/reviews', 'DashboardController@submitReview', 'auth');
$router->get('/dashboard/notifications', 'DashboardController@notifications', 'auth');
$router->post('/dashboard/notifications/read', 'DashboardController@markNotificationRead', 'auth');

// Vendor Dashboard
$router->get('/vendor/dashboard', 'VendorController@dashboard', 'vendor');
$router->get('/vendor/products', 'VendorController@products', 'vendor');
$router->get('/vendor/products/create', 'VendorController@createProduct', 'vendor');
$router->post('/vendor/products/store', 'VendorController@storeProduct', 'vendor');
$router->get('/vendor/products/{id}/edit', 'VendorController@editProduct', 'vendor');
$router->post('/vendor/products/{id}/update', 'VendorController@updateProduct', 'vendor');
$router->post('/vendor/products/{id}/delete', 'VendorController@deleteProduct', 'vendor');
$router->get('/vendor/orders', 'VendorController@orders', 'vendor');
$router->get('/vendor/orders/{id}', 'VendorController@orderDetail', 'vendor');
$router->post('/vendor/orders/{id}/status', 'VendorController@updateOrderStatus', 'vendor');
$router->get('/vendor/reviews', 'VendorController@reviews', 'vendor');
$router->get('/vendor/coupons', 'VendorController@coupons', 'vendor');
$router->post('/vendor/coupons', 'VendorController@storeCoupon', 'vendor');
$router->post('/vendor/coupons/{id}/delete', 'VendorController@deleteCoupon', 'vendor');
$router->get('/vendor/earnings', 'VendorController@earnings', 'vendor');
$router->get('/vendor/withdrawals', 'VendorController@withdrawals', 'vendor');
$router->post('/vendor/withdrawals', 'VendorController@requestWithdrawal', 'vendor');
$router->get('/vendor/store', 'VendorController@storeSettings', 'vendor');
$router->post('/vendor/store', 'VendorController@updateStore', 'vendor');
$router->get('/vendor/notifications', 'VendorController@notifications', 'vendor');

// Admin Dashboard
$router->get('/admin/dashboard', 'AdminController@dashboard', 'admin');
$router->get('/admin/users', 'AdminController@users', 'admin');
$router->post('/admin/users/{id}/status', 'AdminController@updateUserStatus', 'admin');
$router->get('/admin/vendors', 'AdminController@vendors', 'admin');
$router->post('/admin/vendors/{id}/verify', 'AdminController@verifyVendor', 'admin');
$router->get('/admin/products', 'AdminController@products', 'admin');
$router->post('/admin/products/{id}/approve', 'AdminController@approveProduct', 'admin');
$router->post('/admin/products/{id}/reject', 'AdminController@rejectProduct', 'admin');
$router->post('/admin/products/{id}/featured', 'AdminController@toggleFeatured', 'admin');
$router->get('/admin/categories', 'AdminController@categories', 'admin');
$router->post('/admin/categories', 'AdminController@storeCategory', 'admin');
$router->post('/admin/categories/{id}/delete', 'AdminController@deleteCategory', 'admin');
$router->get('/admin/brands', 'AdminController@brands', 'admin');
$router->post('/admin/brands', 'AdminController@storeBrand', 'admin');
$router->post('/admin/brands/{id}/delete', 'AdminController@deleteBrand', 'admin');
$router->get('/admin/orders', 'AdminController@orders', 'admin');
$router->get('/admin/orders/{id}', 'AdminController@orderDetail', 'admin');
$router->post('/admin/orders/{id}/status', 'AdminController@updateOrderStatus', 'admin');
$router->get('/admin/transactions', 'AdminController@transactions', 'admin');
$router->get('/admin/withdrawals', 'AdminController@withdrawals', 'admin');
$router->post('/admin/withdrawals/{id}/process', 'AdminController@processWithdrawal', 'admin');
$router->get('/admin/banners', 'AdminController@banners', 'admin');
$router->post('/admin/banners', 'AdminController@storeBanner', 'admin');
$router->post('/admin/banners/{id}/toggle', 'AdminController@toggleBanner', 'admin');
$router->post('/admin/banners/{id}/delete', 'AdminController@deleteBanner', 'admin');
$router->get('/admin/settings', 'AdminController@settings', 'admin');
$router->post('/admin/settings', 'AdminController@updateSettings', 'admin');
$router->get('/admin/notifications', 'AdminController@notifications', 'admin');
$router->post('/admin/notifications/send', 'AdminController@sendNotification', 'admin');
$router->get('/admin/profile', 'AdminController@profile', 'admin');
$router->post('/admin/profile', 'AdminController@updateProfile', 'admin');
$router->post('/admin/profile/password', 'AdminController@updatePassword', 'admin');

// API
$router->get('/api/products', 'ApiController@products');
$router->get('/api/products/{id}', 'ApiController@productDetail');
$router->get('/api/categories', 'ApiController@categories');
$router->get('/api/stores', 'ApiController@stores');

$basePath = dirname($_SERVER['SCRIPT_NAME']);
$uri = $_SERVER['REQUEST_URI'] ?? '/';
$uri = parse_url($uri, PHP_URL_PATH);
if ($basePath !== '/' && str_starts_with($uri, $basePath)) {
    $uri = substr($uri, strlen($basePath));
}
$uri = preg_replace('#/index\.php$#', '', $uri);
$uri = $uri ?: '/';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

try {
    $router->resolve($uri, $method);
} catch (\Throwable $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode([
        'error' => 'Internal Server Error',
        'message' => getenv('APP_DEBUG') ? $e->getMessage() : 'An unexpected error occurred',
    ]);
}
