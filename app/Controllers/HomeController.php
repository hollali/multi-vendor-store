<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Models\Category;
use App\Models\Banner;
use App\Models\Product;
use App\Services\Geolocation;
use App\Services\Shipping;

class HomeController extends Controller
{
    public function index(): void
    {
        $db = Database::getInstance();
        $this->initGeo();

        $countryCode = $this->geo->getCountryCode();
        $countryData = $this->geo->getCountryData();
        $currencyData = $this->geo->getCurrencyData();

        $featuredProducts = $db->fetchAll(
            "SELECT p.*, s.store_name, s.slug as store_slug,
                    (SELECT image FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as primary_image,
                    (SELECT AVG(rating) FROM reviews WHERE product_id = p.id AND is_approved = 1) as avg_rating,
                    (SELECT COUNT(*) FROM reviews WHERE product_id = p.id AND is_approved = 1) as review_count
             FROM products p
             LEFT JOIN stores s ON p.store_id = s.id
             WHERE p.is_active = 1 AND p.is_approved = 1 AND p.is_featured = 1
             ORDER BY p.id DESC LIMIT 8"
        );

        $latestProducts = $db->fetchAll(
            "SELECT p.*, s.store_name, s.slug as store_slug,
                    (SELECT image FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as primary_image,
                    (SELECT AVG(rating) FROM reviews WHERE product_id = p.id AND is_approved = 1) as avg_rating,
                    (SELECT COUNT(*) FROM reviews WHERE product_id = p.id AND is_approved = 1) as review_count
             FROM products p
             LEFT JOIN stores s ON p.store_id = s.id
             WHERE p.is_active = 1 AND p.is_approved = 1
             ORDER BY p.id DESC LIMIT 12"
        );

        $categories = Category::where('is_active', 1)->orderBy('sort_order', 'ASC')->get();
        $banners = Banner::where('is_active', 1)->orderBy('sort_order', 'ASC')->get();

        $trendingProducts = $db->fetchAll(
            "SELECT p.*, s.store_name, s.slug as store_slug,
                    (SELECT image FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as primary_image,
                    (SELECT AVG(rating) FROM reviews WHERE product_id = p.id AND is_approved = 1) as avg_rating,
                    (SELECT COUNT(*) FROM reviews WHERE product_id = p.id AND is_approved = 1) as review_count
             FROM products p
             LEFT JOIN stores s ON p.store_id = s.id
             WHERE p.is_active = 1 AND p.is_approved = 1
             ORDER BY RAND() LIMIT 8"
        );

        $topStores = $db->fetchAll(
            "SELECT s.*,
                    (SELECT COUNT(*) FROM products WHERE store_id = s.id AND is_active = 1 AND is_approved = 1) as product_count,
                    (SELECT AVG(r.rating) FROM reviews r JOIN products p ON r.product_id = p.id WHERE p.store_id = s.id AND r.is_approved = 1) as avg_rating
             FROM stores s
             WHERE s.is_verified = 1 AND s.is_active = 1
             ORDER BY product_count DESC LIMIT 6"
        );

        $this->renderView('shop/home', [
            'featuredProducts' => $featuredProducts,
            'latestProducts' => $latestProducts,
            'categories' => $categories,
            'banners' => $banners,
            'trendingProducts' => $trendingProducts,
            'topStores' => $topStores,
            'countryCode' => $countryCode,
            'countryName' => $countryData->name ?? 'Ghana',
        ]);
    }
}
