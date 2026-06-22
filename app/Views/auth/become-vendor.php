<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="min-h-screen bg-gray-50 dark:bg-gray-900 flex items-center justify-center px-4 py-12">
    <div class="max-w-lg w-full">
        <div class="text-center mb-8">
            <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-primary-50 dark:bg-primary-900/20 flex items-center justify-center">
                <i class="fas fa-store text-3xl text-primary-600 dark:text-primary-400"></i>
            </div>
            <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">Become a Vendor</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Start selling your products to thousands of customers.</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 lg:p-8">
            <div class="space-y-4 mb-6">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <i class="fas fa-store text-blue-600 dark:text-blue-400 text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Your Own Storefront</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Get a dedicated store page to showcase your products with your branding.</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-lg bg-green-50 dark:bg-green-900/20 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <i class="fas fa-chart-line text-green-600 dark:text-green-400 text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Earn Revenue</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Set your prices and earn money from every sale. Withdraw your earnings anytime.</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-lg bg-purple-50 dark:bg-purple-900/20 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <i class="fas fa-tags text-purple-600 dark:text-purple-400 text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Manage Products & Orders</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Full dashboard to manage inventory, process orders, and create promotions.</p>
                    </div>
                </div>
            </div>

            <form action="/become-vendor" method="POST">
                <?= $csrf_field() ?>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-4 leading-relaxed">
                    By upgrading, you agree to our <a href="/terms" class="text-primary-600 hover:underline">Vendor Terms of Service</a>.
                    Your existing account will be upgraded to a vendor account and a store will be created for you.
                </p>
                <button type="submit" class="w-full py-3 bg-primary-700 hover:bg-primary-800 text-white font-semibold rounded-lg transition shadow-md hover:shadow-lg text-sm">
                    <i class="fas fa-rocket mr-2"></i> Upgrade to Vendor
                </button>
                <a href="/dashboard" class="block w-full text-center text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 mt-3">Cancel</a>
            </form>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>