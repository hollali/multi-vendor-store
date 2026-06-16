<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php $activeMenu = isset($product) ? 'products' : 'add-product'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="flex-1 p-4 md:p-6 bg-gray-50 dark:bg-gray-900 min-h-screen">
    <div class="max-w-5xl mx-auto">
        <div class="mb-6">
            <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white"><?= isset($product) ? 'Edit Product' : 'Add New Product' ?></h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1"><?= isset($product) ? 'Update your product details below.' : 'Fill in the details to add a new product to your store.' ?></p>
        </div>

        <form action="<?= isset($product) ? '/vendor/products/' . htmlspecialchars($product->id ?? $product['id'] ?? '') . '/update' : '/vendor/products/store' ?>" method="POST" enctype="multipart/form-data" class="space-y-6">
            <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($_SESSION['_csrf_token'] ?? '') ?>">

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 lg:p-6">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Basic Information</h2>
                        <div class="space-y-4">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Product Name <span class="text-red-500">*</span></label>
                                <input type="text" id="name" name="name" value="<?= htmlspecialchars($product->name ?? $product['name'] ?? '') ?>" required
                                       class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-gray-200 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition"
                                       placeholder="e.g. Premium Wireless Headphones">
                            </div>
                            <div>
                                <label for="slug" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Slug</label>
                                <input type="text" id="slug" name="slug" value="<?= htmlspecialchars($product->slug ?? $product['slug'] ?? '') ?>"
                                       class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-gray-200 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition"
                                       placeholder="auto-generated from name">
                            </div>
                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Description <span class="text-red-500">*</span></label>
                                <textarea id="description" name="description" rows="6" required
                                          class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-gray-200 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition resize-y"
                                          placeholder="Detailed description of your product..."><?= htmlspecialchars($product->description ?? $product['description'] ?? '') ?></textarea>
                            </div>
                            <div>
                                <label for="short_description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Short Description</label>
                                <textarea id="short_description" name="short_description" rows="3"
                                          class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-gray-200 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition resize-y"
                                          placeholder="Brief summary for product listings..."><?= htmlspecialchars($product->short_description ?? $product['short_description'] ?? '') ?></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 lg:p-6">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Variants</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Add product variants like size, color, etc. with optional price adjustments.</p>
                        <div id="variants-container">
                            <?php $variants = $variants ?? $product->variants ?? []; ?>
                            <?php if (!empty($variants)): ?>
                                <?php foreach ($variants as $i => $variant): ?>
                                    <div class="variant-group border border-gray-200 dark:border-gray-700 rounded-lg p-4 mb-3 relative">
                                        <button type="button" class="absolute top-3 right-3 text-red-500 hover:text-red-700 dark:hover:text-red-400 text-sm" onclick="this.closest('.variant-group').remove()"><i class="fas fa-times"></i></button>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                            <div>
                                                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Variant Name</label>
                                                <input type="text" name="variants[<?= $i ?>][name]" value="<?= htmlspecialchars($variant->name ?? $variant['name'] ?? '') ?>" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="e.g. Size">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Values (comma separated)</label>
                                                <input type="text" name="variants[<?= $i ?>][values]" value="<?= htmlspecialchars(implode(', ', array_map(function($v) { return $v->value ?? $v['value'] ?? $v; }, is_array($variant->values ?? $variant['values'] ?? []) ? ($variant->values ?? $variant['values'] ?? []) : []))) ?>" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="e.g. Small, Medium, Large">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Price Adjustment (GH₵)</label>
                                                <input type="number" step="0.01" name="variants[<?= $i ?>][price_adjustment]" value="<?= htmlspecialchars($variant->price_adjustment ?? $variant['price_adjustment'] ?? 0) ?>" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="0.00">
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <button type="button" id="add-variant" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition"><i class="fas fa-plus"></i> Add Variant</button>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 lg:p-6">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Images</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Upload product images. First image will be used as the cover.</p>
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 mb-4" id="image-preview-container">
                            <?php $images = $images ?? $product->images ?? []; ?>
                            <?php if (!empty($images)): ?>
                                <?php foreach ($images as $img): ?>
                                    <?php $url = $img->url ?? $img['url'] ?? $img->image ?? $img['image'] ?? ''; ?>
                                    <?php if ($url): ?>
                                        <div class="relative group aspect-square rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-700 border border-gray-200 dark:border-gray-700">
                                            <img src="<?= htmlspecialchars($url) ?>" alt="" class="w-full h-full object-cover">
                                            <button type="button" class="absolute top-1 right-1 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center text-xs opacity-0 group-hover:opacity-100 transition" onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <label class="flex flex-col items-center justify-center w-full p-6 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:border-primary-500 dark:hover:border-primary-500 bg-gray-50 dark:bg-gray-900/50 transition">
                            <i class="fas fa-cloud-upload-alt text-2xl text-gray-400 mb-2"></i>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Click to upload images</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">PNG, JPG, WebP up to 5MB</p>
                            <input type="file" id="images" name="images[]" multiple accept="image/*" class="hidden" onchange="previewImages(event)">
                        </label>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 lg:p-6">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Organization</h2>
                        <div class="space-y-4">
                            <div>
                                <label for="category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Category <span class="text-red-500">*</span></label>
                                <select id="category_id" name="category_id" required
                                        class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition">
                                    <option value="">Select Category</option>
                                    <?php foreach ($categories ?? [] as $cat): ?>
                                        <option value="<?= $cat->id ?? $cat['id'] ?? '' ?>" <?= (($product->category_id ?? $product['category_id'] ?? '') == ($cat->id ?? $cat['id'] ?? '')) ? 'selected' : '' ?>><?= htmlspecialchars($cat->name ?? $cat['name'] ?? '') ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div>
                                <label for="brand_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Brand</label>
                                <select id="brand_id" name="brand_id"
                                        class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition">
                                    <option value="">Select Brand</option>
                                    <?php foreach ($brands ?? [] as $brand): ?>
                                        <option value="<?= $brand->id ?? $brand['id'] ?? '' ?>" <?= (($product->brand_id ?? $product['brand_id'] ?? '') == ($brand->id ?? $brand['id'] ?? '')) ? 'selected' : '' ?>><?= htmlspecialchars($brand->name ?? $brand['name'] ?? '') ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 lg:p-6">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Pricing</h2>
                        <div class="space-y-4">
                            <div>
                                <label for="price" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Base Price (GH₵) <span class="text-red-500">*</span></label>
                                <input type="number" step="0.01" min="0" id="price" name="price" value="<?= htmlspecialchars($product->price ?? $product['price'] ?? '') ?>" required
                                       class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-gray-200 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition"
                                       placeholder="0.00">
                            </div>
                            <div>
                                <label for="sale_price" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Sale Price (GH₵)</label>
                                <input type="number" step="0.01" min="0" id="sale_price" name="sale_price" value="<?= htmlspecialchars($product->sale_price ?? $product['sale_price'] ?? '') ?>"
                                       class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-gray-200 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition"
                                       placeholder="0.00">
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 lg:p-6">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Inventory</h2>
                        <div class="space-y-4">
                            <div>
                                <label for="quantity" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Quantity <span class="text-red-500">*</span></label>
                                <input type="number" min="0" id="quantity" name="quantity" value="<?= htmlspecialchars($product->quantity ?? $product['quantity'] ?? $product->stock ?? $product['stock'] ?? 0) ?>" required
                                       class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-gray-200 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition"
                                       placeholder="0">
                            </div>
                            <div>
                                <label for="sku" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">SKU <span class="text-red-500">*</span></label>
                                <input type="text" id="sku" name="sku" value="<?= htmlspecialchars($product->sku ?? $product['sku'] ?? '') ?>" required
                                       class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-gray-200 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition"
                                       placeholder="e.g. WH-001">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 lg:p-6">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="status" value="draft" <?= (!isset($product) || ($product->status ?? $product['status'] ?? 'draft') === 'draft') ? 'checked' : '' ?> class="w-4 h-4 text-primary-700 focus:ring-primary-500">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Save as Draft</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="status" value="pending" <?= (($product->status ?? $product['status'] ?? '') === 'pending') ? 'checked' : '' ?> class="w-4 h-4 text-primary-700 focus:ring-primary-500">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Submit for Approval</span>
                        </label>
                    </div>
                    <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-3 bg-primary-700 hover:bg-primary-800 text-white text-sm font-semibold rounded-lg transition shadow-sm">
                        <i class="fas fa-save"></i> <?= isset($product) ? 'Update Product' : 'Create Product' ?>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function previewImages(event) {
    const container = document.getElementById('image-preview-container');
    const files = event.target.files;
    for (let i = 0; i < files.length; i++) {
        const file = files[i];
        const reader = new FileReader();
        reader.onload = function(e) {
            const div = document.createElement('div');
            div.className = 'relative group aspect-square rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-700 border border-gray-200 dark:border-gray-700';
            div.innerHTML = '<img src="' + e.target.result + '" alt="" class="w-full h-full object-cover">' +
                '<button type="button" class="absolute top-1 right-1 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center text-xs opacity-0 group-hover:opacity-100 transition" onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button>';
            container.appendChild(div);
        };
        reader.readAsDataURL(file);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const nameInput = document.getElementById('name');
    const slugInput = document.getElementById('slug');
    if (nameInput && slugInput && !slugInput.value) {
        nameInput.addEventListener('input', function() {
            slugInput.value = this.value.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
        });
    }

    let variantIndex = <?= !empty($variants) ? count($variants) : 0 ?>;
    document.getElementById('add-variant').addEventListener('click', function() {
        const container = document.getElementById('variants-container');
        const div = document.createElement('div');
        div.className = 'variant-group border border-gray-200 dark:border-gray-700 rounded-lg p-4 mb-3 relative';
        div.innerHTML = '<button type="button" class="absolute top-3 right-3 text-red-500 hover:text-red-700 dark:hover:text-red-400 text-sm" onclick="this.closest(\'.variant-group\').remove()"><i class="fas fa-times"></i></button>' +
            '<div class="grid grid-cols-1 md:grid-cols-2 gap-3">' +
            '<div><label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Variant Name</label><input type="text" name="variants[' + variantIndex + '][name]" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="e.g. Size"></div>' +
            '<div><label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Values (comma separated)</label><input type="text" name="variants[' + variantIndex + '][values]" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="e.g. Small, Medium, Large"></div>' +
            '<div><label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Price Adjustment (GH₵)</label><input type="number" step="0.01" name="variants[' + variantIndex + '][price_adjustment]" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="0.00"></div>' +
            '</div>';
        container.appendChild(div);
        variantIndex++;
    });
});
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
