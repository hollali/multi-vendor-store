<?php
$session = $session ?? \App\Core\Session::getInstance();
$error = $error ?? $session->getFlash('error', '');
$success = $success ?? $session->getFlash('success', '');
?>
<!DOCTYPE html>
<html lang="en" class="">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= htmlspecialchars($_SESSION['_csrf_token'] ?? '') ?>">
    <title>Forgot Password | Celer Market</title>
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
    <style>* { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gray-50 dark:bg-gray-950 text-gray-800 dark:text-gray-200 antialiased min-h-screen flex items-center justify-center p-4">
    <div class="absolute top-4 left-4">
        <a href="/" class="flex items-center gap-2">
            <span class="flex items-center justify-center w-8 h-8 bg-gradient-to-br from-primary-700 to-primary-500 rounded-lg shadow-md">
                <i class="fas fa-bolt text-white text-sm"></i>
            </span>
            <span class="text-lg font-extrabold bg-gradient-to-r from-primary-700 to-accent-600 bg-clip-text text-transparent">Celer Market</span>
        </a>
    </div>

    <div class="absolute top-4 right-4">
        <button id="dark-mode-toggle-auth" class="p-2 rounded-lg bg-gray-200 dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:bg-gray-300 dark:hover:bg-gray-700 transition" aria-label="Toggle dark mode">
            <i class="fas fa-moon dark:hidden"></i>
            <i class="fas fa-sun hidden dark:inline"></i>
        </button>
    </div>

    <div class="w-full max-w-md">
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-800 p-8">
            <div class="text-center mb-7">
                <div class="w-14 h-14 bg-gradient-to-br from-orange-50 to-orange-100 dark:from-orange-900/30 dark:to-orange-800/20 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-sm">
                    <i class="fas fa-key text-2xl text-accent-600 dark:text-accent-400"></i>
                </div>
                <h1 class="text-2xl font-extrabold text-gray-900 dark:text-white">Forgot Password?</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Enter your email and we'll send you a reset link</p>
            </div>

            <?php if ($error): ?>
                <div class="flex items-center gap-3 px-4 py-3 mb-4 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-lg text-red-700 dark:text-red-300 text-sm">
                    <i class="fas fa-exclamation-circle flex-shrink-0"></i>
                    <span><?= htmlspecialchars(is_array($error) ? implode(', ', $error) : $error) ?></span>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="flex items-center gap-3 px-4 py-3 mb-4 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-lg text-green-700 dark:text-green-300 text-sm">
                    <i class="fas fa-check-circle flex-shrink-0"></i>
                    <span><?= htmlspecialchars(is_array($success) ? implode(', ', $success) : $success) ?></span>
                </div>
            <?php endif; ?>

            <form action="/forgot-password" method="POST" class="space-y-4">
                <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($_SESSION['_csrf_token'] ?? '') ?>">

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Email Address</label>
                    <div class="relative">
                        <i class="fas fa-envelope absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                        <input type="email" id="email" name="email" required placeholder="you@example.com" class="w-full pl-10 pr-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:text-gray-200 placeholder-gray-400 transition">
                    </div>
                </div>

                <button type="submit" class="w-full py-2.5 bg-gradient-to-r from-primary-700 to-primary-600 hover:from-primary-800 hover:to-primary-700 text-white font-medium rounded-lg shadow-md hover:shadow-lg transition-all text-sm">
                    <i class="fas fa-paper-plane mr-2"></i>Send Reset Link
                </button>
            </form>

            <p class="text-center text-sm text-gray-500 dark:text-gray-400 mt-6">
                <a href="/login" class="font-medium text-primary-700 dark:text-primary-400 hover:text-primary-800 dark:hover:text-primary-300 transition">
                    <i class="fas fa-arrow-left mr-1"></i>Back to Login
                </a>
            </p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var stored = localStorage.getItem('celer_dark_mode');
            if (stored === 'true') { document.documentElement.classList.add('dark'); }
            var btn = document.getElementById('dark-mode-toggle-auth');
            if (btn) {
                btn.addEventListener('click', function() {
                    document.documentElement.classList.toggle('dark');
                    localStorage.setItem('celer_dark_mode', document.documentElement.classList.contains('dark'));
                });
            }
        });
    </script>
</body>
</html>
