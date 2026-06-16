<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Validator;
use App\Core\Middleware;
use App\Models\Address;
use App\Models\Notification;
use App\Models\Wishlist;

class DashboardController extends Controller
{
    public function index(): void
    {
        Middleware::auth();

        $db = Database::getInstance();
        $userId = $this->session->getUserId();

        $totalOrders = (int)$db->fetch(
            "SELECT COUNT(*) as count FROM orders WHERE user_id = :user_id",
            ['user_id' => $userId]
        )->count;

        $totalSpent = (float)$db->fetch(
            "SELECT COALESCE(SUM(total), 0) as total FROM orders WHERE user_id = :user_id AND payment_status = 'paid'",
            ['user_id' => $userId]
        )->total;

        $pendingOrders = (int)$db->fetch(
            "SELECT COUNT(*) as count FROM orders WHERE user_id = :user_id AND status = 'pending'",
            ['user_id' => $userId]
        )->count;

        $wishlistCount = (int)$db->fetch(
            "SELECT COUNT(*) as count FROM wishlists WHERE user_id = :user_id",
            ['user_id' => $userId]
        )->count;

        $recentOrders = $db->fetchAll(
            "SELECT * FROM orders WHERE user_id = :user_id ORDER BY id DESC LIMIT 5",
            ['user_id' => $userId]
        );

        $this->renderView('customer/dashboard', [
            'totalOrders' => $totalOrders,
            'totalSpent' => $totalSpent,
            'pendingOrders' => $pendingOrders,
            'wishlistCount' => $wishlistCount,
            'recentOrders' => $recentOrders,
        ]);
    }

    public function orders(): void
    {
        Middleware::auth();

        $db = Database::getInstance();
        $userId = $this->session->getUserId();
        $page = max(1, (int)$this->getParam('page', 1));
        $perPage = 10;
        $offset = ($page - 1) * $perPage;

        $total = (int)$db->fetch(
            "SELECT COUNT(*) as count FROM orders WHERE user_id = :user_id",
            ['user_id' => $userId]
        )->count;

        $lastPage = max(1, (int)ceil($total / $perPage));

        $orders = $db->fetchAll(
            "SELECT * FROM orders WHERE user_id = :user_id ORDER BY id DESC LIMIT :limit OFFSET :offset",
            ['user_id' => $userId, 'limit' => $perPage, 'offset' => $offset]
        );

        $this->renderView('customer/orders', [
            'orders' => $orders,
            'current_page' => $page,
            'per_page' => $perPage,
            'total' => $total,
            'last_page' => $lastPage,
            'from' => $offset + 1,
            'to' => min($offset + $perPage, $total),
        ]);
    }

    public function orderDetail($id): void
    {
        Middleware::auth();

        $db = Database::getInstance();
        $userId = $this->session->getUserId();

        $order = $db->fetch(
            "SELECT * FROM orders WHERE id = :id AND user_id = :user_id",
            ['id' => $id, 'user_id' => $userId]
        );

        if (!$order) {
            $this->redirectWith('/orders', 'Order not found.', 'error');
            return;
        }

        $items = $db->fetchAll(
            "SELECT oi.*,
                    (SELECT pi.image FROM product_images pi WHERE pi.product_id = oi.product_id AND pi.is_primary = 1 LIMIT 1) as image
             FROM order_items oi
             WHERE oi.order_id = :order_id
             ORDER BY oi.id ASC",
            ['order_id' => $order->id]
        );

        $this->renderView('customer/order-detail', [
            'order' => $order,
            'items' => $items,
        ]);
    }

    public function wishlist(): void
    {
        Middleware::auth();

        $userId = $this->session->getUserId();
        $items = Wishlist::getUserWishlist($userId);

        $this->renderView('customer/wishlist', [
            'items' => $items,
        ]);
    }

    public function addresses(): void
    {
        Middleware::auth();

        $userId = $this->session->getUserId();
        $addresses = Address::where('user_id', $userId)->orderBy('is_default', 'DESC')->orderBy('id', 'DESC')->get();

        $this->renderView('customer/addresses', [
            'addresses' => $addresses,
        ]);
    }

