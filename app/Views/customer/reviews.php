<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php $activeMenu = 'reviews'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="flex-1 p-4 md:p-6 bg-gray-50 dark:bg-gray-900 min-h-screen">
    <div class="max-w-4xl mx-auto">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
            <div>
                <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">My Reviews</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1"><?= $reviewsCount ?? count($reviews ?? []) ?> review(s)</p>
            </div>
        </div>

        <?php $reviews = $reviews ?? []; ?>
        <?php if (empty($reviews)): ?>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-12 text-center">
                <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-yellow-50 dark:bg-yellow-900/20 flex items-center justify-center">
                    <i class="far fa-star text-yellow-400 text-3xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">No reviews yet</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Share your thoughts on purchased products.</p>
                <a href="/orders" class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary-700 hover:bg-primary-800 text-white text-sm font-medium rounded-lg transition shadow-sm"><i class="fas fa-box"></i> View Orders</a>
            </div>
        <?php else: ?>
            <div class="space-y-4">
                <?php foreach ($reviews as $review): ?>
                    <?php
                    $rating = (int)($review->rating ?? $review['rating'] ?? 5);
                    ?>
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5">
                        <div class="flex items-start gap-4">
                            <div class="w-14 h-14 rounded-lg bg-gray-100 dark:bg-gray-700 flex-shrink-0 overflow-hidden">
                                <img src="<?= htmlspecialchars($review->product_image ?? $review['product_image'] ?? '/assets/img/placeholder.png') ?>" alt="<?= htmlspecialchars($review->product_name ?? $review['product_name'] ?? 'Product') ?>" class="w-full h-full object-cover">
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-2">
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white"><?= htmlspecialchars($review->product_name ?? $review['product_name'] ?? 'Product') ?></p>
                                        <div class="flex items-center gap-0.5 mt-1">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="fas fa-star text-xs <?= $i <= $rating ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' ?>"></i>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                    <span class="text-xs text-gray-400 dark:text-gray-500 flex-shrink-0"><?= htmlspecialchars(date('M d, Y', strtotime($review->created_at ?? $review['created_at'] ?? ''))) ?></span>
                                </div>
                                <?php $title = $review->title ?? $review['title'] ?? ''; ?>
                                <?php if ($title): ?>
                                    <p class="text-sm font-medium text-gray-800 dark:text-gray-200 mt-2"><?= htmlspecialchars($title) ?></p>
                                <?php endif; ?>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1"><?= htmlspecialchars($review->text ?? $review['text'] ?? $review->review ?? $review['review'] ?? '') ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
