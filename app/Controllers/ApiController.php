<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;

class ApiController extends Controller
{
    public function __construct()
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');

        if (($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS') {
            http_response_code(200);
            exit;
        }
    }

    public function products()
    {
        $db = Database::getInstance();

        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = max(1, min(100, (int)($_GET['per_page'] ?? 12)));
        $category = $_GET['category'] ?? null;
        $brand = $_GET['brand'] ?? null;
        $search = $_GET['search'] ?? null;
        $minPrice = $_GET['min_price'] ?? null;
        $maxPrice = $_GET['max_price'] ?? null;
        $sort = $_GET['sort'] ?? 'newest';

        $allowedSorts = ['newest', 'price_asc', 'price_desc', 'name_asc', 'name_desc'];
        if (!in_array($sort, $allowedSorts)) {
            $sort = 'newest';
        }

        $conditions = ['p.is_active = 1', 'p.is_approved = 1'];
        $params = [];

        if ($category) {
            $conditions[] = 'p.category_id = :category';
            $params['category'] = (int)$category;
        }

        if ($brand) {
            $conditions[] = 'p.brand_id = :brand';
            $params['brand'] = (int)$brand;
        }

        if ($search) {
            $conditions[] = '(p.name LIKE :search OR p.description LIKE :search2)';
            $params['search'] = "%{$search}%";
            $params['search2'] = "%{$search}%";
        }

        if ($minPrice !== null) {
            $conditions[] = 'COALESCE(p.sale_price, p.base_price) >= :min_price';
            $params['min_price'] = (float)$minPrice;
        }

        if ($maxPrice !== null) {
            $conditions[] = 'COALESCE(p.sale_price, p.base_price) <= :max_price';
            $params['max_price'] = (float)$maxPrice;
        }

        $whereClause = implode(' AND ', $conditions);

        $countResult = $db->fetch("SELECT COUNT(*) as count FROM products p WHERE {$whereClause}", $params);
        $total = (int)($countResult->count ?? 0);

        $lastPage = max(1, (int)ceil($total / $perPage));
        $page = min($page, $lastPage);
        $offset = ($page - 1) * $perPage;

        $orderClause = match ($sort) {
            'price_asc' => 'COALESCE(p.sale_price, p.base_price) ASC',
            'price_desc' => 'COALESCE(p.sale_price, p.base_price) DESC',
            'name_asc' => 'p.name ASC',
            'name_desc' => 'p.name DESC',
            default => 'p.id DESC',
        };

        $products = $db->fetchAll(
            "SELECT p.id, p.name, p.slug, p.base_price, p.sale_price,
                    (SELECT image FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) AS image,
                    s.store_name,
                    (SELECT AVG(rating) FROM reviews WHERE product_id = p.id AND is_approved = 1) AS rating
             FROM products p
             LEFT JOIN stores s ON p.store_id = s.id
             WHERE {$whereClause}
             GROUP BY p.id
             ORDER BY {$orderClause}
             LIMIT {$perPage} OFFSET {$offset}",
            $params
        );

        $this->renderJSON([
            'current_page' => $page,
            'data' => $products,
            'total' => $total,
            'last_page' => $lastPage,
        ]);
    }

