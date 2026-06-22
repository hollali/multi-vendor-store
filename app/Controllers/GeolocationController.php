<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Services\Geolocation;
use App\Models\Country;
use App\Models\Currency;

class GeolocationController extends Controller
{
    public function setCountry(): void
    {
        $code = strtoupper(trim($this->getParam('code', '')));
        if (strlen($code) !== 2) {
            $this->renderJSON(['success' => false, 'message' => 'Invalid country code']);
            return;
        }

        $country = Country::findByCode($code);
        if (!$country || !$country->is_active) {
            $this->renderJSON(['success' => false, 'message' => 'Country not found']);
            return;
        }

        $geo = Geolocation::getInstance();
        $geo->setCountryCode($code);

        $currencyCode = $country->currency_code ?? 'GHS';
        $currency = Currency::findByCode($currencyCode);

        $this->renderJSON([
            'success' => true,
            'country_code' => $code,
            'country_name' => $country->name,
            'currency_code' => $currencyCode,
            'currency_symbol' => $currency->symbol ?? 'GH₵',
            'message' => 'Country updated to ' . $country->name,
        ]);
    }

    public function setCurrency(): void
    {
        $code = strtoupper(trim($this->getParam('code', '')));
        $currency = Currency::findByCode($code);
        if (!$currency || !$currency->is_active) {
            $this->renderJSON(['success' => false, 'message' => 'Currency not supported']);
            return;
        }

        $geo = Geolocation::getInstance();
        $geo->setCurrencyCode($code);

        $this->renderJSON([
            'success' => true,
            'currency_code' => $code,
            'currency_symbol' => $currency->symbol,
            'message' => 'Currency changed to ' . $currency->name,
        ]);
    }

    public function getCountries(): void
    {
        $geo = Geolocation::getInstance();
        $countries = $geo->getAllCountries();
        $current = $geo->getCountryCode();

        $this->renderJSON([
            'success' => true,
            'countries' => $countries,
            'current' => $current,
        ]);
    }

    public function getCurrencies(): void
    {
        $geo = Geolocation::getInstance();
        $currencies = $geo->getAllCurrencies();
        $current = $geo->getCurrencyCode();

        $this->renderJSON([
            'success' => true,
            'currencies' => $currencies,
            'current' => $current,
        ]);
    }

    public function detect(): void
    {
        $geo = Geolocation::getInstance();
        $countryCode = $geo->getCountryCode();
        $currencyCode = $geo->getCurrencyCode();
        $country = $geo->getCountryData();
        $currency = $geo->getCurrencyData();

        $this->renderJSON([
            'success' => true,
            'country_code' => $countryCode,
            'country_name' => $country->name ?? 'Ghana',
            'currency_code' => $currencyCode,
            'currency_symbol' => $currency->symbol ?? 'GH₵',
        ]);
    }

    public function getShippingZones(): void
    {
        $svc = new \App\Services\Shipping();
        $zones = $svc->getShippingZones();
        $this->renderJSON(['success' => true, 'zones' => $zones]);
    }
}
