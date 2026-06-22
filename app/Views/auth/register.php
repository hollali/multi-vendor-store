<?php
$session = $session ?? \App\Core\Session::getInstance();
$user = $user ?? $session->getUser();
$error = $error ?? $session->getFlash('error', '');
$success = $success ?? $session->getFlash('success', '');
$old = $_POST ?? [];
$selectedRole = $old['role'] ?? 'customer';
?>
<!DOCTYPE html>
<html lang="en" class="">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= htmlspecialchars($_SESSION['_csrf_token'] ?? '') ?>">
    <title>Create Account | Celer Market</title>
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
            <img src="/uploads/logos/logo1.png" alt="Celer Market" class="h-8 w-auto">
            <span class="text-lg font-extrabold text-primary-700 dark:text-primary-400">Celer Market</span>
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
                <div class="w-14 h-14 bg-primary-50 dark:bg-primary-900/30 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-sm">
                    <i class="fas fa-user-plus text-2xl text-primary-700 dark:text-primary-400"></i>
                </div>
                <h1 class="text-2xl font-extrabold text-gray-900 dark:text-white">Create Account</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Join Celer Market today</p>
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

            <form action="/register" method="POST" class="space-y-4">
                <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($_SESSION['_csrf_token'] ?? '') ?>">

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">First Name</label>
                        <div class="relative">
                            <i class="fas fa-user absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                            <input type="text" id="first_name" name="first_name" value="<?= htmlspecialchars($old['first_name'] ?? '') ?>" required placeholder="John" class="w-full pl-9 pr-3 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:text-gray-200 placeholder-gray-400 transition">
                        </div>
                    </div>
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Last Name</label>
                        <div class="relative">
                            <i class="fas fa-user absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                            <input type="text" id="last_name" name="last_name" value="<?= htmlspecialchars($old['last_name'] ?? '') ?>" required placeholder="Doe" class="w-full pl-9 pr-3 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:text-gray-200 placeholder-gray-400 transition">
                        </div>
                    </div>
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Email Address</label>
                    <div class="relative">
                        <i class="fas fa-envelope absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                        <input type="email" id="email" name="email" value="<?= htmlspecialchars($old['email'] ?? '') ?>" required placeholder="you@example.com" class="w-full pl-9 pr-3 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:text-gray-200 placeholder-gray-400 transition">
                    </div>
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Phone Number</label>
                    <div class="relative">
                        <i class="fas fa-phone absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                        <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($old['phone'] ?? '') ?>" required placeholder="+233 XX XXX XXXX" class="w-full pl-9 pr-3 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:text-gray-200 placeholder-gray-400 transition">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Password</label>
                    <div class="relative">
                        <i class="fas fa-lock absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                        <input type="password" id="password" name="password" required placeholder="&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;" minlength="8" class="w-full pl-9 pr-10 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:text-gray-200 placeholder-gray-400 transition">
                        <button type="button" onclick="togglePassword('password', 'togglePasswordIcon')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition" aria-label="Toggle password visibility">
                            <i class="far fa-eye" id="togglePasswordIcon"></i>
                        </button>
                    </div>
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Confirm Password</label>
                    <div class="relative">
                        <i class="fas fa-lock absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                        <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;" minlength="8" class="w-full pl-9 pr-10 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:text-gray-200 placeholder-gray-400 transition">
                        <button type="button" onclick="togglePassword('password_confirmation', 'toggleConfirmIcon')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition" aria-label="Toggle password visibility">
                            <i class="far fa-eye" id="toggleConfirmIcon"></i>
                        </button>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">I want to</label>
                    <div class="flex gap-2 p-1 bg-gray-100 dark:bg-gray-800 rounded-lg">
                        <button type="button" id="role-customer" class="flex-1 py-2 px-4 text-sm font-medium rounded-md transition-all <?= $selectedRole === 'customer' ? 'bg-white dark:bg-gray-700 text-primary-700 dark:text-primary-400 shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300' ?>" onclick="selectRole('customer')">
                            <i class="fas fa-user mr-1.5"></i>Buy
                        </button>
                        <button type="button" id="role-vendor" class="flex-1 py-2 px-4 text-sm font-medium rounded-md transition-all <?= $selectedRole === 'vendor' ? 'bg-white dark:bg-gray-700 text-primary-700 dark:text-primary-400 shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300' ?>" onclick="selectRole('vendor')">
                            <i class="fas fa-store mr-1.5"></i>Sell
                        </button>
                    </div>
                    <input type="hidden" name="role" id="role-input" value="<?= $selectedRole ?>">
                </div>

                <label class="flex items-start gap-2 cursor-pointer">
                    <input type="checkbox" name="terms" required class="w-4 h-4 mt-0.5 rounded border-gray-300 dark:border-gray-600 text-primary-700 focus:ring-primary-500 cursor-pointer">
                    <span class="text-sm text-gray-600 dark:text-gray-400">I agree to the <a href="/terms" class="font-medium text-primary-700 dark:text-primary-400 hover:underline">Terms of Service</a> and <a href="/privacy" class="font-medium text-primary-700 dark:text-primary-400 hover:underline">Privacy Policy</a></span>
                </label>

                <button type="submit" class="w-full py-2.5 bg-primary-700 hover:bg-primary-800 text-white font-medium rounded-lg shadow-md hover:shadow-lg transition-all text-sm">
                    Create Account
                </button>
            </form>

            <p class="text-center text-sm text-gray-500 dark:text-gray-400 mt-6">
                Already have an account?
                <a href="/login" class="font-medium text-primary-700 dark:text-primary-400 hover:text-primary-800 dark:hover:text-primary-300 transition">Sign in</a>
            </p>
        </div>
    </div>

    <script>
        function selectRole(role) {
            document.getElementById('role-input').value = role;
            var cust = document.getElementById('role-customer');
            var vend = document.getElementById('role-vendor');
            if (role === 'customer') {
                cust.className = 'flex-1 py-2 px-4 text-sm font-medium rounded-md transition-all bg-white dark:bg-gray-700 text-primary-700 dark:text-primary-400 shadow-sm';
                vend.className = 'flex-1 py-2 px-4 text-sm font-medium rounded-md transition-all text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300';
            } else {
                vend.className = 'flex-1 py-2 px-4 text-sm font-medium rounded-md transition-all bg-white dark:bg-gray-700 text-primary-700 dark:text-primary-400 shadow-sm';
                cust.className = 'flex-1 py-2 px-4 text-sm font-medium rounded-md transition-all text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300';
            }
        }

        function togglePassword(id, iconId) {
            var pwd = document.getElementById(id);
            var icon = document.getElementById(iconId);
            if (pwd.type === 'password') {
                pwd.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                pwd.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

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
