<?php
namespace App\Services;

use App\Core\Database;

class Shipping
{
    private static ?Shipping $instance = null;
    private Geolocation $geo;

    private function __construct()
    {
        $this->geo = Geolocation::getInstance();
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getShippingRate(int $vendorId, string $countryCode, float $subtotal, float $totalWeight = 0): array
    {
        $db = Database::getInstance();

        $zone = $this->geo->getShippingZoneForCountry($countryCode);
        if (!$zone) {
            return [
                'available' => false,
                'rate' => 0,
                'estimated_days_min' => null,
                'estimated_days_max' => null,
                'message' => 'Shipping not available to this country',
                'free_shipping' => false,
            ];
        }

        $rate = $db->fetch(
            "SELECT * FROM vendor_shipping_rates WHERE vendor_id = :vid AND zone_id = :zid AND is_active = 1",
            ['vid' => $vendorId, 'zid' => $zone->id]
        );

        if (!$rate) {
            return [
                'available' => false,
                'rate' => 0,
                'estimated_days_min' => null,
                'estimated_days_max' => null,
                'message' => 'This vendor does not ship to your country',
                'free_shipping' => false,
            ];
        }

        $freeShippingMin = (float)($rate->free_shipping_min ?? 0);
        $isFree = $freeShippingMin > 0 && $subtotal >= $freeShippingMin;

        $shippingCost = $isFree ? 0 : ((float)$rate->base_rate + (float)$rate->rate_per_kg * max(0, $totalWeight));

        $product = $db->fetch(
            "SELECT MIN(COALESCE(sale_price, base_price)) as min_price FROM products WHERE vendor_id = :vid AND is_active = 1 AND is_approved = 1 AND free_shipping = 1",
            ['vid' => $vendorId]
        );

        return [
            'available' => true,
            'rate' => round($shippingCost, 2),
            'zone_name' => $zone->name,
            'zone_type' => $zone->type,
            'estimated_days_min' => (int)($rate->estimated_days_min ?? 3),
            'estimated_days_max' => (int)($rate->estimated_days_max ?? 7),
            'free_shipping_min' => $freeShippingMin,
            'free_shipping' => $isFree,
            'message' => $isFree ? 'Free Shipping' : 'Shipping fee applies',
        ];
    }

    public function getVendorShippingRates(int $vendorId): array
    {
        $db = Database::getInstance();
        return $db->fetchAll(
            "SELECT vsr.*, sz.name as zone_name, sz.type as zone_type, sz.countries
             FROM vendor_shipping_rates vsr
             JOIN shipping_zones sz ON vsr.zone_id = sz.id
             WHERE vsr.vendor_id = :vid AND vsr.is_active = 1 AND sz.is_active = 1
             ORDER BY sz.type ASC",
            ['vid' => $vendorId]
        );
    }

    public function saveVendorShippingRate(int $vendorId, int $zoneId, array $data): int
    {
        $db = Database::getInstance();

        $existing = $db->fetch(
            "SELECT id FROM vendor_shipping_rates WHERE vendor_id = :vid AND zone_id = :zid",
            ['vid' => $vendorId, 'zid' => $zoneId]
        );

        $rateData = [
            'base_rate' => (float)($data['base_rate'] ?? 0),
            'rate_per_kg' => (float)($data['rate_per_kg'] ?? 0),
            'free_shipping_min' => !empty($data['free_shipping_min']) ? (float)$data['free_shipping_min'] : null,
            'estimated_days_min' => (int)($data['estimated_days_min'] ?? 3),
            'estimated_days_max' => (int)($data['estimated_days_max'] ?? 7),
            'is_active' => isset($data['is_active']) ? (bool)$data['is_active'] : true,
        ];

        if ($existing) {
            $db->update('vendor_shipping_rates', $rateData, 'id = :id', ['id' => $existing->id]);
            return $existing->id;
        }

        $rateData['vendor_id'] = $vendorId;
        $rateData['zone_id'] = $zoneId;
        return $db->insert('vendor_shipping_rates', $rateData);
    }

    public function getShippingZones(): array
    {
        $db = Database::getInstance();
        return $db->fetchAll("SELECT * FROM shipping_zones WHERE is_active = 1 ORDER BY type ASC, name ASC");
    }

    public function estimateDelivery(int $vendorId, string $countryCode): array
    {
        $db = Database::getInstance();
        $zone = $this->geo->getShippingZoneForCountry($countryCode);
        if (!$zone) {
            return ['min' => null, 'max' => null, 'text' => 'Not available'];
        }

        $rate = $db->fetch(
            "SELECT estimated_days_min, estimated_days_max FROM vendor_shipping_rates WHERE vendor_id = :vid AND zone_id = :zid AND is_active = 1",
            ['vid' => $vendorId, 'zid' => $zone->id]
        );

        if (!$rate) {
            return ['min' => null, 'max' => null, 'text' => 'Not available'];
        }

        $min = (int)$rate->estimated_days_min;
        $max = (int)$rate->estimated_days_max;

        return [
            'min' => $min,
            'max' => $max,
            'text' => "{$min}-{$max} business days",
        ];
    }
}
