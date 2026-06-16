<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php $activeMenu = 'banners'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="flex-1 p-4 md:p-6 bg-gray-50 dark:bg-gray-900 min-h-screen">
    <div class="max-w-7xl mx-auto">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
            <div>
                <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">Homepage Banners</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Manage homepage slider banners.</p>
            </div>
            <button onclick="document.getElementById('addBannerForm').scrollIntoView({behavior: 'smooth'})" class="mt-3 sm:mt-0 inline-flex items-center gap-2 px-5 py-2.5 bg-primary-700 hover:bg-primary-800 text-white text-sm font-medium rounded-lg transition shadow-sm">
                <i class="fas fa-plus-circle"></i> Add Banner
            </button>
        </div>

        <?php $banners = $banners ?? []; ?>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <?php if (empty($banners)): ?>
                <div class="md:col-span-2 bg-white dark:bg-gray-800 rounded-xl shadow-sm p-12 text-center">
                    <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                        <i class="fas fa-image text-gray-400 text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">No banners yet</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Create your first homepage banner.</p>
                </div>
            <?php else: ?>
                <?php foreach ($banners as $banner): ?>
                    <?php
                    $isActive = $banner->active ?? $banner['active'] ?? $banner->is_active ?? $banner['is_active'] ?? true;
                    $image = $banner->image ?? $banner['image'] ?? '';
                    ?>
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden group">
                        <div class="relative h-44 bg-gray-100 dark:bg-gray-700 overflow-hidden">
                            <?php if ($image): ?>
                                <img src="<?= htmlspecialchars($image) ?>" alt="" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                            <?php else: ?>
                                <div class="w-full h-full flex items-center justify-center">
                                    <i class="fas fa-image text-gray-400 text-4xl"></i>
                                </div>
                            <?php endif; ?>
                            <div class="absolute top-3 right-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $isActive ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' ?>"><?= $isActive ? 'Active' : 'Inactive' ?></span>
                            </div>
                        </div>
                        <div class="p-4">
                            <div class="flex items-start justify-between mb-2">
                                <div>
                                    <h3 class="font-semibold text-gray-900 dark:text-white"><?= htmlspecialchars($banner->title ?? $banner['title'] ?? '') ?></h3>
                                    <?php if ($banner->subtitle ?? $banner['subtitle'] ?? ''): ?>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5"><?= htmlspecialchars($banner->subtitle ?? $banner['subtitle'] ?? '') ?></p>
                                    <?php endif; ?>
                                </div>
                                <span class="text-xs text-gray-400 bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded">#<?= $banner->sort_order ?? $banner['sort_order'] ?? 0 ?></span>
                            </div>
                            <?php if ($banner->link ?? $banner['link'] ?? ''): ?>
                                <p class="text-xs text-primary-600 dark:text-primary-400 truncate mb-3"><i class="fas fa-link mr-1"></i><?= htmlspecialchars($banner->link ?? $banner['link'] ?? '') ?></p>
                            <?php endif; ?>
                            <div class="flex items-center gap-2 pt-2 border-t border-gray-100 dark:border-gray-700">
                                <form action="/admin/banners/<?= htmlspecialchars($banner->id ?? $banner['id'] ?? 0) ?>/toggle" method="POST" class="inline">
                                    <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($_SESSION['_csrf_token'] ?? '') ?>">
                                    <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 <?= $isActive ? 'bg-yellow-50 dark:bg-yellow-900/20 text-yellow-700 dark:text-yellow-400' : 'bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400' ?> text-xs font-medium rounded-lg hover:bg-opacity-80 transition">
                                        <i class="fas <?= $isActive ? 'fa-eye-slash' : 'fa-eye' ?>"></i> <?= $isActive ? 'Deactivate' : 'Activate' ?>
                                    </button>
                                </form>
                                <form action="/admin/banners/<?= htmlspecialchars($banner->id ?? $banner['id'] ?? 0) ?>/delete" method="POST" onsubmit="return confirm('Delete this banner?')" class="inline">
                                    <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($_SESSION['_csrf_token'] ?? '') ?>">
                                    <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 text-xs font-medium rounded-lg hover:bg-red-100 dark:hover:bg-red-900/40 transition"><i class="fas fa-trash"></i> Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div id="addBannerForm" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 lg:p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4"><?= isset($editBanner) ? 'Edit Banner' : 'Add New Banner' ?></h2>
            <form action="/admin/banners/save" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($_SESSION['_csrf_token'] ?? '') ?>">
                <?php if (isset($editBanner)): ?>
                    <input type="hidden" name="id" value="<?= htmlspecialchars($editBanner->id ?? $editBanner['id'] ?? '') ?>">
                <?php endif; ?>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Title</label>
                        <input type="text" name="title" required value="<?= htmlspecialchars($editBanner->title ?? $editBanner['title'] ?? '') ?>" class="w-full px-3.5 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:text-gray-200 placeholder-gray-400" placeholder="Summer Sale">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Subtitle</label>
                        <input type="text" name="subtitle" value="<?= htmlspecialchars($editBanner->subtitle ?? $editBanner['subtitle'] ?? '') ?>" class="w-full px-3.5 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:text-gray-200 placeholder-gray-400" placeholder="Up to 50% off on electronics">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Link URL</label>
                        <input type="url" name="link" value="<?= htmlspecialchars($editBanner->link ?? $editBanner['link'] ?? '') ?>" class="w-full px-3.5 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:text-gray-200 placeholder-gray-400" placeholder="https://example.com/sale">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Sort Order</label>
                        <input type="number" name="sort_order" value="<?= htmlspecialchars($editBanner->sort_order ?? $editBanner['sort_order'] ?? '0') ?>" class="w-full px-3.5 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:text-gray-200 placeholder-gray-400" placeholder="0">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Banner Image</label>
                        <div class="flex items-center gap-4">
                            <?php $editImage = $editBanner->image ?? $editBanner['image'] ?? ''; ?>
                            <?php if ($editImage): ?>
                                <div class="w-24 h-16 rounded-lg bg-gray-100 dark:bg-gray-700 overflow-hidden flex-shrink-0">
                                    <img src="<?= htmlspecialchars($editImage) ?>" alt="" class="w-full h-full object-cover">
                                </div>
                            <?php endif; ?>
                            <input type="file" name="image" accept="image/*" class="flex-1 text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-primary-50 dark:file:bg-primary-900/20 file:text-primary-700 dark:file:text-primary-400 hover:file:bg-primary-100 dark:hover:file:bg-primary-900/40" <?= isset($editBanner) ? '' : 'required' ?>>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex gap-3">
                    <button type="submit" class="px-5 py-2.5 bg-primary-700 hover:bg-primary-800 text-white text-sm font-medium rounded-lg transition shadow-sm">
                        <i class="fas fa-save mr-1.5"></i> <?= isset($editBanner) ? 'Update Banner' : 'Save Banner' ?>
                    </button>
                    <?php if (isset($editBanner)): ?>
                        <a href="/admin/banners" class="px-5 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition">Cancel</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>