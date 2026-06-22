<!DOCTYPE html>
<html lang="en" class="">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php if (isset($csrf_meta) && $csrf_meta instanceof Closure): ?>
        <?= $csrf_meta() ?>
    <?php endif; ?>
    <meta name="csrf-token" content="<?= htmlspecialchars($_SESSION['_csrf_token'] ?? '') ?>">
    <meta name="base-url" content="<?= $site_url ?? '' ?>">
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
        .toast { animation: slideIn 0.3s ease-out; }
        @keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
        @keyframes slideOut { from { transform: translateX(0); opacity: 1; } to { transform: translateX(100%); opacity: 0; } }
        .geo-dropdown { max-height: 280px; overflow-y: auto; scrollbar-width: thin; }
        .geo-dropdown::-webkit-scrollbar { width: 4px; }
        .geo-dropdown::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 4px; }
        .dark .geo-dropdown::-webkit-scrollbar-thumb { background: #4b5563; }
        .nav-search:focus + .search-icon { color: #1D4ED8; }
        .hero-swiper .swiper-pagination-bullet { background: rgba(255,255,255,0.6); width: 10px; height: 10px; opacity: 1; }
        .hero-swiper .swiper-pagination-bullet-active { background: #EA580C; width: 28px; border-radius: 6px; }
        .hero-swiper .swiper-button-next::after, .hero-swiper .swiper-button-prev::after { font-size: 16px; font-weight: 700; }
        .hero-swiper .swiper-slide img { animation: heroZoom 8s ease-out forwards; }
        @keyframes heroZoom { 0% { transform: scale(1.1); } 100% { transform: scale(1); } }
        .trending-swiper .swiper-slide { height: auto; }
        .trending-swiper .swiper-button-next::after, .trending-swiper .swiper-button-prev::after { font-size: 14px; font-weight: 700; }
    </style>
</head>
<body class="bg-gray-50 dark:bg-gray-950 text-gray-800 dark:text-gray-200 antialiased transition-colors duration-200">

<?php
$session = $session ?? \App\Core\Session::getInstance();
$user = $user ?? $session->getUser();
$isLoggedIn = $session->isAuthenticated();
$userRole = $session->getUserRole();
$cartCount = $cartCount ?? $_SESSION['cart_count'] ?? 0;
$categories = $categories ?? \App\Models\Category::where('parent_id', null, 'IS')->get();
$currentUrl = $_SERVER['REQUEST_URI'] ?? '/';
$flashMessages = [];
foreach (['success', 'error', 'warning', 'info'] as $type) {
    if ($session->hasFlash($type)) {
        $flashMessages[$type] = $session->getFlash($type);
    }
}

function flagEmoji($code) {
    $code = strtoupper($code);
    if (strlen($code) !== 2) return '';
    $first = 0x1F1E6 + ord($code[0]) - ord('A');
    $second = 0x1F1E6 + ord($code[1]) - ord('A');
    return json_decode('"\u{' . dechex($first) . '}\u{' . dechex($second) . '}"');
}
?>

<script>var cartCount = <?= (int)$cartCount ?>;</script>

<header class="sticky top-0 z-50">

    <div class="hidden md:block bg-gray-100 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 text-[13px]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between h-9">
            <div class="flex items-center gap-4">
                <span class="text-gray-500 dark:text-gray-400">
                    <i class="fas fa-truck text-primary-600 dark:text-primary-400 mr-1.5"></i>
                    Free shipping on orders over GH₵200
                </span>
            </div>
            <div class="flex items-center gap-3">
                <div class="relative group">
                    <button class="flex items-center gap-1.5 px-2.5 py-1 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 transition rounded hover:bg-gray-200 dark:hover:bg-gray-800">
                        <?= flagEmoji($geo_country_code ?? 'GH') ?>
                        <span class="font-medium"><?= htmlspecialchars($geo_country_code ?? 'GH') ?></span>
                        <i class="fas fa-chevron-down text-[9px] ml-0.5"></i>
                    </button>
                    <div class="geo-dropdown absolute left-0 mt-1 w-56 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 transform origin-top-left z-50">
                        <div class="p-2 border-b border-gray-100 dark:border-gray-700">
                            <p class="text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider px-2 py-1">Select Country</p>
                        </div>
                        <div class="p-1 max-h-60 overflow-y-auto">
                            <?php foreach ($geo_all_countries ?? [] as $c): ?>
                                <?php $cc = $c->code ?? $c['code'] ?? ''; ?>
                                <button type="button" data-geo="country" data-code="<?= htmlspecialchars($cc) ?>" class="flex items-center gap-2.5 w-full px-3 py-2 text-sm rounded-lg transition <?= ($cc === ($geo_country_code ?? 'GH')) ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-400 font-medium' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50' ?>">
                                    <span class="text-base"><?= flagEmoji($cc) ?></span>
                                    <span><?= htmlspecialchars($c->name ?? $c['name'] ?? '') ?></span>
                                    <?php if ($cc === ($geo_country_code ?? 'GH')): ?>
                                        <i class="fas fa-check ml-auto text-primary-600 text-[10px]"></i>
                                    <?php endif; ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <span class="text-gray-300 dark:text-gray-700">|</span>

                <div class="relative group">
                    <button class="flex items-center gap-1.5 px-2.5 py-1 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 transition rounded hover:bg-gray-200 dark:hover:bg-gray-800">
                        <span class="font-semibold"><?= htmlspecialchars($geo_currency_symbol ?? 'GH₵') ?></span>
                        <span class="font-medium"><?= htmlspecialchars($geo_currency_code ?? 'GHS') ?></span>
                        <i class="fas fa-chevron-down text-[9px] ml-0.5"></i>
                    </button>
                    <div class="geo-dropdown absolute right-0 mt-1 w-64 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 transform origin-top-right z-50">
                        <div class="p-2 border-b border-gray-100 dark:border-gray-700">
                            <p class="text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider px-2 py-1">Select Currency</p>
                        </div>
                        <div class="p-1 max-h-60 overflow-y-auto">
                            <?php foreach ($geo_all_currencies ?? [] as $c): ?>
                                <?php $curCode = $c->code ?? $c['code'] ?? ''; ?>
                                <button type="button" data-geo="currency" data-code="<?= htmlspecialchars($curCode) ?>" class="flex items-center gap-3 w-full px-3 py-2.5 text-sm rounded-lg transition <?= ($curCode === ($geo_currency_code ?? 'GHS')) ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-400 font-medium' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50' ?>">
                                    <span class="font-semibold text-gray-500 dark:text-gray-400 w-8"><?= htmlspecialchars($c->symbol ?? $c['symbol'] ?? '') ?></span>
                                    <span class="flex-1"><?= htmlspecialchars($curCode) ?> - <?= htmlspecialchars($c->name ?? $c['name'] ?? '') ?></span>
                                    <?php if ($curCode === ($geo_currency_code ?? 'GHS')): ?>
                                        <i class="fas fa-check text-primary-600 text-[10px]"></i>
                                    <?php endif; ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <?php if ($isLoggedIn): ?>
                    <a href="<?= $site_url ?? '' ?>/<?= $userRole === 'vendor' ? 'vendor' : ($userRole === 'admin' ? 'admin' : 'dashboard') ?>/dashboard" class="flex items-center gap-1.5 px-2.5 py-1 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 transition rounded hover:bg-gray-200 dark:hover:bg-gray-800">
                        <i class="fas fa-th-large text-[11px]"></i>
                        <span class="font-medium">Dashboard</span>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <nav class="bg-white/95 dark:bg-gray-900/95 backdrop-blur-xl border-b border-gray-200/80 dark:border-gray-800/80 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 md:h-20">

                <div class="flex items-center gap-2 md:gap-3 flex-shrink-0">
                    <button type="button" id="mobile-menu-btn" class="md:hidden p-2.5 rounded-xl text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 focus:outline-none transition" aria-label="Toggle menu">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <a href="<?= $site_url ?? '/' ?>" class="flex items-center gap-2.5 group">
                        <img src="/uploads/logos/logo1.png" alt="<?= htmlspecialchars($site_name ?? 'Celer Market') ?>" class="h-9 w-auto md:h-10">
                        <span class="hidden sm:block text-xl font-extrabold text-primary-700 dark:text-primary-400 tracking-tight"><?= htmlspecialchars($site_name ?? 'Celer Market') ?></span>
                    </a>
                </div>

                <div class="hidden md:flex flex-1 max-w-xl mx-6 lg:mx-10">
                    <form action="<?= $site_url ?? '' ?>/shop/search" method="GET" class="w-full" autocomplete="off">
                        <div class="relative">
                            <input type="text" name="q" placeholder="Search products, brands, categories..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>" data-autocomplete="<?= $site_url ?? '' ?>/shop/search/autocomplete" class="nav-search w-full pl-11 pr-12 py-2.5 bg-gray-100 dark:bg-gray-800 border-2 border-transparent focus:border-primary-500 dark:focus:border-primary-500 rounded-xl text-sm focus:outline-none focus:bg-white dark:focus:bg-gray-800 dark:text-gray-200 placeholder-gray-400 dark:placeholder-gray-500 transition-all duration-200">
                            <i class="fas fa-search search-icon absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500 text-sm transition-colors duration-200"></i>
                            <button type="submit" class="absolute right-1.5 top-1/2 -translate-y-1/2 px-3 py-1.5 bg-primary-700 hover:bg-primary-800 text-white text-xs font-medium rounded-lg transition shadow-sm">
                                Search
                            </button>
                        </div>
                    </form>
                </div>

                <div class="flex items-center gap-1 md:gap-1.5">
                    <button type="button" class="dark-toggle-btn p-2.5 rounded-xl text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition" aria-label="Toggle dark mode">
                        <i class="fas fa-moon dark:hidden text-lg"></i>
                        <i class="fas fa-sun hidden dark:inline text-lg"></i>
                    </button>
                    <a href="<?= $site_url ?? '' ?>/wishlist" class="relative p-2.5 rounded-xl text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition" aria-label="Wishlist">
                        <i class="far fa-heart text-lg"></i>
                        <?php if ($isLoggedIn): ?>
                            <span class="absolute -top-0.5 -right-0.5 w-4 h-4 bg-red-500 rounded-full text-white text-[10px] font-bold flex items-center justify-center shadow-sm">0</span>
                        <?php endif; ?>
                    </a>

                    <a href="<?= $site_url ?? '' ?>/cart" class="relative p-2.5 rounded-xl text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition" aria-label="Cart">
                        <i class="fas fa-shopping-bag text-lg"></i>
                        <span id="cart-badge" class="absolute -top-0.5 -right-0.5 w-4 h-4 bg-accent-600 rounded-full text-white text-[10px] font-bold flex items-center justify-center shadow-sm <?= $cartCount > 0 ? '' : 'hidden' ?>"><?= $cartCount ?></span>
                    </a>

                    <?php if ($isLoggedIn && $user): ?>
                        <div class="relative group hidden md:block">
                            <button class="flex items-center gap-2 p-1.5 pl-3 rounded-xl text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                                <span class="w-8 h-8 bg-primary-700 rounded-full flex items-center justify-center text-white text-xs font-bold shadow-sm ring-2 ring-white dark:ring-gray-800">
                                    <?= strtoupper(substr($user->name ?? $user['name'] ?? 'U', 0, 1)) ?>
                                </span>
                                <span class="text-sm font-medium max-w-[100px] truncate hidden lg:block"><?= htmlspecialchars($user->name ?? $user['name'] ?? 'User') ?></span>
                                <i class="fas fa-chevron-down text-[10px] text-gray-400 transition-transform duration-200 group-hover:rotate-180"></i>
                            </button>
                            <div class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 transform origin-top-right">
                                <div class="p-3 border-b border-gray-100 dark:border-gray-700">
                                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-200 truncate"><?= htmlspecialchars($user->name ?? $user['name'] ?? 'User') ?></p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate"><?= htmlspecialchars($user->email ?? $user['email'] ?? '') ?></p>
                                </div>
                                <div class="p-1.5">
                                    <?php
                                    $dashboards = ['customer' => 'dashboard', 'vendor' => 'vendor/dashboard', 'admin' => 'admin/dashboard'];
                                    $dashboardUrl = $dashboards[$userRole] ?? 'dashboard';
                                    ?>
                                    <a href="<?= $site_url ?? '' ?>/<?= $dashboardUrl ?>" class="flex items-center gap-3 px-3 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 rounded-lg transition"><i class="fas fa-th-large w-4 text-gray-400"></i>Dashboard</a>
                                    <a href="<?= $site_url ?? '' ?>/orders" class="flex items-center gap-3 px-3 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 rounded-lg transition"><i class="fas fa-box w-4 text-gray-400"></i>Orders</a>
                                    <?php if ($userRole === 'vendor'): ?>
                                        <a href="<?= $site_url ?? '' ?>/vendor/products" class="flex items-center gap-3 px-3 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 rounded-lg transition"><i class="fas fa-boxes w-4 text-gray-400"></i>Products</a>
                                        <a href="<?= $site_url ?? '' ?>/vendor/earnings" class="flex items-center gap-3 px-3 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 rounded-lg transition"><i class="fas fa-chart-line w-4 text-gray-400"></i>Earnings</a>
                                    <?php endif; ?>
                                    <hr class="my-1 border-gray-100 dark:border-gray-700">
                                    <?php $profileUrls = ['customer' => 'dashboard/profile', 'vendor' => 'vendor/profile', 'admin' => 'admin/profile']; ?>
                                    <a href="<?= $site_url ?? '' ?>/<?= $profileUrls[$userRole] ?? 'dashboard/profile' ?>" class="flex items-center gap-3 px-3 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 rounded-lg transition"><i class="fas fa-user-cog w-4 text-gray-400"></i>Profile Settings</a>
                                    <a href="<?= $site_url ?? '' ?>/logout" class="flex items-center gap-3 px-3 py-2.5 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition"><i class="fas fa-sign-out-alt w-4"></i>Logout</a>
                                </div>
                            </div>
                        </div>
                        <a href="<?= $site_url ?? '' ?>/logout" class="md:hidden p-2.5 rounded-xl text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition" aria-label="Logout">
                            <i class="fas fa-sign-out-alt text-lg"></i>
                        </a>
                    <?php else: ?>
                        <div class="hidden md:flex items-center gap-2">
                            <a href="<?= $site_url ?? '' ?>/login" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-xl transition">Login</a>
                            <a href="<?= $site_url ?? '' ?>/register" class="px-5 py-2 text-sm font-medium text-white bg-primary-700 hover:bg-primary-800 rounded-xl shadow-sm hover:shadow-md transition-all">Register</a>
                        </div>
                        <a href="<?= $site_url ?? '' ?>/login" class="md:hidden p-2.5 rounded-xl text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition" aria-label="Login">
                            <i class="fas fa-user text-lg"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="md:hidden pb-3">
                <form action="<?= $site_url ?? '' ?>/shop/search" method="GET" class="w-full" autocomplete="off">
                    <div class="relative">
                        <input type="text" name="q" placeholder="Search products..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>" class="w-full pl-10 pr-4 py-2.5 bg-gray-100 dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 focus:border-primary-500 rounded-xl text-sm focus:outline-none focus:bg-white dark:focus:bg-gray-800 dark:text-gray-200 placeholder-gray-400 transition-all">
                        <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    </div>
                </form>
            </div>
        </div>
    </nav>

    <nav id="mobile-nav" class="hidden md:hidden bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 shadow-lg max-h-[80vh] overflow-y-auto">
        <div class="px-4 py-3 space-y-1">
            <?php if ($isLoggedIn && $user): ?>
                <div class="flex items-center gap-3 px-3 py-3 border-b border-gray-100 dark:border-gray-800 mb-2">
                    <span class="w-10 h-10 bg-primary-700 rounded-full flex items-center justify-center text-white font-bold shadow-sm flex-shrink-0"><?= strtoupper(substr($user->name ?? $user['name'] ?? 'U', 0, 1)) ?></span>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-gray-800 dark:text-gray-200 truncate"><?= htmlspecialchars($user->name ?? $user['name'] ?? 'User') ?></p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate"><?= htmlspecialchars($user->email ?? $user['email'] ?? '') ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <div class="flex items-center justify-between px-3 py-2 mb-2 bg-gray-50 dark:bg-gray-800/50 rounded-xl">
                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-500 dark:text-gray-400"><?= flagEmoji($geo_country_code ?? 'GH') ?> <?= htmlspecialchars($geo_country_code ?? 'GH') ?></span>
                    <span class="text-gray-300 dark:text-gray-600">|</span>
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300"><?= htmlspecialchars($geo_currency_symbol ?? 'GH₵') ?> <?= htmlspecialchars($geo_currency_code ?? 'GHS') ?></span>
                </div>
                <button type="button" class="dark-toggle-btn p-2 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700 transition" aria-label="Toggle dark mode">
                    <i class="fas fa-moon dark:hidden"></i>
                    <i class="fas fa-sun hidden dark:inline"></i>
                </button>
            </div>

            <a href="<?= $site_url ?? '/' ?>" class="flex items-center gap-3 px-3 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-xl transition"><i class="fas fa-home w-4 text-gray-400"></i>Home</a>
            <a href="<?= $site_url ?? '' ?>/shop" class="flex items-center gap-3 px-3 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-xl transition"><i class="fas fa-store w-4 text-gray-400"></i>Shop</a>
            <a href="<?= $site_url ?? '' ?>/wishlist" class="flex items-center gap-3 px-3 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-xl transition"><i class="far fa-heart w-4 text-gray-400"></i>Wishlist</a>
            <a href="<?= $site_url ?? '' ?>/cart" class="flex items-center gap-3 px-3 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-xl transition"><i class="fas fa-shopping-bag w-4 text-gray-400"></i>Cart<?php if ($cartCount > 0): ?><span class="ml-auto bg-accent-600 text-white text-xs font-bold px-2 py-0.5 rounded-full"><?= $cartCount ?></span><?php endif; ?></a>
            <?php if ($isLoggedIn): ?>
                <?php $dashboards = ['customer' => 'dashboard', 'vendor' => 'vendor/dashboard', 'admin' => 'admin/dashboard']; ?>
                <a href="<?= $site_url ?? '' ?>/<?= $dashboards[$userRole] ?? 'dashboard' ?>" class="flex items-center gap-3 px-3 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-xl transition"><i class="fas fa-th-large w-4 text-gray-400"></i>Dashboard</a>
                <hr class="my-1 border-gray-100 dark:border-gray-800">
                <a href="<?= $site_url ?? '' ?>/logout" class="flex items-center gap-3 px-3 py-2.5 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-xl transition"><i class="fas fa-sign-out-alt w-4"></i>Logout</a>
            <?php else: ?>
                <hr class="my-1 border-gray-100 dark:border-gray-800">
                <a href="<?= $site_url ?? '' ?>/login" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium text-primary-700 dark:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/20 rounded-xl transition"><i class="fas fa-sign-in-alt w-4"></i>Login</a>
                <a href="<?= $site_url ?? '' ?>/register" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium text-white bg-primary-700 hover:bg-primary-800 rounded-xl transition text-center justify-center mt-1 shadow-sm">Create Account</a>
            <?php endif; ?>
        </div>
    </nav>

    <?php if (!empty($categories) && count($categories) > 0): ?>
    <nav class="bg-gray-100 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 shadow-inner">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center gap-1 overflow-x-auto scrollbar-hide py-2.5 text-sm whitespace-nowrap">
                <a href="<?= $site_url ?? '' ?>/shop" class="flex-shrink-0 px-3.5 py-1.5 text-gray-600 dark:text-gray-400 hover:text-primary-700 dark:hover:text-primary-400 font-medium transition rounded-full hover:bg-white/60 dark:hover:bg-gray-700/50 <?= $currentUrl === ($site_url ?? '') . '/shop' || $currentUrl === ($site_url ?? '') . '/shop/' ? 'bg-primary-700 text-white hover:text-white dark:hover:text-white' : '' ?>">
                    All
                </a>
                <?php foreach ($categories as $cat): ?>
                    <?php
                    $slug = $cat->slug ?? $cat['slug'] ?? '';
                    $catName = $cat->name ?? $cat['name'] ?? '';
                    $isActive = strpos($currentUrl, '/shop/category/' . $slug) !== false;
                    ?>
                    <a href="<?= $site_url ?? '' ?>/shop/category/<?= htmlspecialchars($slug) ?>"
                       class="flex-shrink-0 px-3.5 py-1.5 rounded-full font-medium transition <?= $isActive ? 'bg-primary-700 text-white shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:bg-white/60 dark:hover:bg-gray-700/50' ?>">
                        <?= htmlspecialchars($catName) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </nav>
    <?php endif; ?>
</header>

<?php if (!empty($flashMessages)): ?>
<div class="fixed top-20 md:top-24 right-4 z-[100] space-y-2 max-w-sm" id="flash-container">
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
        <div class="toast flex items-center gap-3 px-4 py-3 rounded-xl border shadow-lg <?= $style ?>" role="alert">
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
