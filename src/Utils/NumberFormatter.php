<?php

namespace Utils;

class NumberFormatter
{
    public static function format($value, $decimals = 1, $language = 'en')
    {
        $num = floatval($value);

        if ($language === 'de') {
            // German formatting: 1.000,0
            return number_format($num, $decimals, ',', '.');
        } else {
            // English formatting: 1,000.0
            return number_format($num, $decimals, '.', ',');
        }
    }
}