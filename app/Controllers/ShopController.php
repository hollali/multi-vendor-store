<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Store;
use App\Models\Product as ProductModel;
use App\Services\Shipping;

class ShopController extends Controller
{
    public function index(): void
    {
        $db = Database::getInstance();
        $this->initGeo();

        $page = max(1, (int)$this->getParam('page', 1));
        $perPage = 12;
        $offset = ($page - 1) * $perPage;

        $where = "WHERE p.is_active = 1 AND p.is_approved = 1";
        $params = [];

        $categorySlug = $this->getParam('category', '');
        if ($categorySlug) {
            $category = Category::findBy('slug', $categorySlug);
            if ($category) {
                $where .= " AND (p.category_id = :category_id";
                $childIds = $db->fetchAll(
                    "SELECT id FROM categories WHERE parent_id = :pid AND is_active = 1",
                    ['pid' => $category->id]
                );
                if (!empty($childIds)) {
                    $ids = array_column($childIds, 'id');
                    $where .= " OR p.category_id IN (" . implode(',', $ids) . ")";
                }
                $where .= ")";
                $params['category_id'] = $category->id;
            }
        }

        $brandSlug = $this->getParam('brand', '');
        if ($brandSlug) {
            $brand = Brand::findBy('slug', $brandSlug);
            if ($brand) {
                $where .= " AND p.brand_id = :brand_id";
                $params['brand_id'] = $brand->id;
            }
        }

        $minPrice = $this->getParam('min_price', '');
        if ($minPrice !== '' && is_numeric($minPrice)) {
            $where .= " AND COALESCE(p.sale_price, p.base_price) >= :min_price";
            $params['min_price'] = (float)$minPrice;
        }

        $maxPrice = $this->getParam('max_price', '');
        if ($maxPrice !== '' && is_numeric($maxPrice)) {
            $where .= " AND COALESCE(p.sale_price, p.base_price) <= :max_price";
            $params['max_price'] = (float)$maxPrice;
        }

        $sort = $this->getParam('sort', 'newest');
        $orderBy = match ($sort) {
            'price_asc' => 'COALESCE(p.sale_price, p.base_price) ASC',
            'price_desc' => 'COALESCE(p.sale_price, p.base_price) DESC',
            default => 'p.id DESC',
        };

        $search = $this->getParam('q', '');
        if ($search) {
            $like = '%' . $search . '%';
            $where .= " AND (p.name LIKE :search OR p.description LIKE :search2)";
            $params['search'] = $like;
            $params['search2'] = $like;
        }

        $countResult = $db->fetch(
            "SELECT COUNT(*) as total FROM products p {$where}",
            $params
        );
        $total = (int)($countResult->total ?? 0);
        $lastPage = max(1, ceil($total / $perPage));
        $page = min($page, $lastPage);
        $offset = ($page - 1) * $perPage;

        $products = $db->fetchAll(
            "SELECT p.*, s.store_name, s.slug as store_slug,
                    (SELECT AVG(r.rating) FROM reviews r WHERE r.product_id = p.id AND r.is_approved = 1) as avg_rating,
                    (SELECT COUNT(*) FROM reviews r WHERE r.product_id = p.id AND r.is_approved = 1) as review_count,
                    (SELECT image FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as primary_image
             FROM products p
             LEFT JOIN stores s ON p.store_id = s.id
             {$where}
             ORDER BY {$orderBy}
             LIMIT {$perPage} OFFSET {$offset}",
            $params
        );

        $categories = Category::where('is_active', 1)->orderBy('name', 'ASC')->get();
        $brands = Brand::where('is_active', 1)->orderBy('name', 'ASC')->get();

        $priceRange = $db->fetch(
            "SELECT MIN(COALESCE(sale_price, base_price)) as min_price, MAX(COALESCE(sale_price, base_price)) as max_price FROM products WHERE is_active = 1 AND is_approved = 1"
        );

        $this->renderView('shop/index', [
            'products' => $products,
            'categories' => $categories,
            'brands' => $brands,
            'currentPage' => $page,
            'perPage' => $perPage,
            'total' => $total,
            'lastPage' => $lastPage,
            'sort' => $sort,
            'category' => $categorySlug,
            'brand' => $brandSlug,
            'minPrice' => $minPrice,
            'maxPrice' => $maxPrice,
            'search' => $search,
            'priceRange' => $priceRange,
        ]);
    }

    public function category(string $slug): void
    {
        $db = Database::getInstance();
        $this->initGeo();

        $category = Category::findBy('slug', $slug);
        if (!$category || !$category->is_active) {
            $this->redirectWith('/', 'Category not found.', 'error');
            return;
        }

        $childCategories = $db->fetchAll(
            "SELECT * FROM categories WHERE parent_id = :parent_id AND is_active = 1 ORDER BY sort_order ASC",
            ['parent_id' => $category->id]
        );

        $categoryIds = [$category->id];
        foreach ($childCategories as $child) {
            $categoryIds[] = (int)$child->id;
        }
        $idPlaceholder = implode(',', $categoryIds);

        $page = max(1, (int)$this->getParam('page', 1));
        $perPage = 12;
        $offset = ($page - 1) * $perPage;

        $countResult = $db->fetch(
            "SELECT COUNT(*) as total FROM products WHERE category_id IN ({$idPlaceholder}) AND is_active = 1 AND is_approved = 1"
        );
        $total = (int)($countResult->total ?? 0);
        $lastPage = max(1, ceil($total / $perPage));
        $page = min($page, $lastPage);
        $offset = ($page - 1) * $perPage;

        $products = $db->fetchAll(
            "SELECT p.*, s.store_name, s.slug as store_slug,
                    (SELECT AVG(r.rating) FROM reviews r WHERE r.product_id = p.id AND r.is_approved = 1) as avg_rating,
                    (SELECT COUNT(*) FROM reviews r WHERE r.product_id = p.id AND r.is_approved = 1) as review_count,
                    (SELECT image FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as primary_image
             FROM products p
             LEFT JOIN stores s ON p.store_id = s.id
             WHERE p.category_id IN ({$idPlaceholder}) AND p.is_active = 1 AND p.is_approved = 1
             ORDER BY p.id DESC
             LIMIT {$perPage} OFFSET {$offset}"
        );

        $this->renderView('shop/category', [
            'category' => $category,
            'childCategories' => $childCategories,
            'products' => $products,
            'currentPage' => $page,
            'perPage' => $perPage,
            'total' => $total,
            'lastPage' => $lastPage,
        ]);
    }

    public function search(): void
    {
        $query = trim($this->getParam('q', ''));
        if (empty($query)) {
            $this->redirect('/shop');
            return;
        }

        $db = Database::getInstance();
        $this->initGeo();

        $like = '%' . $query . '%';
        $page = max(1, (int)$this->getParam('page', 1));
        $perPage = 12;
        $offset = ($page - 1) * $perPage;

        $countResult = $db->fetch(
            "SELECT COUNT(*) as total FROM products WHERE is_active = 1 AND is_approved = 1 AND (name LIKE :q OR description LIKE :q2)",
            ['q' => $like, 'q2' => $like]
        );
        $total = (int)($countResult->total ?? 0);
        $lastPage = max(1, ceil($total / $perPage));
        $page = min($page, $lastPage);
        $offset = ($page - 1) * $perPage;

        $products = $db->fetchAll(
            "SELECT p.*, s.store_name, s.slug as store_slug,
                    (SELECT AVG(r.rating) FROM reviews r WHERE r.product_id = p.id AND r.is_approved = 1) as avg_rating,
                    (SELECT COUNT(*) FROM reviews r WHERE r.product_id = p.id AND r.is_approved = 1) as review_count,
                    (SELECT image FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as primary_image
             FROM products p
             LEFT JOIN stores s ON p.store_id = s.id
             WHERE p.is_active = 1 AND p.is_approved = 1 AND (p.name LIKE :q OR p.description LIKE :q2)
             ORDER BY p.id DESC
             LIMIT {$perPage} OFFSET {$offset}",
            ['q' => $like, 'q2' => $like]
        );

        $this->renderView('shop/search', [
            'products' => $products,
            'query' => $query,
            'currentPage' => $page,
            'perPage' => $perPage,
            'total' => $total,
            'lastPage' => $lastPage,
        ]);
    }

    public function show(string $slug): void
    {
        $db = Database::getInstance();
        $this->initGeo();

        $product = $db->fetch(
            "SELECT p.*, s.store_name, s.slug as store_slug, s.is_verified as store_verified, s.vendor_id
             FROM products p
             LEFT JOIN stores s ON p.store_id = s.id
             WHERE p.slug = :slug AND p.is_active = 1 AND p.is_approved = 1",
            ['slug' => $slug]
        );

        if (!$product) {
            $this->redirectWith('/', 'Product not found.', 'error');
            return;
        }

        $images = $db->fetchAll(
            "SELECT * FROM product_images WHERE product_id = :product_id ORDER BY sort_order ASC, is_primary DESC",
            ['product_id' => $product->id]
        );

        $variants = $db->fetchAll(
            "SELECT pv.* FROM product_variants pv
             WHERE pv.product_id = :product_id AND pv.is_active = 1",
            ['product_id' => $product->id]
        );

        foreach ($variants as $variant) {
            $variant->values = $db->fetchAll(
                "SELECT * FROM product_variant_values WHERE variant_id = :variant_id ORDER BY sort_order ASC",
                ['variant_id' => $variant->id]
            );
        }

        $reviews = $db->fetchAll(
            "SELECT r.*, CONCAT(u.first_name, ' ', u.last_name) as user_name, u.avatar as user_avatar
             FROM reviews r
             LEFT JOIN users u ON r.user_id = u.id
             WHERE r.product_id = :product_id AND r.is_approved = 1
             ORDER BY r.id DESC LIMIT 20",
            ['product_id' => $product->id]
        );

        $ratingSummary = $db->fetch(
            "SELECT AVG(rating) as avg_rating, COUNT(*) as total_reviews,
                    SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END) as five_star,
                    SUM(CASE WHEN rating = 4 THEN 1 ELSE 0 END) as four_star,
                    SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) as three_star,
                    SUM(CASE WHEN rating = 2 THEN 1 ELSE 0 END) as two_star,
                    SUM(CASE WHEN rating = 1 THEN 1 ELSE 0 END) as one_star
             FROM reviews WHERE product_id = :product_id AND is_approved = 1",
            ['product_id' => $product->id]
        );

        $relatedProducts = $db->fetchAll(
            "SELECT p.*, s.store_name, s.slug as store_slug,
                    (SELECT image FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as primary_image
             FROM products p
             LEFT JOIN stores s ON p.store_id = s.id
             WHERE p.category_id = :category_id AND p.id != :product_id AND p.is_active = 1 AND p.is_approved = 1
             ORDER BY p.id DESC LIMIT 4",
            ['category_id' => $product->category_id, 'product_id' => $product->id]
        );

        $storeProducts = $db->fetchAll(
            "SELECT p.*, s.store_name, s.slug as store_slug,
                    (SELECT image FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as primary_image
             FROM products p
             LEFT JOIN stores s ON p.store_id = s.id
             WHERE p.store_id = :store_id AND p.id != :product_id AND p.is_active = 1 AND p.is_approved = 1
             ORDER BY p.id DESC LIMIT 4",
            ['store_id' => $product->store_id, 'product_id' => $product->id]
        );

        $category = $product->category_id ? Category::find($product->category_id) : null;

        $canReview = false;
        if ($this->session->isAuthenticated()) {
            $existing = $db->fetch(
                "SELECT id FROM reviews WHERE product_id = :product_id AND user_id = :user_id",
                ['product_id' => $product->id, 'user_id' => $this->session->getUserId()]
            );
            $canReview = !$existing;
        }

        $shippingService = Shipping::getInstance();
        $countryCode = $this->geo->getCountryCode();
        $productPrice = (float)($product->sale_price ?? $product->base_price ?? 0);
        $shippingInfo = $shippingService->getShippingRate(
            $product->vendor_id,
            $countryCode,
            $productPrice,
            (float)($product->weight_kg ?? 0)
        );

        $this->renderView('shop/show', [
            'product' => $product,
            'images' => $images,
            'variants' => $variants,
            'reviews' => $reviews,
            'ratingSummary' => $ratingSummary,
            'relatedProducts' => $relatedProducts,
            'storeProducts' => $storeProducts,
            'category' => $category,
            'canReview' => $canReview,
            'shippingInfo' => $shippingInfo,
            'countryCode' => $countryCode,
        ]);
    }

    public function store(string $slug): void
    {
        $db = Database::getInstance();
        $this->initGeo();

        $store = Store::findBy('slug', $slug);
        if (!$store || !$store->is_active) {
            $this->redirectWith('/', 'Store not found.', 'error');
            return;
        }

        $page = max(1, (int)$this->getParam('page', 1));
        $perPage = 12;
        $offset = ($page - 1) * $perPage;

        $countResult = $db->fetch(
            "SELECT COUNT(*) as total FROM products WHERE store_id = :store_id AND is_active = 1 AND is_approved = 1",
            ['store_id' => $store->id]
        );
        $total = (int)($countResult->total ?? 0);
        $lastPage = max(1, ceil($total / $perPage));
        $page = min($page, $lastPage);
        $offset = ($page - 1) * $perPage;

        $products = $db->fetchAll(
            "SELECT p.*,
                    (SELECT AVG(r.rating) FROM reviews r WHERE r.product_id = p.id AND r.is_approved = 1) as avg_rating,
                    (SELECT COUNT(*) FROM reviews r WHERE r.product_id = p.id AND r.is_approved = 1) as review_count,
                    (SELECT image FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as primary_image
             FROM products p
             WHERE p.store_id = :store_id AND p.is_active = 1 AND p.is_approved = 1
             ORDER BY p.id DESC
             LIMIT {$perPage} OFFSET {$offset}",
            ['store_id' => $store->id]
        );

        $storeRating = $db->fetch(
            "SELECT AVG(r.rating) as avg_rating, COUNT(*) as total_reviews
             FROM reviews r
             JOIN products p ON r.product_id = p.id
             WHERE p.store_id = :store_id AND r.is_approved = 1",
            ['store_id' => $store->id]
        );

        $shippingService = Shipping::getInstance();
        $vendorShippingRates = $shippingService->getVendorShippingRates($store->vendor_id);

        $this->renderView('shop/store', [
            'store' => $store,
            'products' => $products,
            'storeRating' => $storeRating,
            'vendorShippingRates' => $vendorShippingRates,
            'currentPage' => $page,
            'perPage' => $perPage,
            'total' => $total,
            'lastPage' => $lastPage,
        ]);
    }
}