    public function saveAddress(): void
    {
        Middleware::auth();
        Middleware::csrf();

        $userId = $this->session->getUserId();
        $addressId = (int)$this->getParam('id', 0);

        $data = [
            'user_id' => $userId,
            'label' => Validator::sanitizeString($this->getParam('label', 'Home')),
            'full_name' => Validator::sanitizeString($this->getParam('full_name', '')),
            'phone' => Validator::sanitizeString($this->getParam('phone', '')),
            'street_address' => Validator::sanitizeString($this->getParam('street_address', '')),
            'city' => Validator::sanitizeString($this->getParam('city', '')),
            'state' => Validator::sanitizeString($this->getParam('state', '')),
            'postal_code' => Validator::sanitizeString($this->getParam('postal_code', '')),
            'country' => Validator::sanitizeString($this->getParam('country', 'Ghana')),
            'is_default' => (int)(bool)$this->getParam('is_default', 0),
        ];

        $this->validator->validate($data, [
            'full_name' => 'required|min:2|max:100',
            'phone' => 'required|min:5|max:20',
            'street_address' => 'required|min:5|max:255',
            'city' => 'required|min:2|max:100',
            'country' => 'required|min:2|max:100',
        ]);

        if ($this->validator->fails()) {
            $this->redirectWith('/dashboard/addresses', implode(', ', $this->validator->getErrors()), 'error');
            return;
        }

        $db = Database::getInstance();

        $fillable = [
            'user_id', 'label', 'full_name', 'phone', 'street_address',
            'city', 'state', 'postal_code', 'country', 'is_default',
        ];

        $addressData = array_intersect_key($data, array_flip($fillable));

        if ($data['is_default']) {
            $db->update('addresses', ['is_default' => 0], 'user_id = :user_id', ['user_id' => $userId]);
        }

        if ($addressId) {
            $existing = Address::find($addressId);
            if (!$existing || (int)$existing->user_id !== $userId) {
                $this->redirectWith('/dashboard/addresses', 'Address not found.', 'error');
                return;
            }

            Address::update($addressId, $addressData);
            $this->redirectWith('/dashboard/addresses', 'Address updated successfully.', 'success');
        } else {
            Address::create($addressData);
            $this->redirectWith('/dashboard/addresses', 'Address added successfully.', 'success');
        }
    }

    public function deleteAddress(): void
    {
        Middleware::auth();
        Middleware::csrf();

        $userId = $this->session->getUserId();
        $addressId = (int)$this->getParam('id', 0);

        $address = Address::find($addressId);
        if (!$address || (int)$address->user_id !== $userId) {
            $this->renderJSON(['success' => false, 'message' => 'Address not found.'], 404);
            return;
        }

        Address::delete($addressId);
        $this->renderJSON(['success' => true, 'message' => 'Address deleted successfully.']);
    }

    public function profile(): void
    {
        Middleware::auth();

        $userId = $this->session->getUserId();
        $db = Database::getInstance();
        $user = $db->fetch("SELECT * FROM users WHERE id = :id", ['id' => $userId]);

        $this->renderView('customer/profile', [
            'user' => $user,
        ]);
    }

    public function updateProfile(): void
    {
        Middleware::auth();
        Middleware::csrf();

        $userId = $this->session->getUserId();

        $firstName = Validator::sanitizeString($this->getParam('first_name', ''));
        $lastName = Validator::sanitizeString($this->getParam('last_name', ''));
        $phone = Validator::sanitizeString($this->getParam('phone', ''));
        $email = Validator::sanitizeEmail($this->getParam('email', ''));

        $this->validator->validate([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'phone' => $phone,
            'email' => $email,
        ], [
            'first_name' => 'required|min:1|max:100',
            'last_name' => 'required|min:1|max:100',
            'phone' => 'required|min:5|max:20',
            'email' => 'required|email',
        ]);

        if ($this->validator->fails()) {
            $this->redirectWith('/dashboard/profile', implode(', ', $this->validator->getErrors()), 'error');
            return;
        }

        $db = Database::getInstance();

        $existing = $db->fetch(
            "SELECT id FROM users WHERE email = :email AND id != :user_id",
            ['email' => $email, 'user_id' => $userId]
        );

        if ($existing) {
            $this->redirectWith('/dashboard/profile', 'This email is already in use by another account.', 'error');
            return;
        }

        $db->update('users', [
            'name' => trim($firstName . ' ' . $lastName),
            'phone' => $phone,
            'email' => $email,
        ], 'id = :id', ['id' => $userId]);

        $updatedUser = $db->fetch("SELECT * FROM users WHERE id = :id", ['id' => $userId]);
        $this->session->setUser($updatedUser);

        $this->redirectWith('/dashboard/profile', 'Profile updated successfully.', 'success');
    }

