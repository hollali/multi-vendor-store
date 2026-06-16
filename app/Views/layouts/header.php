<!DOCTYPE html>
<html lang="en" class="">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php if (isset($csrf_meta) && $csrf_meta instanceof Closure): ?>
        <?= $csrf_meta() ?>
    <?php endif; ?>
    <meta name="csrf-token" content="<?= htmlspecialchars($_SESSION['_csrf_token'] ?? '') ?>">
    <title><?= htmlspecialchars($site_name ?? 'Celer Market') ?> | Multi-Vendor Marketplace</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: { DEFAULT: '#1D4ED8', 50: '#EFF6FF', 100: '#DBEAFE', 200: '#BFDBFE', 300: '#93C5FD', 400: '#60A5FA', 500: '#3B82F6', 600: '#2563EB', 700: '#1D4ED8', 800: '#1E40AF', 900: '#1E3A8A' },
                        accent: { DEFAULT: '#EA580C', 50: '#FFF7ED', 100: '#FFEDD5', 200: '#FED7AA', 300: '#FDBA74', 400: '#FB923C', 500: '#F97316', 600: '#EA580C', 700: '#C2410C', 800: '#9A3412', 900: '#7C2D12' }
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
        .toast { animation: slideIn 0.3s ease-out; }
        @keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
        @keyframes slideOut { from { transform: translateX(0); opacity: 1; } to { transform: translateX(100%); opacity: 0; } }
    </style>
</head>
<body class="bg-gray-50 dark:bg-gray-950 text-gray-800 dark:text-gray-200 antialiased transition-colors duration-200">

<?php
$session = $session ?? \App\Core\Session::getInstance();
$user = $user ?? $session->getUser();
$isLoggedIn = $session->isAuthenticated();
$userRole = $session->getUserRole();
$cartCount = $cartCount ?? $_SESSION['cart_count'] ?? 0;
$categories = $categories ?? \App\Models\Category::where('parent_id', null)->get();
$currentUrl = $_SERVER['REQUEST_URI'] ?? '/';
$flashMessages = [];
foreach (['success', 'error', 'warning', 'info'] as $type) {
    if ($session->hasFlash($type)) {
        $flashMessages[$type] = $session->getFlash($type);
    }
}
?>

<script>var cartCount = <?= (int)$cartCount ?>;</script>

