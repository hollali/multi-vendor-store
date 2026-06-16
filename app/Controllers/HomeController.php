<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Models\Category;
use App\Models\Banner;

class HomeController extends Controller
{
    public function index(): void
    {
        $db = Database::getInstance();

        $featuredProducts = $db->fetchAll(
            "SELECT * FROM products WHERE is_active = 1 AND is_approved = 1 AND is_featured = 1 ORDER BY id DESC LIMIT 8"
        );

        $latestProducts = $db->fetchAll(
            "SELECT * FROM products WHERE is_active = 1 AND is_approved = 1 ORDER BY id DESC LIMIT 12"
        );

        $categories = Category::where('is_active', 1)->orderBy('sort_order', 'ASC')->get();

        $banners = Banner::where('is_active', 1)->orderBy('sort_order', 'ASC')->get();

        $trendingProducts = $db->fetchAll(
            "SELECT * FROM products WHERE is_active = 1 AND is_approved = 1 ORDER BY id DESC LIMIT 8"
        );

        $this->renderView('shop/home', [
            'featuredProducts' => $featuredProducts,
            'latestProducts' => $latestProducts,
            'categories' => $categories,
            'banners' => $banners,
            'trendingProducts' => $trendingProducts,
        ]);
    }
}
