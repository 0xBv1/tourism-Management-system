<?php

namespace App\Traits;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

trait CurrencyConversion
{
    /**
     * Get currency conversion rates
     */
    protected function getCurrencyRates(): array
    {
        return Cache::remember('currency_rates', 3600, function () {
            try {
                // Using a free currency API (you can replace with your preferred service)
                $response = Http::get('https://api.exchangerate-api.com/v4/latest/USD');
                
                if ($response->successful()) {
                    return $response->json('rates', []);
                }
            } catch (\Exception $e) {
                \Log::error('Currency conversion API error: ' . $e->getMessage());
            }
            
            // Fallback rates (you should update these regularly)
            return [
                'USD' => 1.0,
                'EUR' => 0.85,
                'GBP' => 0.73,
                'JPY' => 110.0,
                'CAD' => 1.25,
                'AUD' => 1.35,
                'CHF' => 0.92,
                'CNY' => 6.45,
                'INR' => 74.0,
                'AED' => 3.67,
            ];
        });
    }

    /**
     * Convert amount from one currency to another
     */
    public function convertCurrency(float $amount, string $fromCurrency, string $toCurrency): float
    {
        if ($fromCurrency === $toCurrency) {
            return $amount;
        }

        $rates = $this->getCurrencyRates();
        
        // Convert to USD first if not already USD
        if ($fromCurrency !== 'USD') {
            $usdRate = $rates[$fromCurrency] ?? 1.0;
            $amountInUsd = $amount / $usdRate;
        } else {
            $amountInUsd = $amount;
        }
        
        // Convert from USD to target currency
        if ($toCurrency !== 'USD') {
            $targetRate = $rates[$toCurrency] ?? 1.0;
            return $amountInUsd * $targetRate;
        }
        
        return $amountInUsd;
    }

    /**
     * Format amount with currency symbol
     */
    public function formatCurrency(float $amount, string $currency): string
    {
        $symbols = [
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'JPY' => '¥',
            'CAD' => 'C$',
            'AUD' => 'A$',
            'CHF' => 'CHF',
            'CNY' => '¥',
            'INR' => '₹',
            'AED' => 'د.إ',
        ];

        $symbol = $symbols[$currency] ?? $currency . ' ';
        $formattedAmount = number_format($amount, 2);
        
        return $symbol . $formattedAmount;
    }

    /**
     * Get supported currencies
     */
    public function getSupportedCurrencies(): array
    {
        return [
            'USD' => 'US Dollar',
            'EUR' => 'Euro',
            'GBP' => 'British Pound',
            'JPY' => 'Japanese Yen',
            'CAD' => 'Canadian Dollar',
            'AUD' => 'Australian Dollar',
            'CHF' => 'Swiss Franc',
            'CNY' => 'Chinese Yuan',
            'INR' => 'Indian Rupee',
            'AED' => 'UAE Dirham',
        ];
    }

    /**
     * Get currency symbol
     */
    public function getCurrencySymbol(string $currency): string
    {
        $symbols = [
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'JPY' => '¥',
            'CAD' => 'C$',
            'AUD' => 'A$',
            'CHF' => 'CHF',
            'CNY' => '¥',
            'INR' => '₹',
            'AED' => 'د.إ',
        ];

        return $symbols[$currency] ?? $currency;
    }

    /**
     * Calculate exchange rate between two currencies
     */
    public function getExchangeRate(string $fromCurrency, string $toCurrency): float
    {
        if ($fromCurrency === $toCurrency) {
            return 1.0;
        }

        $rates = $this->getCurrencyRates();
        
        $fromRate = $rates[$fromCurrency] ?? 1.0;
        $toRate = $rates[$toCurrency] ?? 1.0;
        
        return $toRate / $fromRate;
    }

    /**
     * Convert and format amount
     */
    public function convertAndFormat(float $amount, string $fromCurrency, string $toCurrency): string
    {
        $convertedAmount = $this->convertCurrency($amount, $fromCurrency, $toCurrency);
        return $this->formatCurrency($convertedAmount, $toCurrency);
    }
}