<header class="sticky top-0 z-50">
    <nav class="bg-white/90 dark:bg-gray-900/90 backdrop-blur-xl border-b border-gray-200/80 dark:border-gray-800/80 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 md:h-20">

                <div class="flex items-center gap-2 md:gap-3 flex-shrink-0">
                    <button type="button" id="mobile-menu-btn" class="md:hidden p-2 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 focus:outline-none" aria-label="Toggle menu">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <a href="<?= $site_url ?? '/' ?>" class="flex items-center gap-2 group">
                        <span class="flex items-center justify-center w-9 h-9 bg-gradient-to-br from-primary-700 to-primary-500 rounded-lg shadow-md group-hover:shadow-lg transition-shadow">
                            <i class="fas fa-bolt text-white text-lg"></i>
                        </span>
                        <span class="hidden sm:block text-xl font-extrabold bg-gradient-to-r from-primary-700 to-accent-600 bg-clip-text text-transparent">Celer Market</span>
                    </a>
                </div>

                <div class="hidden md:flex flex-1 max-w-xl mx-6 lg:mx-10">
                    <form action="<?= $site_url ?? '' ?>/shop/search" method="GET" class="w-full">
                        <div class="relative">
                            <input type="text" name="q" placeholder="Search products, brands, categories..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>" class="w-full pl-11 pr-4 py-2.5 bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-full text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:text-gray-200 placeholder-gray-400 dark:placeholder-gray-500 transition">
                            <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500 text-sm"></i>
                        </div>
                    </form>
                </div>

                <div class="flex items-center gap-1 md:gap-2">
                    <a href="<?= $site_url ?? '' ?>/wishlist" class="relative p-2.5 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition" aria-label="Wishlist">
                        <i class="far fa-heart text-lg"></i>
                        <?php if ($isLoggedIn): ?>
                            <span class="absolute -top-0.5 -right-0.5 w-4 h-4 bg-red-500 rounded-full text-white text-[10px] font-bold flex items-center justify-center">0</span>
                        <?php endif; ?>
                    </a>

                    <a href="<?= $site_url ?? '' ?>/cart" class="relative p-2.5 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition" aria-label="Cart">
                        <i class="fas fa-shopping-bag text-lg"></i>
                        <span id="cart-badge" class="absolute -top-0.5 -right-0.5 w-4 h-4 bg-accent-600 rounded-full text-white text-[10px] font-bold flex items-center justify-center <?= $cartCount > 0 ? '' : 'hidden' ?>"><?= $cartCount ?></span>
                    </a>

                    <?php if ($isLoggedIn && $user): ?>
                        <div class="relative group hidden md:block">
                            <button class="flex items-center gap-2 p-1.5 pl-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                                <span class="w-7 h-7 bg-gradient-to-br from-primary-500 to-primary-700 rounded-full flex items-center justify-center text-white text-xs font-bold shadow-sm">
                                    <?= strtoupper(substr($user->name ?? $user['name'] ?? 'U', 0, 1)) ?>
                                </span>
                                <span class="text-sm font-medium max-w-[100px] truncate hidden lg:block"><?= htmlspecialchars($user->name ?? $user['name'] ?? 'User') ?></span>
                                <i class="fas fa-chevron-down text-[10px] text-gray-400"></i>
                            </button>
                            <div class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 transform origin-top-right">
                                <div class="p-3 border-b border-gray-100 dark:border-gray-700">
                                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-200 truncate"><?= htmlspecialchars($user->name ?? $user['name'] ?? 'User') ?></p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate"><?= htmlspecialchars($user->email ?? $user['email'] ?? '') ?></p>
                                </div>
                                <div class="p-1">
                                    <?php
                                    $dashboards = ['customer' => '/dashboard', 'vendor' => '/vendor/dashboard', 'admin' => '/admin/dashboard'];
                                    $dashboardUrl = $dashboards[$userRole] ?? '/dashboard';
                                    ?>
                                    <a href="<?= $dashboardUrl ?>" class="flex items-center gap-3 px-3 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 rounded-lg transition"><i class="fas fa-th-large w-4 text-gray-400"></i>Dashboard</a>
                                    <a href="<?= $site_url ?? '' ?>/orders" class="flex items-center gap-3 px-3 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 rounded-lg transition"><i class="fas fa-box w-4 text-gray-400"></i>Orders</a>
                                    <hr class="my-1 border-gray-100 dark:border-gray-700">
                                    <a href="<?= $site_url ?? '' ?>/profile" class="flex items-center gap-3 px-3 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 rounded-lg transition"><i class="fas fa-user-cog w-4 text-gray-400"></i>Profile Settings</a>
                                    <a href="<?= $site_url ?? '' ?>/logout" class="flex items-center gap-3 px-3 py-2.5 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition"><i class="fas fa-sign-out-alt w-4"></i>Logout</a>
                                </div>
                            </div>
                        </div>
                        <a href="<?= $site_url ?? '' ?>/logout" class="md:hidden p-2.5 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition" aria-label="Logout">
                            <i class="fas fa-sign-out-alt text-lg"></i>
                        </a>
                    <?php else: ?>
                        <div class="hidden md:flex items-center gap-2">
                            <a href="/login" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition">Login</a>
                            <a href="/register" class="px-5 py-2 text-sm font-medium text-white bg-gradient-to-r from-primary-700 to-primary-600 hover:from-primary-800 hover:to-primary-700 rounded-lg shadow-sm hover:shadow-md transition-all">Register</a>
                        </div>
                        <a href="/login" class="md:hidden p-2.5 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition" aria-label="Login">
                            <i class="fas fa-user text-lg"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="md:hidden pb-3">
                <form action="<?= $site_url ?? '' ?>/shop/search" method="GET" class="w-full">
                    <div class="relative">
                        <input type="text" name="q" placeholder="Search products..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>" class="w-full pl-10 pr-4 py-2.5 bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-full text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:text-gray-200 placeholder-gray-400">
                        <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    </div>
                </form>
            </div>
        </div>
    </nav>

    <nav id="mobile-nav" class="hidden md:hidden bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 shadow-lg">
        <div class="px-4 py-3 space-y-1">
            <?php if ($isLoggedIn && $user): ?>
                <div class="flex items-center gap-3 px-3 py-3 border-b border-gray-100 dark:border-gray-800 mb-2">
                    <span class="w-10 h-10 bg-gradient-to-br from-primary-500 to-primary-700 rounded-full flex items-center justify-center text-white font-bold shadow-sm"><?= strtoupper(substr($user->name ?? $user['name'] ?? 'U', 0, 1)) ?></span>
                    <div>
                        <p class="text-sm font-semibold text-gray-800 dark:text-gray-200"><?= htmlspecialchars($user->name ?? $user['name'] ?? 'User') ?></p>
                        <p class="text-xs text-gray-500 dark:text-gray-400"><?= htmlspecialchars($user->email ?? $user['email'] ?? '') ?></p>
                    </div>
                </div>
            <?php endif; ?>
            <a href="/" class="flex items-center gap-3 px-3 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-lg transition"><i class="fas fa-home w-4 text-gray-400"></i>Home</a>
            <a href="/shop" class="flex items-center gap-3 px-3 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-lg transition"><i class="fas fa-store w-4 text-gray-400"></i>Shop</a>
            <a href="/wishlist" class="flex items-center gap-3 px-3 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-lg transition"><i class="far fa-heart w-4 text-gray-400"></i>Wishlist</a>
            <a href="/cart" class="flex items-center gap-3 px-3 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-lg transition"><i class="fas fa-shopping-bag w-4 text-gray-400"></i>Cart<?php if ($cartCount > 0): ?><span class="ml-auto bg-accent-600 text-white text-xs font-bold px-2 py-0.5 rounded-full"><?= $cartCount ?></span><?php endif; ?></a>
            <?php if ($isLoggedIn): ?>
                <?php $dashboards = ['customer' => '/dashboard', 'vendor' => '/vendor/dashboard', 'admin' => '/admin/dashboard']; ?>
                <a href="<?= $dashboards[$userRole] ?? '/dashboard' ?>" class="flex items-center gap-3 px-3 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-lg transition"><i class="fas fa-th-large w-4 text-gray-400"></i>Dashboard</a>
                <hr class="my-1 border-gray-100 dark:border-gray-800">
                <a href="/logout" class="flex items-center gap-3 px-3 py-2.5 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition"><i class="fas fa-sign-out-alt w-4"></i>Logout</a>
            <?php else: ?>
                <hr class="my-1 border-gray-100 dark:border-gray-800">
                <a href="/login" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium text-primary-700 dark:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/20 rounded-lg transition"><i class="fas fa-sign-in-alt w-4"></i>Login</a>
                <a href="/register" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-primary-700 to-primary-600 rounded-lg transition text-center justify-center mt-1">Create Account</a>
            <?php endif; ?>
        </div>
    </nav>

    <?php if (!empty($categories) && count($categories) > 0): ?>
    <nav class="bg-gray-100 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center gap-1 overflow-x-auto scrollbar-hide py-2 text-sm whitespace-nowrap">
                <a href="/shop" class="flex-shrink-0 px-3 py-1.5 text-gray-600 dark:text-gray-400 hover:text-primary-700 dark:hover:text-primary-400 font-medium transition">All</a>
                <?php foreach ($categories as $cat): ?>
                    <?php
                    $slug = $cat->slug ?? $cat['slug'] ?? '';
                    $catName = $cat->name ?? $cat['name'] ?? '';
                    $isActive = strpos($currentUrl, '/category/' . $slug) !== false;
                    ?>
                    <a href="/category/<?= htmlspecialchars($slug) ?>"
                       class="flex-shrink-0 px-3 py-1.5 rounded-full font-medium transition <?= $isActive ? 'bg-primary-700 text-white shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:bg-white dark:hover:bg-gray-700' ?>">
                        <?= htmlspecialchars($catName) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </nav>
    <?php endif; ?>
