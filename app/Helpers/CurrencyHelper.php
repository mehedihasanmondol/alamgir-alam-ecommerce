<?php

namespace App\Helpers;

use App\Models\SiteSetting;

/**
 * Currency Helper
 * 
 * Provides global currency symbol and formatting functions
 * 
 * @category Helpers
 * @package  App\Helpers
 * @created  2025-11-18
 */
class CurrencyHelper
{
    /**
     * Get currency symbol from settings
     *
     * @return string
     */
    public static function symbol(): string
    {
        return SiteSetting::get('currency_symbol', '$');
    }

    /**
     * Get currency code from settings
     *
     * @return string
     */
    public static function code(): string
    {
        return SiteSetting::get('currency_code', 'USD');
    }

    /**
     * Get currency position (before/after)
     *
     * @return string
     */
    public static function position(): string
    {
        return SiteSetting::get('currency_position', 'before');
    }

    /**
     * Format price with currency symbol
     *
     * @param float $amount
     * @param int $decimals
     * @return string
     */
    public static function format($amount, int $decimals = 2): string
    {
        $symbol = self::symbol();
        $position = self::position();
        $formatted = number_format((float)$amount, $decimals);

        if ($position === 'after') {
            return $formatted . $symbol;
        }

        return $symbol . $formatted;
    }

    /**
     * Format price without currency symbol (just number)
     *
     * @param float $amount
     * @param int $decimals
     * @return string
     */
    public static function formatNumber($amount, int $decimals = 2): string
    {
        return number_format((float)$amount, $decimals);
    }
}
