<?php
namespace App\Services;

use App\Core\Database;
use App\Core\Session;

class Geolocation
{
    private static ?Geolocation $instance = null;
    private Session $session;
    private ?array $countryData = null;
    private ?array $currencyData = null;

    private function __construct()
    {
        $this->session = Session::getInstance();
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getCountryCode(): string
    {
        if ($this->session->has('geo_country_code')) {
            return $this->session->get('geo_country_code');
        }

        $detected = $this->detectFromIp();
        $countryCode = $detected ?: 'GH';
        $this->session->set('geo_country_code', $countryCode);
        return $countryCode;
    }

    public function setCountryCode(string $code): void
    {
        $this->session->set('geo_country_code', strtoupper($code));
        $this->session->remove('geo_country_data');
        $this->session->remove('geo_currency_code');
        $this->countryData = null;
        $this->currencyData = null;

        if ($this->session->isAuthenticated()) {
            $userId = $this->session->getUserId();
            $db = Database::getInstance();
            $existing = $db->fetch("SELECT id FROM user_preferences WHERE user_id = :uid", ['uid' => $userId]);
            if ($existing) {
                $db->update('user_preferences', ['country_code' => strtoupper($code)], 'id = :id', ['id' => $existing->id]);
            } else {
                $db->insert('user_preferences', ['user_id' => $userId, 'country_code' => strtoupper($code)]);
            }
        }
    }

    public function getCountryData(): ?object
    {
        if ($this->countryData === null) {
            $code = $this->getCountryCode();
            $db = Database::getInstance();
            $data = $db->fetch("SELECT * FROM countries WHERE code = :code AND is_active = 1", ['code' => $code]);
            $this->countryData = $data ? (array)$data : null;
        }
        return $this->countryData ? (object)$this->countryData : null;
    }

    public function getCurrencyCode(): string
    {
        if ($this->session->has('geo_currency_code')) {
            return $this->session->get('geo_currency_code');
        }
        $country = $this->getCountryData();
        $code = $country->currency_code ?? 'GHS';
        $this->session->set('geo_currency_code', $code);
        return $code;
    }

    public function setCurrencyCode(string $code): void
    {
        $this->session->set('geo_currency_code', strtoupper($code));
        $this->currencyData = null;
    }

    public function getCurrencyData(): ?object
    {
        if ($this->currencyData === null) {
            $code = $this->getCurrencyCode();
            $db = Database::getInstance();
            $data = $db->fetch("SELECT * FROM currencies WHERE code = :code AND is_active = 1", ['code' => $code]);
            $this->currencyData = $data ? (array)$data : null;
        }
        return $this->currencyData ? (object)$this->currencyData : null;
    }

    public function getAllCountries(): array
    {
        $db = Database::getInstance();
        return $db->fetchAll("SELECT * FROM countries WHERE is_active = 1 ORDER BY sort_order ASC, name ASC");
    }

    public function getAllCurrencies(): array
    {
        $db = Database::getInstance();
        return $db->fetchAll("SELECT * FROM currencies WHERE is_active = 1 ORDER BY code ASC");
    }

    public function convertPrice(float $amountInGhs, ?string $toCurrency = null): float
    {
        $currency = $toCurrency ?: $this->getCurrencyCode();
        if ($currency === 'GHS') return $amountInGhs;

        $db = Database::getInstance();
        $rate = $db->fetch(
            "SELECT exchange_rate FROM currencies WHERE code = :code AND is_active = 1",
            ['code' => $currency]
        );

        if (!$rate || (float)$rate->exchange_rate <= 0) return $amountInGhs;
        return round($amountInGhs * (float)$rate->exchange_rate, 2);
    }

    public function formatPrice(float $amountInGhs, ?string $toCurrency = null): string
    {
        $currency = $toCurrency ?: $this->getCurrencyCode();
        $converted = $this->convertPrice($amountInGhs, $currency);

        $db = Database::getInstance();
        $currData = $db->fetch("SELECT symbol, decimal_places FROM currencies WHERE code = :code", ['code' => $currency]);

        $symbol = $currData->symbol ?? '$';
        $decimals = (int)($currData->decimal_places ?? 2);

        return $symbol . number_format($converted, $decimals);
    }

    public function getShippingZoneForCountry(string $countryCode): ?object
    {
        $db = Database::getInstance();
        $zones = $db->fetchAll("SELECT * FROM shipping_zones WHERE is_active = 1 ORDER BY type ASC");
        foreach ($zones as $zone) {
            $countries = array_map('trim', explode(',', $zone->countries ?? ''));
            if (in_array(strtoupper($countryCode), $countries)) {
                return $zone;
            }
        }
        return null;
    }

    private function detectFromIp(): ?string
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        if ($ip === '127.0.0.1' || $ip === '::1') return 'GH';

        $ip = $this->getClientIp();
        $apiKey = getenv('IPAPI_KEY') ?: '';

        $url = $apiKey
            ? "https://ipapi.co/{$ip}/country_code/?key={$apiKey}"
            : "https://ipapi.co/{$ip}/country_code/";

        $context = stream_context_create(['http' => ['timeout' => 3]]);
        $code = @file_get_contents($url, false, $context);

        if ($code && strlen(trim($code)) === 2) {
            return strtoupper(trim($code));
        }

        $url2 = "http://ip-api.com/json/{$ip}?fields=countryCode";
        $response = @file_get_contents($url2, false, $context);
        if ($response) {
            $data = json_decode($response);
            if ($data && isset($data->countryCode) && strlen($data->countryCode) === 2) {
                return strtoupper($data->countryCode);
            }
        }

        return 'GH';
    }

    private function getClientIp(): string
    {
        $headers = ['HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'HTTP_CLIENT_IP', 'REMOTE_ADDR'];
        foreach ($headers as $h) {
            if (!empty($_SERVER[$h])) {
                $ips = explode(',', $_SERVER[$h]);
                $ip = trim($ips[0]);
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }
        return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    }
}