</header>

<?php if (!empty($flashMessages)): ?>
<div class="fixed top-20 right-4 z-[100] space-y-2 max-w-sm" id="flash-container">
    <?php foreach ($flashMessages as $type => $msg): ?>
        <?php
            $typeStyles = [
                'success' => 'bg-green-50 dark:bg-green-900/30 border-green-400 dark:border-green-600 text-green-800 dark:text-green-300',
                'error'   => 'bg-red-50 dark:bg-red-900/30 border-red-400 dark:border-red-600 text-red-800 dark:text-red-300',
                'warning' => 'bg-yellow-50 dark:bg-yellow-900/30 border-yellow-400 dark:border-yellow-600 text-yellow-800 dark:text-yellow-300',
                'info'    => 'bg-blue-50 dark:bg-blue-900/30 border-blue-400 dark:border-blue-600 text-blue-800 dark:text-blue-300',
            ];
            $icons = ['success' => 'fa-check-circle', 'error' => 'fa-exclamation-circle', 'warning' => 'fa-exclamation-triangle', 'info' => 'fa-info-circle'];
            $style = $typeStyles[$type] ?? $typeStyles['info'];
            $icon = $icons[$type] ?? 'fa-info-circle';
        ?>
        <div class="toast flex items-center gap-3 px-4 py-3 rounded-lg border shadow-lg <?= $style ?>" role="alert">
            <i class="fas <?= $icon ?> flex-shrink-0"></i>
            <p class="text-sm font-medium flex-1"><?= htmlspecialchars(is_array($msg) ? implode(', ', $msg) : $msg) ?></p>
            <button onclick="this.parentElement.remove()" class="flex-shrink-0 opacity-60 hover:opacity-100 transition" aria-label="Dismiss"><i class="fas fa-times text-sm"></i></button>
        </div>
    <?php endforeach; ?>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            document.querySelectorAll('#flash-container .toast').forEach(function(el) {
                el.style.animation = 'slideOut 0.3s ease-out forwards';
                setTimeout(function() { el.remove(); }, 300);
            });
        }, 5000);
    });
</script>
<?php endif; ?>

<main class="min-h-screen md:flex">
