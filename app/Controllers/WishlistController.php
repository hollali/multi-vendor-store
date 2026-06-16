<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Middleware;
use App\Models\Wishlist;

class WishlistController extends Controller
{
    public function index(): void
    {
        Middleware::auth();

        $userId = $this->session->getUserId();
        $items = Wishlist::getUserWishlist($userId);

        $this->renderView('customer/wishlist', [
            'items' => $items,
        ]);
    }

    public function toggle(): void
    {
        Middleware::auth();

        $userId = $this->session->getUserId();
        $productId = (int)$this->getParam('product_id', 0);

        if ($productId <= 0) {
            if ($this->isAjax()) {
                $this->renderJSON(['success' => false, 'message' => 'Invalid product.'], 400);
            }
            $this->redirectWith('/wishlist', 'Invalid product.', 'error');
            return;
        }

        $added = Wishlist::toggle($userId, $productId);
        $message = $added ? 'Added to wishlist.' : 'Removed from wishlist.';

        if ($this->isAjax()) {
            $this->renderJSON([
                'success' => true,
                'message' => $message,
                'in_wishlist' => $added,
            ]);
        }

        $this->redirectWith('/wishlist', $message, $added ? 'success' : 'info');
    }

    private function isAjax(): bool
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
}