    public function productDetail($id)
    {
        $db = Database::getInstance();

        $product = $db->fetch(
            "SELECT p.*, c.name AS category_name, s.store_name, s.slug AS store_slug, s.id AS store_id,
                    CONCAT(u.first_name, ' ', u.last_name) AS seller_name
             FROM products p
             LEFT JOIN categories c ON p.category_id = c.id
             LEFT JOIN stores s ON p.store_id = s.id
             LEFT JOIN users u ON s.vendor_id = u.id
             WHERE p.id = :id AND p.is_active = 1 AND p.is_approved = 1",
            ['id' => (int)$id]
        );

        if (!$product) {
            $this->renderJSON(['error' => 'Product not found'], 404);
            return;
        }

        $images = $db->fetchAll(
            "SELECT id, image, is_primary FROM product_images WHERE product_id = :id ORDER BY is_primary DESC, sort_order ASC",
            ['id' => (int)$id]
        );

        $variants = $db->fetchAll(
            "SELECT pv.id, pv.name, pvv.value, pvv.price_adjustment, pvv.quantity
             FROM product_variants pv
             LEFT JOIN product_variant_values pvv ON pv.id = pvv.variant_id
             WHERE pv.product_id = :id AND pv.is_active = 1",
            ['id' => (int)$id]
        );

        $reviews = $db->fetchAll(
            "SELECT r.id, r.rating, r.review, r.created_at, CONCAT(u.first_name, ' ', u.last_name) AS customer_name
             FROM reviews r
             LEFT JOIN users u ON r.user_id = u.id
             WHERE r.product_id = :id AND r.is_approved = 1
             ORDER BY r.created_at DESC LIMIT 20",
            ['id' => (int)$id]
        );

        $ratingSummary = $db->fetch(
            "SELECT ROUND(AVG(rating), 1) AS average, COUNT(*) AS total FROM reviews WHERE product_id = :id AND is_approved = 1",
            ['id' => (int)$id]
        );

        $this->renderJSON([
            'product' => [
                'id' => (int)$product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'description' => $product->description,
                'base_price' => (float)$product->base_price,
                'sale_price' => $product->sale_price ? (float)$product->sale_price : null,
                'quantity' => (int)$product->quantity,
                'sku' => $product->sku,
                'category' => $product->category_name,
                'store' => [
                    'id' => (int)$product->store_id,
                    'name' => $product->store_name,
                    'slug' => $product->store_slug,
                    'seller' => $product->seller_name,
                ],
                'images' => $images,
                'variants' => $variants,
                'reviews' => $reviews,
                'rating' => $ratingSummary ? (float)$ratingSummary->average : null,
                'total_reviews' => $ratingSummary ? (int)$ratingSummary->total : 0,
            ]
        ]);
    }

    public function categories()
    {
        $db = Database::getInstance();

        $categories = $db->fetchAll(
            "SELECT c.id, c.name, c.slug, c.icon,
                    (SELECT COUNT(*) FROM products WHERE category_id = c.id AND is_active = 1 AND is_approved = 1) AS product_count
             FROM categories c
             WHERE c.is_active = 1
             ORDER BY c.name ASC"
        );

        $this->renderJSON($categories);
    }

    public function stores()
    {
        $db = Database::getInstance();

        $stores = $db->fetchAll(
            "SELECT s.id, s.store_name, s.slug, s.description, s.logo, s.banner,
                    s.city, s.state,
                    (SELECT COUNT(*) FROM products WHERE store_id = s.id AND is_active = 1 AND is_approved = 1) AS product_count
             FROM stores s
             WHERE s.is_verified = 1 AND s.is_active = 1
             ORDER BY s.store_name ASC"
        );

        $this->renderJSON($stores);
    }

    public function searchSuggestions()
    {
        $query = trim($_GET['q'] ?? '');
        if (strlen($query) < 2) {
            $this->renderJSON(['products' => [], 'categories' => []]);
            return;
        }

        $db = Database::getInstance();
        $like = '%' . $query . '%';

        $products = $db->fetchAll(
            "SELECT p.id, p.name, p.slug, COALESCE(p.sale_price, p.base_price) as price,
                    (SELECT image FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as image
             FROM products p
             WHERE p.is_active = 1 AND p.is_approved = 1 AND p.name LIKE :q
             LIMIT 5",
            ['q' => $like]
        );

        $categories = $db->fetchAll(
            "SELECT id, name, slug FROM categories WHERE is_active = 1 AND (name LIKE :q OR description LIKE :q2) LIMIT 3",
            ['q' => $like, 'q2' => $like]
        );

        $this->renderJSON([
            'products' => $products,
            'categories' => $categories,
            'query' => $query,
        ]);
    }
}
