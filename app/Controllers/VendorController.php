<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Validator;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Store;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Review;
use App\Models\Coupon;
use App\Models\Withdrawal;
use App\Models\Transaction;
use App\Models\Notification;
use App\Services\Shipping as ShippingService;

class VendorController extends Controller
{
    private function getVendorId(): int
    {
        return (int)$this->session->getUserId();
    }

    private function getStore(): ?\stdClass
    {
        return Store::findBy('vendor_id', $this->getVendorId());
    }

    private function generateSlug(string $name): string
    {
        return strtolower(trim(preg_replace('/[^a-z0-9-]+/', '-', $name), '-'));
    }

    private function generateSku(string $name): string
    {
        $prefix = strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $name), 0, 4));
        if (empty($prefix)) $prefix = 'PRD';
        return $prefix . '-' . strtoupper(bin2hex(random_bytes(4)));
    }

    private function handleImageUpload(array $file, string $subdir = 'products'): ?string
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file['type'], $allowedTypes)) {
            return null;
        }

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('img_') . '.' . $ext;
        $uploadDir = __DIR__ . '/../../public/uploads/' . $subdir . '/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $dest = $uploadDir . $filename;
        if (move_uploaded_file($file['tmp_name'], $dest)) {
            return 'uploads/' . $subdir . '/' . $filename;
        }

        return null;
    }

    private function deleteImageFile(string $path): void
    {
        $fullPath = __DIR__ . '/../../public/' . $path;
        if ($path && file_exists($fullPath)) {
            unlink($fullPath);
        }
    }

    public function dashboard(): void
    {
        $vendorId = $this->getVendorId();
        $store = $this->getStore();
        $db = Database::getInstance();

        $totalProducts = (int)($db->fetch(
            "SELECT COUNT(*) as count FROM products WHERE vendor_id = :vid",
            ['vid' => $vendorId]
        )->count ?? 0);

        $activeProducts = (int)($db->fetch(
            "SELECT COUNT(*) as count FROM products WHERE vendor_id = :vid AND is_active = 1 AND is_approved = 1",
            ['vid' => $vendorId]
        )->count ?? 0);

        $totalOrders = (int)($db->fetch(
            "SELECT COUNT(DISTINCT order_id) as count FROM order_items WHERE vendor_id = :vid",
            ['vid' => $vendorId]
        )->count ?? 0);

        $pendingOrders = (int)($db->fetch(
            "SELECT COUNT(DISTINCT order_id) as count FROM order_items WHERE vendor_id = :vid AND status = 'pending'",
            ['vid' => $vendorId]
        )->count ?? 0);

        $totalRevenue = (float)($db->fetch(
            "SELECT COALESCE(SUM(vendor_earnings), 0) as total FROM order_items WHERE vendor_id = :vid AND status = 'delivered'",
            ['vid' => $vendorId]
        )->total ?? 0);

        $recentOrders = $db->fetchAll(
            "SELECT DISTINCT o.id, o.order_number, o.total, o.order_status, o.created_at, o.payment_status
             FROM orders o
             JOIN order_items oi ON o.id = oi.order_id
             WHERE oi.vendor_id = :vid
             ORDER BY o.created_at DESC
             LIMIT 5",
            ['vid' => $vendorId]
        );

        $this->renderView('vendor/dashboard', [
            'store' => $store,
            'totalProducts' => $totalProducts,
            'activeProducts' => $activeProducts,
            'totalOrders' => $totalOrders,
            'pendingOrders' => $pendingOrders,
            'totalRevenue' => $totalRevenue,
            'recentOrders' => $recentOrders,
        ]);
    }

    public function products(): void
    {
        $vendorId = $this->getVendorId();
        $store = $this->getStore();
        $db = Database::getInstance();

        $page = max(1, (int)$this->getParam('page', 1));
        $perPage = 10;
        $offset = ($page - 1) * $perPage;

        $total = (int)($db->fetch(
            "SELECT COUNT(*) as count FROM products WHERE vendor_id = :vid",
            ['vid' => $vendorId]
        )->count ?? 0);

        $lastPage = max(1, (int)ceil($total / $perPage));

        $products = $db->fetchAll(
            "SELECT p.*, c.name as category_name, b.name as brand_name
             FROM products p
             LEFT JOIN categories c ON p.category_id = c.id
             LEFT JOIN brands b ON p.brand_id = b.id
             WHERE p.vendor_id = :vid
             ORDER BY p.id DESC
             LIMIT :lim OFFSET :off",
            ['vid' => $vendorId, 'lim' => $perPage, 'off' => $offset]
        );

        $this->renderView('vendor/products', [
            'store' => $store,
            'products' => $products,
            'currentPage' => $page,
            'perPage' => $perPage,
            'total' => $total,
            'lastPage' => $lastPage,
            'from' => $offset + 1,
            'to' => min($offset + $perPage, $total),
        ]);
    }

    public function createProduct(): void
    {
        $store = $this->getStore();
        if (!$store) {
            $this->redirectWith('/vendor/dashboard', 'Please set up your store first.', 'error');
            return;
        }

        $parentCategories = Category::where('parent_id', 0, 'IS')->orWhere('parent_id', 0)->where('is_active', 1)->orderBy('name', 'ASC')->get();
        if (empty($parentCategories)) {
            $parentCategories = Category::where('parent_id', 0)->orderBy('name', 'ASC')->get();
        }

        $childCategories = [];
        if (!empty($parentCategories)) {
            $ids = array_column($parentCategories, 'id');
            $placeholders = implode(',', $ids);
            $db = Database::getInstance();
            $childCategories = $db->fetchAll(
                "SELECT * FROM categories WHERE parent_id IN ({$placeholders}) AND is_active = 1 ORDER BY name ASC"
            );
            if (empty($childCategories)) {
                $childCategories = $db->fetchAll(
                    "SELECT * FROM categories WHERE parent_id IN ({$placeholders}) ORDER BY name ASC"
                );
            }
        }

        $brands = Brand::where('is_active', 1)->orderBy('name', 'ASC')->get();
        if (empty($brands)) {
            $brands = Brand::all();
        }

        $categories = array_merge($parentCategories, $childCategories);

        $this->renderView('vendor/product-form', [
            'store' => $store,
            'parentCategories' => $parentCategories,
            'childCategories' => $childCategories,
            'categories' => $categories,
            'brands' => $brands,
            'product' => null,
        ]);
    }

    public function storeProduct(): void
    {
        $vendorId = $this->getVendorId();
        $store = $this->getStore();

        if (!$store) {
            $this->redirectWith('/vendor/dashboard', 'Please set up your store first.', 'error');
            return;
        }

        $name = Validator::sanitizeString($this->getParam('name', ''));
        $price = $this->getParam('price', 0);
        $quantity = $this->getParam('quantity', 0);
        $categoryId = (int)$this->getParam('category_id', 0);
        $description = $this->getParam('description', '');

        $this->validator->validate([
            'name' => $name,
            'price' => $price,
            'quantity' => $quantity,
            'category_id' => $categoryId,
            'description' => $description,
        ], [
            'name' => 'required|min:3|max:300',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'category_id' => 'required|integer',
            'description' => 'required|min:10',
        ]);

        if ($this->validator->fails()) {
            $this->session->setFlash('error', implode(', ', $this->validator->getErrors()));
            $this->redirectWith('/vendor/products/create', 'Please fix the errors below.', 'error');
            return;
        }

        $slug = $this->generateSlug($name);
        $db = Database::getInstance();

        $existing = $db->fetch(
            "SELECT id FROM products WHERE slug = :slug LIMIT 1",
            ['slug' => $slug]
        );
        if ($existing) {
            $slug = $slug . '-' . uniqid();
        }

        $productId = Product::create([
            'vendor_id' => $vendorId,
            'store_id' => $store->id,
            'category_id' => $categoryId,
            'brand_id' => (int)$this->getParam('brand_id', 0) ?: null,
            'name' => $name,
            'slug' => $slug,
            'description' => $description,
            'short_description' => Validator::sanitizeString($this->getParam('short_description', '')),
            'base_price' => (float)$price,
            'sale_price' => $this->getParam('sale_price') ? (float)$this->getParam('sale_price') : null,
            'quantity' => (int)$quantity,
            'sku' => $this->generateSku($name),
            'weight' => $this->getParam('weight') ? (float)$this->getParam('weight') : null,
            'is_featured' => (int)(bool)$this->getParam('is_featured', 0),
            'status' => 'draft',
            'is_approved' => 0,
            'is_active' => 0,
        ]);

        if (!$productId) {
            $this->redirectWith('/vendor/products/create', 'Failed to create product.', 'error');
            return;
        }

        if (!empty($_FILES['images']) && is_array($_FILES['images']['name'])) {
            $files = $_FILES['images'];
            $totalFiles = count($files['name']);

            for ($i = 0; $i < $totalFiles; $i++) {
                if ($files['error'][$i] !== UPLOAD_ERR_OK) {
                    continue;
                }

                $file = [
                    'name' => $files['name'][$i],
                    'type' => $files['type'][$i],
                    'tmp_name' => $files['tmp_name'][$i],
                    'error' => $files['error'][$i],
                    'size' => $files['size'][$i],
                ];

                $path = $this->handleImageUpload($file);
                if ($path) {
                    ProductImage::create([
                        'product_id' => $productId,
                        'image' => $path,
                        'is_primary' => $i === 0 ? 1 : 0,
                        'sort_order' => $i,
                    ]);
                }
            }
        }

        $this->redirectWith('/vendor/products', 'Product created successfully.', 'success');
    }

    public function editProduct(int $id): void
    {
        $vendorId = $this->getVendorId();
        $store = $this->getStore();

        $product = Product::find($id);
        if (!$product || (int)$product->vendor_id !== $vendorId) {
            $this->redirectWith('/vendor/products', 'Product not found.', 'error');
            return;
        }

        $parentCategories = Category::where('parent_id', 0, 'IS')->orWhere('parent_id', 0)->orderBy('name', 'ASC')->get();
        if (empty($parentCategories)) {
            $parentCategories = Category::where('parent_id', 0)->orderBy('name', 'ASC')->get();
        }

        $childCategories = [];
        if (!empty($parentCategories)) {
            $ids = array_column($parentCategories, 'id');
            $placeholders = implode(',', $ids);
            $db = Database::getInstance();
            $childCategories = $db->fetchAll(
                "SELECT * FROM categories WHERE parent_id IN ({$placeholders}) AND is_active = 1 ORDER BY name ASC"
            );
            if (empty($childCategories)) {
                $childCategories = $db->fetchAll(
                    "SELECT * FROM categories WHERE parent_id IN ({$placeholders}) ORDER BY name ASC"
                );
            }
        }

        $brands = Brand::where('is_active', 1)->orderBy('name', 'ASC')->get();
        if (empty($brands)) {
            $brands = Brand::all();
        }

        $images = ProductImage::where('product_id', $product->id)->orderBy('sort_order', 'ASC')->get();

        $categories = array_merge($parentCategories, $childCategories);

        $this->renderView('vendor/product-form', [
            'store' => $store,
            'product' => $product,
            'images' => $images,
            'parentCategories' => $parentCategories,
            'childCategories' => $childCategories,
            'categories' => $categories,
            'brands' => $brands,
        ]);
    }

    public function updateProduct(int $id): void
    {
        $vendorId = $this->getVendorId();
        $store = $this->getStore();

        $product = Product::find($id);
        if (!$product || (int)$product->vendor_id !== $vendorId) {
            $this->redirectWith('/vendor/products', 'Product not found.', 'error');
            return;
        }

        $name = Validator::sanitizeString($this->getParam('name', ''));
        $price = $this->getParam('price', 0);
        $quantity = $this->getParam('quantity', 0);
        $categoryId = (int)$this->getParam('category_id', 0);
        $description = $this->getParam('description', '');

        $this->validator->validate([
            'name' => $name,
            'price' => $price,
            'quantity' => $quantity,
            'category_id' => $categoryId,
            'description' => $description,
        ], [
            'name' => 'required|min:3|max:300',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'category_id' => 'required|integer',
            'description' => 'required|min:10',
        ]);

        if ($this->validator->fails()) {
            $this->session->setFlash('error', implode(', ', $this->validator->getErrors()));
            $this->redirectWith('/vendor/products/edit/' . $id, 'Please fix the errors below.', 'error');
            return;
        }

        $slug = $this->generateSlug($name);
        $db = Database::getInstance();
        $existing = $db->fetch(
            "SELECT id FROM products WHERE slug = :slug AND id != :id LIMIT 1",
            ['slug' => $slug, 'id' => $id]
        );
        if ($existing) {
            $slug = $slug . '-' . uniqid();
        }

        $sku = $this->getParam('sku', '');
        if (empty($sku)) {
            $sku = !empty($product->sku) ? $product->sku : $this->generateSku($name);
        } else {
            $sku = Validator::sanitizeString($sku);
        }

        Product::update($id, [
            'category_id' => $categoryId,
            'brand_id' => (int)$this->getParam('brand_id', 0) ?: null,
            'name' => $name,
            'slug' => $slug,
            'description' => $description,
            'short_description' => Validator::sanitizeString($this->getParam('short_description', '')),
            'base_price' => (float)$price,
            'sale_price' => $this->getParam('sale_price') ? (float)$this->getParam('sale_price') : null,
            'quantity' => (int)$quantity,
            'sku' => $sku,
            'weight' => $this->getParam('weight') ? (float)$this->getParam('weight') : null,
            'is_featured' => (int)(bool)$this->getParam('is_featured', 0),
        ]);

        if (!empty($_FILES['images']) && is_array($_FILES['images']['name'])) {
            $files = $_FILES['images'];
            $totalFiles = count($files['name']);

            $maxSort = 0;
            $existingImages = ProductImage::where('product_id', $id)->orderBy('sort_order', 'DESC')->get();
            if (!empty($existingImages)) {
                $maxSort = (int)$existingImages[0]->sort_order;
            }

            for ($i = 0; $i < $totalFiles; $i++) {
                if ($files['error'][$i] !== UPLOAD_ERR_OK) {
                    continue;
                }

                $file = [
                    'name' => $files['name'][$i],
                    'type' => $files['type'][$i],
                    'tmp_name' => $files['tmp_name'][$i],
                    'error' => $files['error'][$i],
                    'size' => $files['size'][$i],
                ];

                $path = $this->handleImageUpload($file);
                if ($path) {
                    ProductImage::create([
                        'product_id' => $id,
                        'image' => $path,
                        'is_primary' => empty($existingImages) && $i === 0 ? 1 : 0,
                        'sort_order' => $maxSort + $i + 1,
                    ]);
                }
            }
        }

        $this->redirectWith('/vendor/products', 'Product updated successfully.', 'success');
    }

    public function deleteProduct(int $id): void
    {
        $vendorId = $this->getVendorId();

        $product = Product::find($id);
        if (!$product || (int)$product->vendor_id !== $vendorId) {
            if ($this->isPost() && $this->getParam('ajax')) {
                $this->renderJSON(['success' => false, 'message' => 'Product not found.']);
            }
            $this->redirectWith('/vendor/products', 'Product not found.', 'error');
            return;
        }

        $images = ProductImage::where('product_id', $id)->get();
        foreach ($images as $img) {
            $this->deleteImageFile($img->image);
        }

        $db = Database::getInstance();
        $db->delete('product_images', 'product_id = :pid', ['pid' => $id]);
        Product::delete($id);

        if ($this->getParam('ajax')) {
            $this->renderJSON(['success' => true, 'message' => 'Product deleted.']);
        }

        $this->redirectWith('/vendor/products', 'Product deleted successfully.', 'success');
    }

    public function orders(): void
    {
        $vendorId = $this->getVendorId();
        $store = $this->getStore();
        $db = Database::getInstance();

        $page = max(1, (int)$this->getParam('page', 1));
        $perPage = 10;
        $offset = ($page - 1) * $perPage;

        $total = (int)($db->fetch(
            "SELECT COUNT(DISTINCT oi.order_id) as count FROM order_items oi WHERE oi.vendor_id = :vid",
            ['vid' => $vendorId]
        )->count ?? 0);

        $lastPage = max(1, (int)ceil($total / $perPage));

        $orders = $db->fetchAll(
            "SELECT DISTINCT o.*,
                    (SELECT COUNT(*) FROM order_items oi2 WHERE oi2.order_id = o.id AND oi2.vendor_id = :vid2) as vendor_item_count,
                    (SELECT SUM(oi3.total_price) FROM order_items oi3 WHERE oi3.order_id = o.id AND oi3.vendor_id = :vid3) as vendor_subtotal
             FROM orders o
             JOIN order_items oi ON o.id = oi.order_id
             WHERE oi.vendor_id = :vid1
             ORDER BY o.created_at DESC
             LIMIT :lim OFFSET :off",
            ['vid1' => $vendorId, 'vid2' => $vendorId, 'vid3' => $vendorId, 'lim' => $perPage, 'off' => $offset]
        );

        $this->renderView('vendor/orders', [
            'store' => $store,
            'orders' => $orders,
            'currentPage' => $page,
            'perPage' => $perPage,
            'total' => $total,
            'lastPage' => $lastPage,
            'from' => $offset + 1,
            'to' => min($offset + $perPage, $total),
        ]);
    }

    public function orderDetail(int $id): void
    {
        $vendorId = $this->getVendorId();
        $store = $this->getStore();
        $db = Database::getInstance();

        $order = Order::find($id);
        if (!$order) {
            $this->redirectWith('/vendor/orders', 'Order not found.', 'error');
            return;
        }

        $items = $db->fetchAll(
            "SELECT oi.*, p.slug as product_slug
             FROM order_items oi
             LEFT JOIN products p ON oi.product_id = p.id
             WHERE oi.order_id = :oid AND oi.vendor_id = :vid
             ORDER BY oi.id ASC",
            ['oid' => $id, 'vid' => $vendorId]
        );

        if (empty($items)) {
            $this->redirectWith('/vendor/orders', 'Order not found.', 'error');
            return;
        }

        $customer = $db->fetch(
            "SELECT id, first_name, last_name, email, phone FROM users WHERE id = :uid",
            ['uid' => $order->user_id]
        );

        $this->renderView('vendor/order-detail', [
            'store' => $store,
            'order' => $order,
            'items' => $items,
            'customer' => $customer,
        ]);
    }

    public function updateOrderStatus(int $id): void
    {
        $vendorId = $this->getVendorId();
        $db = Database::getInstance();

        $status = $this->getParam('status', '');
        $allowed = ['processing', 'shipped', 'delivered', 'cancelled'];

        if (!in_array($status, $allowed)) {
            $this->redirectWith('/vendor/orders/' . $id, 'Invalid status.', 'error');
            return;
        }

        $items = $db->fetchAll(
            "SELECT id FROM order_items WHERE order_id = :oid AND vendor_id = :vid",
            ['oid' => $id, 'vid' => $vendorId]
        );

        if (empty($items)) {
            $this->redirectWith('/vendor/orders', 'Order not found.', 'error');
            return;
        }

        $db->query(
            "UPDATE order_items SET status = :status WHERE order_id = :oid AND vendor_id = :vid",
            ['status' => $status, 'oid' => $id, 'vid' => $vendorId]
        );

        if ($status === 'delivered') {
            $allDelivered = $db->fetch(
                "SELECT COUNT(*) = 0 as done FROM order_items
                 WHERE order_id = :oid AND vendor_id = :vid AND status != 'delivered'",
                ['oid' => $id, 'vid' => $vendorId]
            );

            if ($allDelivered && $allDelivered->done) {
                $otherPending = $db->fetch(
                    "SELECT COUNT(*) as count FROM order_items
                     WHERE order_id = :oid AND vendor_id != :vid AND status NOT IN ('delivered', 'cancelled')",
                    ['oid' => $id, 'vid' => $vendorId]
                );

                if (!$otherPending || (int)$otherPending->count === 0) {
                    $db->query(
                        "UPDATE orders SET order_status = :status, delivered_at = NOW() WHERE id = :oid",
                        ['status' => 'delivered', 'oid' => $id]
                    );
                }
            }
        }

        $this->redirectWith('/vendor/orders/' . $id, 'Order status updated successfully.', 'success');
    }

    public function reviews(): void
    {
        $vendorId = $this->getVendorId();
        $store = $this->getStore();
        $db = Database::getInstance();

        $page = max(1, (int)$this->getParam('page', 1));
        $perPage = 10;
        $offset = ($page - 1) * $perPage;

        $total = (int)($db->fetch(
            "SELECT COUNT(*) as count FROM reviews r
             JOIN products p ON r.product_id = p.id
             WHERE p.vendor_id = :vid",
            ['vid' => $vendorId]
        )->count ?? 0);

        $lastPage = max(1, (int)ceil($total / $perPage));

        $reviews = $db->fetchAll(
            "SELECT r.*, p.name as product_name, p.slug as product_slug,
                    CONCAT(u.first_name, ' ', u.last_name) as user_name
             FROM reviews r
             JOIN products p ON r.product_id = p.id
             LEFT JOIN users u ON r.user_id = u.id
             WHERE p.vendor_id = :vid
             ORDER BY r.created_at DESC
             LIMIT :lim OFFSET :off",
            ['vid' => $vendorId, 'lim' => $perPage, 'off' => $offset]
        );

        $this->renderView('vendor/reviews', [
            'store' => $store,
            'reviews' => $reviews,
            'currentPage' => $page,
            'perPage' => $perPage,
            'total' => $total,
            'lastPage' => $lastPage,
            'from' => $offset + 1,
            'to' => min($offset + $perPage, $total),
        ]);
    }

    public function coupons(): void
    {
        $vendorId = $this->getVendorId();
        $store = $this->getStore();
        $db = Database::getInstance();

        $coupons = $db->fetchAll(
            "SELECT * FROM coupons WHERE vendor_id = :vid ORDER BY created_at DESC",
            ['vid' => $vendorId]
        );

        $this->renderView('vendor/coupons', [
            'store' => $store,
            'coupons' => $coupons,
        ]);
    }

    public function storeCoupon(): void
    {
        $vendorId = $this->getVendorId();

        $code = strtoupper(Validator::sanitizeString($this->getParam('code', '')));
        $type = $this->getParam('type', 'percentage');
        $value = (float)$this->getParam('value', 0);
        $minAmount = (float)$this->getParam('min_amount', 0);
        $expiresAt = $this->getParam('expires_at', '');

        $this->validator->validate([
            'code' => $code,
            'type' => $type,
            'value' => $value,
            'expires_at' => $expiresAt,
        ], [
            'code' => 'required|min:3|max:50',
            'type' => 'required',
            'value' => 'required|numeric|min:0',
            'expires_at' => 'required',
        ]);

        if ($this->validator->fails()) {
            $this->session->setFlash('error', implode(', ', $this->validator->getErrors()));
            $this->redirectWith('/vendor/coupons', 'Please fix the errors below.', 'error');
            return;
        }

        $existing = Coupon::findByCode($code);
        if ($existing) {
            $this->redirectWith('/vendor/coupons', 'Coupon code already exists.', 'error');
            return;
        }

        Coupon::create([
            'vendor_id' => $vendorId,
            'code' => $code,
            'type' => $type,
            'value' => $value,
            'min_order_amount' => $minAmount,
            'is_active' => 1,
            'expires_at' => $expiresAt,
        ]);

        $this->redirectWith('/vendor/coupons', 'Coupon created successfully.', 'success');
    }

    public function deleteCoupon(int $id): void
    {
        $vendorId = $this->getVendorId();

        $coupon = Coupon::find($id);
        if (!$coupon || (int)$coupon->vendor_id !== $vendorId) {
            $this->renderJSON(['success' => false, 'message' => 'Coupon not found.']);
            return;
        }

        Coupon::delete($id);
        $this->renderJSON(['success' => true, 'message' => 'Coupon deleted.']);
    }

    public function earnings(): void
    {
        $vendorId = $this->getVendorId();
        $store = $this->getStore();
        $db = Database::getInstance();

        $totalEarned = (float)($db->fetch(
            "SELECT COALESCE(SUM(vendor_earnings), 0) as total FROM order_items WHERE vendor_id = :vid AND status = 'delivered'",
            ['vid' => $vendorId]
        )->total ?? 0);

        $pendingEarnings = (float)($db->fetch(
            "SELECT COALESCE(SUM(vendor_earnings), 0) as total FROM order_items WHERE vendor_id = :vid AND status NOT IN ('delivered', 'cancelled')",
            ['vid' => $vendorId]
        )->total ?? 0);

        $withdrawnTotal = (float)($db->fetch(
            "SELECT COALESCE(SUM(amount), 0) as total FROM withdrawals WHERE vendor_id = :vid AND status IN ('completed', 'processing')",
            ['vid' => $vendorId]
        )->total ?? 0);

        $availableBalance = $totalEarned - $withdrawnTotal;

        $transactions = $db->fetchAll(
            "SELECT * FROM transactions WHERE user_id = :uid AND type IN ('commission', 'withdrawal', 'payout')
             ORDER BY created_at DESC LIMIT 20",
            ['uid' => $vendorId]
        );

        $this->renderView('vendor/earnings', [
            'store' => $store,
            'totalEarned' => $totalEarned,
            'pendingEarnings' => $pendingEarnings,
            'availableBalance' => $availableBalance,
            'withdrawnTotal' => $withdrawnTotal,
            'transactions' => $transactions,
        ]);
    }

    public function withdrawals(): void
    {
        $vendorId = $this->getVendorId();
        $store = $this->getStore();
        $db = Database::getInstance();

        $withdrawals = $db->fetchAll(
            "SELECT * FROM withdrawals WHERE vendor_id = :vid ORDER BY created_at DESC",
            ['vid' => $vendorId]
        );

        $this->renderView('vendor/withdrawals', [
            'store' => $store,
            'withdrawals' => $withdrawals,
        ]);
    }

    public function requestWithdrawal(): void
    {
        $vendorId = $this->getVendorId();
        $db = Database::getInstance();

        $settings = $this->getSettings();
        $minWithdrawal = (float)($settings['min_withdrawal'] ?? 5000);

        $totalEarned = (float)($db->fetch(
            "SELECT COALESCE(SUM(vendor_earnings), 0) as total FROM order_items WHERE vendor_id = :vid AND status = 'delivered'",
            ['vid' => $vendorId]
        )->total ?? 0);

        $withdrawnTotal = (float)($db->fetch(
            "SELECT COALESCE(SUM(amount), 0) as total FROM withdrawals WHERE vendor_id = :vid AND status IN ('completed', 'processing')",
            ['vid' => $vendorId]
        )->total ?? 0);

        $availableBalance = $totalEarned - $withdrawnTotal;

        if ($availableBalance < $minWithdrawal) {
            $this->redirectWith('/vendor/withdrawals', "Minimum withdrawal amount is {$this->formatPrice($minWithdrawal)}. Your available balance is {$this->formatPrice($availableBalance)}.", 'error');
            return;
        }

        $amount = (float)$this->getParam('amount', 0);

        if ($amount <= 0 || $amount > $availableBalance) {
            $this->redirectWith('/vendor/withdrawals', 'Invalid withdrawal amount.', 'error');
            return;
        }

        if ($amount < $minWithdrawal) {
            $this->redirectWith('/vendor/withdrawals', "Minimum withdrawal amount is {$this->formatPrice($minWithdrawal)}.", 'error');
            return;
        }

        $bankName = Validator::sanitizeString($this->getParam('bank_name', ''));
        $accountNumber = Validator::sanitizeString($this->getParam('account_number', ''));
        $accountName = Validator::sanitizeString($this->getParam('account_name', ''));

        $this->validator->validate([
            'bank_name' => $bankName,
            'account_number' => $accountNumber,
            'account_name' => $accountName,
        ], [
            'bank_name' => 'required',
            'account_number' => 'required',
            'account_name' => 'required',
        ]);

        if ($this->validator->fails()) {
            $this->session->setFlash('error', implode(', ', $this->validator->getErrors()));
            $this->redirectWith('/vendor/withdrawals', 'Please fill in bank details.', 'error');
            return;
        }

        $fee = 0;
        $netAmount = $amount - $fee;

        Withdrawal::create([
            'vendor_id' => $vendorId,
            'amount' => $amount,
            'fee' => $fee,
            'net_amount' => $netAmount,
            'bank_name' => $bankName,
            'account_number' => $accountNumber,
            'account_name' => $accountName,
            'status' => 'pending',
        ]);

        $this->redirectWith('/vendor/withdrawals', 'Withdrawal request submitted successfully.', 'success');
    }

    public function storeSettings(): void
    {
        $vendorId = $this->getVendorId();
        $store = $this->getStore();

        $this->renderView('vendor/store-settings', [
            'store' => $store,
        ]);
    }

    public function updateStore(): void
    {
        $vendorId = $this->getVendorId();
        $store = $this->getStore();

        $name = Validator::sanitizeString($this->getParam('name', ''));
        $description = $this->getParam('description', '');
        $email = Validator::sanitizeEmail($this->getParam('email', ''));
        $phone = Validator::sanitizeString($this->getParam('phone', ''));
        $address = Validator::sanitizeString($this->getParam('address', ''));
        $city = Validator::sanitizeString($this->getParam('city', ''));
        $state = Validator::sanitizeString($this->getParam('state', ''));

        $this->validator->validate([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
        ], [
            'name' => 'required|min:2|max:200',
            'email' => 'required|email',
            'phone' => 'required',
        ]);

        if ($this->validator->fails()) {
            $this->redirectWith('/vendor/store-settings', implode(', ', $this->validator->getErrors()), 'error');
            return;
        }

        $storeData = [
            'store_name' => $name,
            'description' => $description,
            'email' => $email,
            'phone' => $phone,
            'address' => $address,
            'city' => $city,
            'state' => $state,
        ];

        if (!empty($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            $logoPath = $this->handleImageUpload($_FILES['logo'], 'stores');
            if ($logoPath) {
                if ($store && !empty($store->logo)) $this->deleteImageFile($store->logo);
                $storeData['logo'] = $logoPath;
            }
        }

        if (!empty($_FILES['banner']) && $_FILES['banner']['error'] === UPLOAD_ERR_OK) {
            $bannerPath = $this->handleImageUpload($_FILES['banner'], 'stores');
            if ($bannerPath) {
                if ($store && !empty($store->banner)) $this->deleteImageFile($store->banner);
                $storeData['banner'] = $bannerPath;
            }
        }

        $db = Database::getInstance();

        if ($store) {
            $sets = '';
            $params = [];
            foreach ($storeData as $col => $val) {
                $sets .= "{$col} = :{$col}, ";
                $params[$col] = $val;
            }
            $sets = rtrim($sets, ', ');
            $params['id'] = $store->id;
            $db->query("UPDATE stores SET {$sets} WHERE id = :id", $params);
        } else {
            $storeData['vendor_id'] = $vendorId;
            $storeData['slug'] = $this->generateSlug($name);
            $db->insert('stores', $storeData);
        }

        $this->redirectWith('/vendor/store-settings', 'Store settings updated successfully.', 'success');
    }

    public function notifications(): void
    {
        $vendorId = $this->getVendorId();
        $store = $this->getStore();
        $db = Database::getInstance();

        $notifications = $db->fetchAll(
            "SELECT * FROM notifications WHERE user_id = :uid ORDER BY created_at DESC",
            ['uid' => $vendorId]
        );

        $this->renderView('vendor/notifications', [
            'store' => $store,
            'notifications' => $notifications,
        ]);
    }

    public function shippingRates(): void
    {
        $vendorId = $this->getVendorId();
        $store = $this->getStore();

        $shippingService = ShippingService::getInstance();
        $zones = $shippingService->getShippingZones();
        $myRates = $shippingService->getVendorShippingRates($vendorId);

        $this->renderView('vendor/shipping', [
            'store' => $store,
            'zones' => $zones,
            'myRates' => $myRates,
        ]);
    }

    public function saveShippingRate(): void
    {
        $vendorId = $this->getVendorId();
        $store = $this->getStore();

        $zoneId = (int)$this->getParam('zone_id', 0);
        $baseRate = (float)$this->getParam('base_rate', 0);
        $ratePerKg = (float)$this->getParam('rate_per_kg', 0);
        $freeShippingMin = $this->getParam('free_shipping_min', '');
        $estMin = (int)$this->getParam('estimated_days_min', 3);
        $estMax = (int)$this->getParam('estimated_days_max', 7);
        $isActive = (int)$this->getParam('is_active', 1);

        $shippingService = ShippingService::getInstance();
        $shippingService->saveVendorShippingRate($vendorId, $zoneId, [
            'base_rate' => $baseRate,
            'rate_per_kg' => $ratePerKg,
            'free_shipping_min' => $freeShippingMin,
            'estimated_days_min' => $estMin,
            'estimated_days_max' => $estMax,
            'is_active' => $isActive,
        ]);

        $this->redirectWith('/vendor/shipping', 'Shipping rate saved successfully.', 'success');
    }
}