    public function reviews(): void
    {
        Middleware::auth();

        $db = Database::getInstance();
        $userId = $this->session->getUserId();

        $reviews = $db->fetchAll(
            "SELECT r.*, p.name as product_name, p.slug as product_slug,
                    (SELECT pi.image FROM product_images pi WHERE pi.product_id = p.id AND pi.is_primary = 1 LIMIT 1) as product_image
             FROM reviews r
             JOIN products p ON r.product_id = p.id
             WHERE r.user_id = :user_id
             ORDER BY r.id DESC",
            ['user_id' => $userId]
        );

        $this->renderView('customer/reviews', [
            'reviews' => $reviews,
        ]);
    }

    public function submitReview(): void
    {
        Middleware::auth();
        Middleware::csrf();

        $userId = $this->session->getUserId();
        $productId = (int)$this->getParam('product_id', 0);
        $orderId = (int)$this->getParam('order_id', 0);
        $rating = (int)$this->getParam('rating', 0);
        $title = Validator::sanitizeString($this->getParam('title', ''));
        $review = Validator::sanitizeString($this->getParam('review', ''));

        $this->validator->validate([
            'product_id' => $productId,
            'order_id' => $orderId,
            'rating' => $rating,
            'title' => $title,
            'review' => $review,
        ], [
            'product_id' => 'required|integer',
            'order_id' => 'required|integer',
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'required|min:3|max:200',
            'review' => 'required|min:10|max:5000',
        ]);

        if ($this->validator->fails()) {
            $this->redirectWith('/dashboard/reviews', implode(', ', $this->validator->getErrors()), 'error');
            return;
        }

        $db = Database::getInstance();

        $order = $db->fetch(
            "SELECT id FROM orders WHERE id = :id AND user_id = :user_id",
            ['id' => $orderId, 'user_id' => $userId]
        );

        if (!$order) {
            $this->redirectWith('/dashboard/reviews', 'Order not found.', 'error');
            return;
        }

        $orderItem = $db->fetch(
            "SELECT id FROM order_items WHERE order_id = :order_id AND product_id = :product_id",
            ['order_id' => $orderId, 'product_id' => $productId]
        );

        if (!$orderItem) {
            $this->redirectWith('/dashboard/reviews', 'You can only review products you have purchased.', 'error');
            return;
        }

        $existing = $db->fetch(
            "SELECT id FROM reviews WHERE user_id = :user_id AND product_id = :product_id AND order_id = :order_id",
            ['user_id' => $userId, 'product_id' => $productId, 'order_id' => $orderId]
        );

        if ($existing) {
            $this->redirectWith('/dashboard/reviews', 'You have already reviewed this product for this order.', 'error');
            return;
        }

        $db->insert('reviews', [
            'product_id' => $productId,
            'user_id' => $userId,
            'order_id' => $orderId,
            'rating' => $rating,
            'title' => $title,
            'comment' => $review,
            'status' => 'pending',
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $this->redirectWith('/dashboard/reviews', 'Your review has been submitted and is pending approval.', 'success');
    }

    public function notifications(): void
    {
        Middleware::auth();

        $userId = $this->session->getUserId();
        $notifications = Notification::where('user_id', $userId)->orderBy('id', 'DESC')->get();
        $unreadCount = Notification::where('user_id', $userId)->where('is_read', 0)->count();

        $this->renderView('customer/notifications', [
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
        ]);
    }

    public function markNotificationRead(): void
    {
        Middleware::auth();

        $notificationId = (int)$this->getParam('id', 0);
        $notification = Notification::find($notificationId);

        if (!$notification || (int)$notification->user_id !== $this->session->getUserId()) {
            $this->renderJSON(['success' => false, 'message' => 'Notification not found.'], 404);
            return;
        }

        Notification::markAsRead($notificationId);
        $this->renderJSON(['success' => true, 'message' => 'Notification marked as read.']);
    }
}
