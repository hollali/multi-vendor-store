<?php
namespace App\Services;

use App\Core\Database;

class Currency
{
    private static ?Currency $instance = null;
    private ?array $rates = null;
    private ?string $baseCurrency = null;
    private ?array $formatters = [];

    private function __construct()
    {
        $this->loadRates();
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getBaseCurrency(): string
    {
        if ($this->baseCurrency === null) {
            $db = Database::getInstance();
            $base = $db->fetch("SELECT code FROM currencies WHERE is_base = 1 AND is_active = 1 LIMIT 1");
            $this->baseCurrency = $base->code ?? 'GHS';
        }
        return $this->baseCurrency;
    }

    public function convert(float $amount, string $from, string $to): float
    {
        if ($from === $to) return round($amount, 2);

        $rates = $this->getRates();
        $fromRate = $rates[$from] ?? 1;
        $toRate = $rates[$to] ?? 1;

        if ($toRate <= 0) return round($amount, 2);

        $amountInBase = $amount / $fromRate;
        return round($amountInBase * $toRate, 2);
    }

    public function format(float $amount, string $currencyCode): string
    {
        $cacheKey = $currencyCode;
        if (isset($this->formatters[$cacheKey])) {
            return $this->formatters[$cacheKey]($amount);
        }

        $db = Database::getInstance();
        $currency = $db->fetch(
            "SELECT symbol, decimal_places, code FROM currencies WHERE code = :code AND is_active = 1",
            ['code' => $currencyCode]
        );

        if (!$currency) {
            $formatter = fn($a) => '$' . number_format($a, 2);
            $this->formatters[$cacheKey] = $formatter;
            return $formatter($amount);
        }

        $symbol = $currency->symbol;
        $decimals = (int)$currency->decimal_places;

        $formatter = fn($a) => $symbol . number_format($a, $decimals);
        $this->formatters[$cacheKey] = $formatter;
        return $formatter($amount);
    }

    public function formatFromGHS(float $amountInGhs, string $toCurrency): string
    {
        $converted = $this->convert($amountInGhs, $this->getBaseCurrency(), $toCurrency);
        return $this->format($converted, $toCurrency);
    }

    public function getAllCurrencies(): array
    {
        $db = Database::getInstance();
        return $db->fetchAll("SELECT * FROM currencies WHERE is_active = 1 ORDER BY code ASC");
    }

    public function getRates(): array
    {
        if ($this->rates === null) {
            $this->loadRates();
        }
        return $this->rates;
    }

    public function getRate(string $currencyCode): float
    {
        $rates = $this->getRates();
        return (float)($rates[$currencyCode] ?? 1.0);
    }

    public function updateRate(string $currencyCode, float $newRate): void
    {
        $db = Database::getInstance();
        $db->update('currencies', ['exchange_rate' => $newRate], 'code = :code', ['code' => strtoupper($currencyCode)]);
        $this->loadRates(true);
    }

    private function loadRates(bool $force = false): void
    {
        if ($this->rates !== null && !$force) return;

        $db = Database::getInstance();
        $currencies = $db->fetchAll("SELECT code, exchange_rate FROM currencies WHERE is_active = 1");

        $this->rates = [];
        foreach ($currencies as $c) {
            $this->rates[$c->code] = (float)$c->exchange_rate;
            if ($c->is_base ?? false) {
                $this->baseCurrency = $c->code;
            }
        }
    }
}
