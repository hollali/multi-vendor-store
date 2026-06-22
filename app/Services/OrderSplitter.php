<?php
namespace App\Services;

use App\Core\Database;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\VendorOrder;

class OrderSplitter
{
    private Database $db;
    private Shipping $shipping;
    private Geolocation $geo;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->shipping = Shipping::getInstance();
        $this->geo = Geolocation::getInstance();
    }

    public function execute(int $parentOrderId, array $items, string $countryCode, float $totalDiscount = 0, ?string $couponCode = null): array
    {
        $grouped = [];
        foreach ($items as $item) {
            $vendorId = (int)$item->vendor_id;
            if (!isset($grouped[$vendorId])) {
                $grouped[$vendorId] = [
                    'vendor_id' => $vendorId,
                    'store_id' => (int)($item->store_id ?? 0),
                    'store_name' => $item->store_name ?? '',
                    'items' => [],
                    'subtotal' => 0,
                    'total_weight' => 0,
                ];
            }
            $itemTotal = (float)$item->unit_price * (int)$item->quantity;
            $grouped[$vendorId]['items'][] = $item;
            $grouped[$vendorId]['subtotal'] += $itemTotal;
            $grouped[$vendorId]['total_weight'] += (float)($item->weight_kg ?? 0) * (int)$item->quantity;
        }

        $vendorOrders = [];
        $vendorCount = count($grouped);
        $discountPerVendor = $vendorCount > 0 ? round($totalDiscount / $vendorCount, 2) : 0;

        foreach ($grouped as $vendorId => $group) {
            $subtotal = $group['subtotal'];

            $shippingInfo = $this->shipping->getShippingRate(
                $vendorId,
                $countryCode,
                $subtotal,
                $group['total_weight']
            );

            $shippingCost = $shippingInfo['available'] ? $shippingInfo['rate'] : 0;
            $tax = round($subtotal * 0.025, 2);
            $discount = min($discountPerVendor, $subtotal);
            $total = max(0, $subtotal - $discount) + $shippingCost + $tax;

            $subOrderNumber = Order::generateOrderNumber() . '-' . $vendorId;

            $vOrderId = $this->db->insert('vendor_orders', [
                'parent_order_id' => $parentOrderId,
                'vendor_id' => $vendorId,
                'store_id' => $group['store_id'] ?: null,
                'order_number' => $subOrderNumber,
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'tax' => $tax,
                'discount' => $discount,
                'total' => $total,
                'status' => 'pending',
                'shipping_method' => $shippingInfo['available'] ? $shippingInfo['zone_name'] : 'Unknown',
                'estimated_delivery_min' => $shippingInfo['estimated_days_min'] ?? null,
                'estimated_delivery_max' => $shippingInfo['estimated_days_max'] ?? null,
            ]);

            foreach ($group['items'] as $item) {
                $this->db->update('order_items', [
                    'vendor_order_id' => $vOrderId,
                ], 'id = :id', ['id' => $item->id]);
            }

            $vendorOrders[] = [
                'id' => $vOrderId,
                'vendor_id' => $vendorId,
                'store_name' => $group['store_name'],
                'order_number' => $subOrderNumber,
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'tax' => $tax,
                'discount' => $discount,
                'total' => $total,
                'status' => 'pending',
                'shipping_method' => $shippingInfo['available'] ? $shippingInfo['zone_name'] : 'Unknown',
                'estimated_delivery_min' => $shippingInfo['estimated_days_min'] ?? null,
                'estimated_delivery_max' => $shippingInfo['estimated_days_max'] ?? null,
                'shipping_info' => $shippingInfo,
            ];
        }

        return $vendorOrders;
    }
}
