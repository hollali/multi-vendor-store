<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php $activeMenu = 'reviews'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="flex-1 p-4 md:p-6 bg-gray-50 dark:bg-gray-900 min-h-screen">
    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">Customer Reviews</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">See what customers are saying about your products.</p>
        </div>

        <?php $reviews = $reviews ?? []; ?>
        <?php if (empty($reviews)): ?>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-12 text-center">
                <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-yellow-50 dark:bg-yellow-900/20 flex items-center justify-center">
                    <i class="fas fa-star text-yellow-400 text-3xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">No reviews yet</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Reviews from customers will show up here.</p>
            </div>
        <?php else: ?>
            <div class="space-y-4">
                <?php foreach ($reviews as $review): ?>
                    <?php
                    $rating = $review->rating ?? $review['rating'] ?? 5;
                    $productName = $review->product_name ?? $review['product_name'] ?? $review->product->name ?? $review['product']['name'] ?? '';
                    $customerName = $review->customer_name ?? $review['customer_name'] ?? $review->user->name ?? $review['user']['name'] ?? 'Anonymous';
                    ?>
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 hover:shadow-md transition">
                        <div class="flex items-start justify-between mb-3">
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400"><?= htmlspecialchars($productName) ?></p>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-sm font-semibold text-gray-900 dark:text-white"><?= htmlspecialchars($customerName) ?></span>
                                    <div class="flex items-center gap-0.5">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="fas fa-star <?= $i <= $rating ? 'text-yellow-400' : 'text-gray-200 dark:text-gray-600' ?> text-xs"></i>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                            </div>
                            <span class="text-xs text-gray-400 dark:text-gray-500 whitespace-nowrap"><?= htmlspecialchars(date('M d, Y', strtotime($review->created_at ?? $review['created_at'] ?? ''))) ?></span>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed"><?= htmlspecialchars($review->comment ?? $review['comment'] ?? $review->review ?? $review['review'] ?? '') ?></p>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if (isset($totalPages) && $totalPages > 1): ?>
                <div class="flex items-center justify-between mt-6">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Page <?= $currentPage ?? 1 ?> of <?= $totalPages ?></p>
                    <div class="flex gap-2">
                        <?php if (($currentPage ?? 1) > 1): ?>
                            <a href="?page=<?= ($currentPage ?? 1) - 1 ?>" class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition"><i class="fas fa-chevron-left"></i> Previous</a>
                        <?php endif; ?>
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <a href="?page=<?= $i ?>" class="px-4 py-2 rounded-lg text-sm font-medium transition <?= ($currentPage ?? 1) == $i ? 'bg-primary-700 text-white' : 'bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' ?>"><?= $i ?></a>
                        <?php endfor; ?>
                        <?php if (($currentPage ?? 1) < $totalPages): ?>
                            <a href="?page=<?= ($currentPage ?? 1) + 1 ?>" class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition">Next <i class="fas fa-chevron-right"></i></a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
