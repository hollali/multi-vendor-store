<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Validator;
use App\Models\Banner;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Notification;
use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Store;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Withdrawal;

class AdminController extends Controller
{
    private function generateSlug(string $name): string
    {
        return strtolower(trim(preg_replace('/[^a-z0-9-]+/', '-', $name), '-'));
    }

    private function handleImageUpload(array $file, string $subdir = 'banners'): ?string
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file['type'], $allowedTypes)) {
            return null;
        }

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('banner_') . '.' . $ext;
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

    private function paginate(string $baseSql, string $countSql, array $params, int $perPage = 15): array
    {
        $db = Database::getInstance();
        $page = max(1, (int)$this->getParam('page', 1));
        $offset = ($page - 1) * $perPage;

        $total = (int)($db->fetch($countSql, $params)->count ?? 0);
        $lastPage = max(1, (int)ceil($total / $perPage));

        $data = $db->fetchAll(
            $baseSql . " LIMIT :lim OFFSET :off",
            array_merge($params, ['lim' => $perPage, 'off' => $offset])
        );

        return [
            'data' => $data,
            'currentPage' => $page,
            'perPage' => $perPage,
            'total' => $total,
            'lastPage' => $lastPage,
            'from' => $offset + 1,
            'to' => min($offset + $perPage, $total),
        ];
    }

    public function dashboard(): void
    {
        $db = Database::getInstance();

        $totalUsers = (int)($db->fetch(
            "SELECT COUNT(*) as count FROM users WHERE role = 'customer'"
        )->count ?? 0);

        $totalVendors = (int)($db->fetch(
            "SELECT COUNT(*) as count FROM users WHERE role = 'vendor'"
        )->count ?? 0);

        $totalOrders = (int)($db->fetch(
            "SELECT COUNT(*) as count FROM orders"
        )->count ?? 0);

        $totalRevenue = (float)($db->fetch(
            "SELECT COALESCE(SUM(total), 0) as total FROM orders WHERE payment_status = 'paid'"
        )->total ?? 0);

        $monthlyRevenue = $db->fetchAll(
            "SELECT DATE_FORMAT(created_at, '%Y-%m') as month,
                    SUM(total) as revenue,
                    COUNT(*) as order_count
             FROM orders
             WHERE payment_status = 'paid'
               AND created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
             GROUP BY DATE_FORMAT(created_at, '%Y-%m')
             ORDER BY month ASC"
        );

        $recentOrders = $db->fetchAll(
            "SELECT o.*, CONCAT(u.first_name, ' ', u.last_name) as customer_name
             FROM orders o
             LEFT JOIN users u ON o.user_id = u.id
             ORDER BY o.id DESC
             LIMIT 10"
        );

        $bestSellers = $db->fetchAll(
            "SELECT p.id, p.name, p.slug, p.base_price,
                    SUM(oi.quantity) as total_qty,
                    SUM(oi.total_price) as total_revenue
             FROM order_items oi
             JOIN products p ON oi.product_id = p.id
             GROUP BY oi.product_id
             ORDER BY total_qty DESC
             LIMIT 5"
        );

        $topVendors = $db->fetchAll(
            "SELECT u.id,
                    CONCAT(u.first_name, ' ', u.last_name) as vendor_name,
                    s.store_name,
                    COALESCE(SUM(oi.vendor_earnings), 0) as revenue,
                    COUNT(DISTINCT oi.order_id) as order_count
             FROM order_items oi
             JOIN users u ON oi.vendor_id = u.id
             LEFT JOIN stores s ON u.id = s.vendor_id
             WHERE oi.status = 'delivered'
             GROUP BY oi.vendor_id
             ORDER BY revenue DESC
             LIMIT 5"
        );

        $this->renderView('admin/dashboard', [
            'totalUsers' => $totalUsers,
            'totalVendors' => $totalVendors,
            'totalOrders' => $totalOrders,
            'totalRevenue' => $totalRevenue,
            'monthlyRevenue' => $monthlyRevenue,
            'recentOrders' => $recentOrders,
            'bestSellers' => $bestSellers,
            'topVendors' => $topVendors,
        ]);
    }

    public function users(): void
    {
        $db = Database::getInstance();

        $pagination = $this->paginate(
            "SELECT * FROM users WHERE role = 'customer' ORDER BY id DESC",
            "SELECT COUNT(*) as count FROM users WHERE role = 'customer'",
            [],
            15
        );

        $this->renderView('admin/users', ['users' => $pagination]);
    }

    public function updateUserStatus($id): void
    {
        $db = Database::getInstance();
        $user = User::find($id);

        if (!$user) {
            $this->renderJSON(['success' => false, 'message' => 'User not found.'], 404);
            return;
        }

        $newStatus = $user->status === 'active' ? 'suspended' : 'active';
        User::update($id, ['status' => $newStatus]);

        $this->renderJSON([
            'success' => true,
            'message' => "User status updated to {$newStatus}.",
            'status' => $newStatus,
        ]);
    }

    public function vendors(): void
    {
        $db = Database::getInstance();

        $page = max(1, (int)$this->getParam('page', 1));
        $perPage = 15;
        $offset = ($page - 1) * $perPage;

        $total = (int)($db->fetch(
            "SELECT COUNT(*) as count FROM users WHERE role = 'vendor'"
        )->count ?? 0);

        $lastPage = max(1, (int)ceil($total / $perPage));

        $vendors = $db->fetchAll(
            "SELECT u.id, u.first_name, u.last_name, u.email, u.phone, u.status as user_status,
                    u.created_at as joined_at,
                    s.id as store_id, s.store_name, s.slug as store_slug,
                    s.is_verified, s.is_active as store_active, s.commission_rate,
                    (SELECT COUNT(*) FROM products p WHERE p.vendor_id = u.id) as product_count,
                    (SELECT COALESCE(SUM(oi.vendor_earnings), 0) FROM order_items oi WHERE oi.vendor_id = u.id AND oi.status = 'delivered') as total_revenue
             FROM users u
             LEFT JOIN stores s ON u.id = s.vendor_id
             WHERE u.role = 'vendor'
             ORDER BY u.id DESC
             LIMIT :lim OFFSET :off",
            ['lim' => $perPage, 'off' => $offset]
        );

        $this->renderView('admin/vendors', [
            'vendors' => $vendors,
            'currentPage' => $page,
            'perPage' => $perPage,
            'total' => $total,
            'lastPage' => $lastPage,
            'from' => $offset + 1,
            'to' => min($offset + $perPage, $total),
        ]);
    }

    public function verifyVendor($id): void
    {
        $db = Database::getInstance();
        $store = Store::findBy('vendor_id', $id);

        if (!$store) {
            $this->renderJSON(['success' => false, 'message' => 'Store not found for this vendor.'], 404);
            return;
        }

        $newVerified = $store->is_verified ? 0 : 1;
        Store::update($store->id, ['is_verified' => $newVerified]);

        $this->renderJSON([
            'success' => true,
            'message' => $newVerified ? 'Vendor verified successfully.' : 'Vendor verification removed.',
            'is_verified' => $newVerified,
        ]);
    }

    public function products(): void
    {
        $db = Database::getInstance();
        $status = $this->getParam('status', 'all');

        $whereClause = '';
        $params = [];

        if ($status !== 'all') {
            $whereClause = "WHERE p.status = :status";
            $params['status'] = $status;
        }

        $page = max(1, (int)$this->getParam('page', 1));
        $perPage = 15;
        $offset = ($page - 1) * $perPage;

        $total = (int)($db->fetch(
            "SELECT COUNT(*) as count FROM products p {$whereClause}",
            $params
        )->count ?? 0);

        $lastPage = max(1, (int)ceil($total / $perPage));

        $products = $db->fetchAll(
            "SELECT p.*,
                    CONCAT(u.first_name, ' ', u.last_name) as vendor_name,
                    s.store_name,
                    c.name as category_name,
                    b.name as brand_name,
                    (SELECT pi.image FROM product_images pi WHERE pi.product_id = p.id AND pi.is_primary = 1 LIMIT 1) as primary_image
             FROM products p
             LEFT JOIN users u ON p.vendor_id = u.id
             LEFT JOIN stores s ON p.store_id = s.id
             LEFT JOIN categories c ON p.category_id = c.id
             LEFT JOIN brands b ON p.brand_id = b.id
             {$whereClause}
             ORDER BY p.id DESC
             LIMIT :lim OFFSET :off",
            array_merge($params, ['lim' => $perPage, 'off' => $offset])
        );

        $this->renderView('admin/products', [
            'products' => $products,
            'currentPage' => $page,
            'perPage' => $perPage,
            'total' => $total,
            'lastPage' => $lastPage,
            'from' => $offset + 1,
            'to' => min($offset + $perPage, $total),
            'filterStatus' => $status,
        ]);
    }

    public function approveProduct($id): void
    {
        $product = Product::find($id);

        if (!$product) {
            $this->renderJSON(['success' => false, 'message' => 'Product not found.'], 404);
            return;
        }

        Product::update($id, [
            'status' => 'approved',
            'is_approved' => 1,
            'is_active' => 1,
        ]);

        $this->renderJSON([
            'success' => true,
            'message' => 'Product approved successfully.',
        ]);
    }

    public function rejectProduct($id): void
    {
        $product = Product::find($id);

        if (!$product) {
            $this->renderJSON(['success' => false, 'message' => 'Product not found.'], 404);
            return;
        }

        $body = $this->getRequestBody();
        $reason = $body['rejection_reason'] ?? $this->getParam('rejection_reason', '');

        Product::update($id, [
            'status' => 'rejected',
            'rejection_reason' => $reason,
        ]);

        $this->renderJSON([
            'success' => true,
            'message' => 'Product rejected.',
        ]);
    }

    public function toggleFeatured($id): void
    {
        $product = Product::find($id);

        if (!$product) {
            $this->renderJSON(['success' => false, 'message' => 'Product not found.'], 404);
            return;
        }

        $newFeatured = $product->is_featured ? 0 : 1;
        Product::update($id, ['is_featured' => $newFeatured]);

        $this->renderJSON([
            'success' => true,
            'message' => $newFeatured ? 'Product marked as featured.' : 'Product unmarked as featured.',
            'is_featured' => $newFeatured,
        ]);
    }

    public function categories(): void
    {
        $db = Database::getInstance();

        $parents = $db->fetchAll(
            "SELECT * FROM categories WHERE parent_id IS NULL OR parent_id = 0 ORDER BY sort_order ASC, name ASC"
        );

        $children = $db->fetchAll(
            "SELECT * FROM categories WHERE parent_id IS NOT NULL AND parent_id > 0 ORDER BY sort_order ASC, name ASC"
        );

        $childMap = [];
        foreach ($children as $child) {
            $childMap[$child->parent_id][] = $child;
        }

        $this->renderView('admin/categories', [
            'parents' => $parents,
            'children' => $childMap,
        ]);
    }

    public function storeCategory(): void
    {
        $name = Validator::sanitizeString($this->getParam('name', ''));
        $slug = $this->getParam('slug', '') ?: $this->generateSlug($name);
        $parentId = $this->getParam('parent_id') !== '' && $this->getParam('parent_id') !== null
            ? (int)$this->getParam('parent_id') : null;
        $description = Validator::sanitizeString($this->getParam('description', ''));

        $this->validator->validate(
            ['name' => $name, 'slug' => $slug],
            ['name' => 'required|min:2|max:200', 'slug' => 'required|min:2|max:200']
        );

        if ($this->validator->fails()) {
            $this->session->setFlash('error', implode(', ', $this->validator->getErrors()));
            $this->redirectWith('/admin/categories', 'Please fix the errors below.', 'error');
            return;
        }

        $db = Database::getInstance();
        $existing = $db->fetch(
            "SELECT id FROM categories WHERE slug = :slug LIMIT 1",
            ['slug' => $slug]
        );

        if ($existing) {
            $slug = $slug . '-' . uniqid();
        }

        Category::create([
            'name' => $name,
            'slug' => $slug,
            'parent_id' => $parentId,
            'description' => $description,
            'sort_order' => (int)$this->getParam('sort_order', 0),
            'is_active' => 1,
        ]);

        $this->redirectWith('/admin/categories', 'Category created successfully.', 'success');
    }

    public function deleteCategory($id): void
    {
        $db = Database::getInstance();
        $category = Category::find($id);

        if (!$category) {
            $this->renderJSON(['success' => false, 'message' => 'Category not found.'], 404);
            return;
        }

        $productCount = (int)($db->fetch(
            "SELECT COUNT(*) as count FROM products WHERE category_id = :id",
            ['id' => $id]
        )->count ?? 0);

        if ($productCount > 0) {
            $this->renderJSON([
                'success' => false,
                'message' => "Cannot delete category. {$productCount} product(s) are assigned to it.",
            ]);
            return;
        }

        $childCount = (int)($db->fetch(
            "SELECT COUNT(*) as count FROM categories WHERE parent_id = :id",
            ['id' => $id]
        )->count ?? 0);

        if ($childCount > 0) {
            $this->renderJSON([
                'success' => false,
                'message' => "Cannot delete category. It has {$childCount} sub-category(ies).",
            ]);
            return;
        }

        Category::delete($id);
        $this->renderJSON(['success' => true, 'message' => 'Category deleted successfully.']);
    }

    public function brands(): void
    {
        $db = Database::getInstance();

        $brands = $db->fetchAll(
            "SELECT b.*,
                    (SELECT COUNT(*) FROM products p WHERE p.brand_id = b.id) as product_count
             FROM brands b
             ORDER BY b.name ASC"
        );

        $this->renderView('admin/brands', ['brands' => $brands]);
    }

    public function storeBrand(): void
    {
        $name = Validator::sanitizeString($this->getParam('name', ''));
        $slug = $this->getParam('slug', '') ?: $this->generateSlug($name);
        $description = Validator::sanitizeString($this->getParam('description', ''));

        $this->validator->validate(
            ['name' => $name],
            ['name' => 'required|min:2|max:200']
        );

        if ($this->validator->fails()) {
            $this->session->setFlash('error', implode(', ', $this->validator->getErrors()));
            $this->redirectWith('/admin/brands', 'Please fix the errors below.', 'error');
            return;
        }

        $db = Database::getInstance();
        $existing = $db->fetch(
            "SELECT id FROM brands WHERE slug = :slug LIMIT 1",
            ['slug' => $slug]
        );

        if ($existing) {
            $slug = $slug . '-' . uniqid();
        }

        Brand::create([
            'name' => $name,
            'slug' => $slug,
            'description' => $description,
            'is_active' => (int)(bool)$this->getParam('is_active', 1),
        ]);

        $this->redirectWith('/admin/brands', 'Brand created successfully.', 'success');
    }

    public function deleteBrand($id): void
    {
        $db = Database::getInstance();
        $brand = Brand::find($id);

        if (!$brand) {
            $this->renderJSON(['success' => false, 'message' => 'Brand not found.'], 404);
            return;
        }

        $productCount = (int)($db->fetch(
            "SELECT COUNT(*) as count FROM products WHERE brand_id = :id",
            ['id' => $id]
        )->count ?? 0);

        if ($productCount > 0) {
            $this->renderJSON([
                'success' => false,
                'message' => "Cannot delete brand. {$productCount} product(s) are assigned to it.",
            ]);
            return;
        }

        Brand::delete($id);
        $this->renderJSON(['success' => true, 'message' => 'Brand deleted successfully.']);
    }

    public function orders(): void
    {
        $db = Database::getInstance();

        $page = max(1, (int)$this->getParam('page', 1));
        $perPage = 15;
        $offset = ($page - 1) * $perPage;

        $total = (int)($db->fetch(
            "SELECT COUNT(*) as count FROM orders"
        )->count ?? 0);

        $lastPage = max(1, (int)ceil($total / $perPage));

        $orders = $db->fetchAll(
            "SELECT o.*,
                    CONCAT(u.first_name, ' ', u.last_name) as customer_name,
                    u.email as customer_email,
                    (SELECT COUNT(*) FROM order_items oi WHERE oi.order_id = o.id) as item_count
             FROM orders o
             LEFT JOIN users u ON o.user_id = u.id
             ORDER BY o.id DESC
             LIMIT :lim OFFSET :off",
            ['lim' => $perPage, 'off' => $offset]
        );

        $this->renderView('admin/orders', [
            'orders' => $orders,
            'currentPage' => $page,
            'perPage' => $perPage,
            'total' => $total,
            'lastPage' => $lastPage,
            'from' => $offset + 1,
            'to' => min($offset + $perPage, $total),
        ]);
    }

    public function orderDetail($id): void
    {
        $db = Database::getInstance();

        $order = Order::find($id);

        if (!$order) {
            $this->redirectWith('/admin/orders', 'Order not found.', 'error');
            return;
        }

        $order->customer = $db->fetch(
            "SELECT id, first_name, last_name, email, phone FROM users WHERE id = :uid",
            ['uid' => $order->user_id]
        );

        $items = $db->fetchAll(
            "SELECT oi.*,
                    CONCAT(u.first_name, ' ', u.last_name) as vendor_name,
                    (SELECT pi.image FROM product_images pi WHERE pi.product_id = oi.product_id AND pi.is_primary = 1 LIMIT 1) as product_image
             FROM order_items oi
             LEFT JOIN users u ON oi.vendor_id = u.id
             WHERE oi.order_id = :oid
             ORDER BY oi.id ASC",
            ['oid' => $id]
        );

        $order->shipping_address = $order->shipping_address_id
            ? $db->fetch("SELECT * FROM addresses WHERE id = :id", ['id' => $order->shipping_address_id])
            : null;

        $order->payment = $db->fetch(
            "SELECT * FROM payments WHERE order_id = :oid LIMIT 1",
            ['oid' => $id]
        );

        $this->renderView('admin/order-detail', [
            'order' => $order,
            'items' => $items,
        ]);
    }

    public function updateOrderStatus($id): void
    {
        $db = Database::getInstance();
        $order = Order::find($id);

        if (!$order) {
            $this->redirectWith('/admin/orders', 'Order not found.', 'error');
            return;
        }

        $orderStatus = $this->getParam('order_status', '');
        $paymentStatus = $this->getParam('payment_status', '');

        $allowedOrderStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled', 'returned'];
        $allowedPaymentStatuses = ['pending', 'paid', 'failed', 'refunded', 'partially_refunded'];

        $updateData = [];

        if ($orderStatus && in_array($orderStatus, $allowedOrderStatuses)) {
            $updateData['order_status'] = $orderStatus;

            if ($orderStatus === 'delivered') {
                $updateData['delivered_at'] = date('Y-m-d H:i:s');
            }
        }

        if ($paymentStatus && in_array($paymentStatus, $allowedPaymentStatuses)) {
            $updateData['payment_status'] = $paymentStatus;

            if ($paymentStatus === 'paid' && !$order->paid_at) {
                $updateData['paid_at'] = date('Y-m-d H:i:s');
            }
        }

        if (empty($updateData)) {
            $this->redirectWith('/admin/orders/' . $id, 'No valid status provided.', 'error');
            return;
        }

        $sets = '';
        $params = [];
        foreach ($updateData as $col => $val) {
            $sets .= "{$col} = :{$col}, ";
            $params[$col] = $val;
        }
        $sets = rtrim($sets, ', ');
        $params['id'] = $id;
        $db->query("UPDATE orders SET {$sets} WHERE id = :id", $params);

        if ($paymentStatus === 'refunded' && $order->payment_status === 'paid') {
            $transactionRef = 'RFD-' . strtoupper(uniqid());

            $db->insert('transactions', [
                'user_id' => $order->user_id,
                'order_id' => $order->id,
                'type' => 'refund',
                'amount' => $order->total,
                'fee' => 0,
                'net_amount' => $order->total,
                'reference' => $transactionRef,
                'description' => 'Full refund for order #' . $order->order_number,
                'status' => 'completed',
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }

        $this->redirectWith('/admin/orders/' . $id, 'Order status updated successfully.', 'success');
    }

    public function transactions(): void
    {
        $db = Database::getInstance();

        $page = max(1, (int)$this->getParam('page', 1));
        $perPage = 15;
        $offset = ($page - 1) * $perPage;

        $total = (int)($db->fetch(
            "SELECT COUNT(*) as count FROM transactions"
        )->count ?? 0);

        $lastPage = max(1, (int)ceil($total / $perPage));

        $transactions = $db->fetchAll(
            "SELECT t.*,
                    CONCAT(u.first_name, ' ', u.last_name) as user_name,
                    u.email as user_email,
                    o.order_number
             FROM transactions t
             LEFT JOIN users u ON t.user_id = u.id
             LEFT JOIN orders o ON t.order_id = o.id
             ORDER BY t.id DESC
             LIMIT :lim OFFSET :off",
            ['lim' => $perPage, 'off' => $offset]
        );

        $this->renderView('admin/transactions', [
            'transactions' => $transactions,
            'currentPage' => $page,
            'perPage' => $perPage,
            'total' => $total,
            'lastPage' => $lastPage,
            'from' => $offset + 1,
            'to' => min($offset + $perPage, $total),
        ]);
    }

    public function withdrawals(): void
    {
        $db = Database::getInstance();

        $page = max(1, (int)$this->getParam('page', 1));
        $perPage = 15;
        $offset = ($page - 1) * $perPage;

        $total = (int)($db->fetch(
            "SELECT COUNT(*) as count FROM withdrawals"
        )->count ?? 0);

        $lastPage = max(1, (int)ceil($total / $perPage));

        $withdrawals = $db->fetchAll(
            "SELECT w.*,
                    CONCAT(u.first_name, ' ', u.last_name) as vendor_name,
                    u.email as vendor_email,
                    s.store_name
             FROM withdrawals w
             LEFT JOIN users u ON w.vendor_id = u.id
             LEFT JOIN stores s ON u.id = s.vendor_id
             ORDER BY FIELD(w.status, 'pending', 'processing', 'completed', 'failed'), w.id DESC
             LIMIT :lim OFFSET :off",
            ['lim' => $perPage, 'off' => $offset]
        );

        $this->renderView('admin/withdrawals', [
            'withdrawals' => $withdrawals,
            'currentPage' => $page,
            'perPage' => $perPage,
            'total' => $total,
            'lastPage' => $lastPage,
            'from' => $offset + 1,
            'to' => min($offset + $perPage, $total),
        ]);
    }

    public function processWithdrawal($id): void
    {
        $db = Database::getInstance();
        $withdrawal = Withdrawal::find($id);

        if (!$withdrawal) {
            $this->redirectWith('/admin/withdrawals', 'Withdrawal request not found.', 'error');
            return;
        }

        $status = $this->getParam('status', '');
        $adminNote = Validator::sanitizeString($this->getParam('admin_note', ''));

        $allowedStatuses = ['processing', 'completed', 'failed'];
        if (!in_array($status, $allowedStatuses)) {
            $this->redirectWith('/admin/withdrawals', 'Invalid status.', 'error');
            return;
        }

        $updateData = [
            'status' => $status,
            'admin_note' => $adminNote,
            'processed_at' => date('Y-m-d H:i:s'),
            'processed_by' => $this->session->getUserId(),
        ];

        $sets = '';
        $params = [];
        foreach ($updateData as $col => $val) {
            $sets .= "{$col} = :{$col}, ";
            $params[$col] = $val;
        }
        $sets = rtrim($sets, ', ');
        $params['id'] = $id;
        $db->query("UPDATE withdrawals SET {$sets} WHERE id = :id", $params);

        if (in_array($status, ['completed', 'processing'])) {
            $txnRef = 'WTH-' . strtoupper(uniqid());
            $db->insert('transactions', [
                'user_id' => $withdrawal->vendor_id,
                'type' => 'withdrawal',
                'amount' => $withdrawal->amount,
                'fee' => $withdrawal->fee ?? 0,
                'net_amount' => $withdrawal->net_amount,
                'reference' => $txnRef,
                'description' => 'Withdrawal ' . $status . ': ' . $this->formatPrice($withdrawal->amount),
                'status' => 'completed',
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }

        $this->redirectWith('/admin/withdrawals', 'Withdrawal updated successfully.', 'success');
    }

    public function banners(): void
    {
        $db = Database::getInstance();

        $banners = $db->fetchAll(
            "SELECT * FROM banners ORDER BY sort_order ASC, id DESC"
        );

        $this->renderView('admin/banners', ['banners' => $banners]);
    }

    public function storeBanner(): void
    {
        $title = Validator::sanitizeString($this->getParam('title', ''));
        $subtitle = Validator::sanitizeString($this->getParam('subtitle', ''));
        $link = Validator::sanitizeString($this->getParam('link', ''));
        $sortOrder = (int)$this->getParam('sort_order', 0);

        if (empty($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            $this->redirectWith('/admin/banners', 'Banner image is required.', 'error');
            return;
        }

        $imagePath = $this->handleImageUpload($_FILES['image'], 'banners');
        if (!$imagePath) {
            $this->redirectWith('/admin/banners', 'Failed to upload image. Allowed types: jpeg, png, gif, webp.', 'error');
            return;
        }

        Banner::create([
            'title' => $title,
            'subtitle' => $subtitle,
            'image' => $imagePath,
            'link' => $link,
            'sort_order' => $sortOrder,
            'is_active' => (int)(bool)$this->getParam('is_active', 1),
        ]);

        $this->redirectWith('/admin/banners', 'Banner created successfully.', 'success');
    }

    public function deleteBanner($id): void
    {
        $banner = Banner::find($id);

        if (!$banner) {
            $this->renderJSON(['success' => false, 'message' => 'Banner not found.'], 404);
            return;
        }

        $this->deleteImageFile($banner->image);
        Banner::delete($id);

        $this->renderJSON(['success' => true, 'message' => 'Banner deleted successfully.']);
    }

    public function settings(): void
    {
        $db = Database::getInstance();

        $allSettings = $db->fetchAll(
            "SELECT * FROM settings ORDER BY group_name ASC, `key` ASC"
        );

        $grouped = [];
        foreach ($allSettings as $setting) {
            $group = $setting->group_name ?: 'general';
            $grouped[$group][] = $setting;
        }

        $this->renderView('admin/settings', ['settingGroups' => $grouped]);
    }

    public function updateSettings(): void
    {
        $db = Database::getInstance();

        foreach ($_POST as $key => $value) {
            if ($key === '_csrf_token') {
                continue;
            }

            $existing = $db->fetch(
                "SELECT id FROM settings WHERE `key` = :key LIMIT 1",
                ['key' => $key]
            );

            if ($existing) {
                $db->update('settings', ['value' => $value], '`key` = :key', ['key' => $key]);
            } else {
                $db->insert('settings', [
                    'key' => $key,
                    'value' => $value,
                    'group_name' => 'general',
                ]);
            }
        }

        $this->redirectWith('/admin/settings', 'Settings updated successfully.', 'success');
    }

    public function notifications(): void
    {
        $db = Database::getInstance();

        $page = max(1, (int)$this->getParam('page', 1));
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        $total = (int)($db->fetch(
            "SELECT COUNT(*) as count FROM notifications"
        )->count ?? 0);

        $lastPage = max(1, (int)ceil($total / $perPage));

        $notifications = $db->fetchAll(
            "SELECT n.*,
                    CONCAT(u.first_name, ' ', u.last_name) as user_name
             FROM notifications n
             LEFT JOIN users u ON n.user_id = u.id
             ORDER BY n.id DESC
             LIMIT :lim OFFSET :off",
            ['lim' => $perPage, 'off' => $offset]
        );

        $this->renderView('admin/notifications', [
            'notifications' => $notifications,
            'currentPage' => $page,
            'perPage' => $perPage,
            'total' => $total,
            'lastPage' => $lastPage,
            'from' => $offset + 1,
            'to' => min($offset + $perPage, $total),
        ]);
    }

    public function sendNotification(): void
    {
        $title = Validator::sanitizeString($this->getParam('title', ''));
        $message = $this->getParam('message', '');
        $type = Validator::sanitizeString($this->getParam('type', 'general'));
        $targetRole = $this->getParam('target_role', 'all');

        $this->validator->validate(
            ['title' => $title, 'message' => $message],
            ['title' => 'required|min:3|max:200', 'message' => 'required|min:10']
        );

        if ($this->validator->fails()) {
            $this->session->setFlash('error', implode(', ', $this->validator->getErrors()));
            $this->redirectWith('/admin/notifications', 'Please fix the errors below.', 'error');
            return;
        }

        $db = Database::getInstance();

        if ($targetRole === 'all') {
            $users = $db->fetchAll("SELECT id FROM users WHERE status = 'active'");
        } else {
            $users = $db->fetchAll(
                "SELECT id FROM users WHERE role = :role AND status = 'active'",
                ['role' => $targetRole]
            );
        }

        foreach ($users as $user) {
            Notification::createForUser($user->id, $type, $title, $message);
        }

        $this->redirectWith(
            '/admin/notifications',
            "Notification sent to " . count($users) . " user(s).",
            'success'
        );
    }
}
