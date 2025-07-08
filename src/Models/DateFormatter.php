<?php

namespace Models;

class DateFormatter {
    public static function isValidGermanDate(string $dateStr): bool {
        if (!$dateStr) return false;
        $pattern = '/^(\d{2})\.(\d{2})\.(\d{4})$/';
        if (!preg_match($pattern, $dateStr, $matches)) return false;

        $day = (int)$matches[1];
        $month = (int)$matches[2];
        $year = (int)$matches[3];

        return checkdate($month, $day, $year);
    }

    public static function germanToIsoDate(string $germanDate): string {
        if (!$germanDate || !self::isValidGermanDate($germanDate)) return '';
        $parts = explode('.', $germanDate);
        return sprintf('%s-%s-%s', $parts[2], $parts[1], $parts[0]);
    }

    public static function isoToGermanDate(string $isoDate): string {
        if (!$isoDate) return '';
        
        // Try to parse the date
        $date = date_create($isoDate);
        if (!$date) {
            // Fallback to simple split if date parsing fails
            $parts = explode('-', $isoDate);
            if (count($parts) !== 3) return '';
            return sprintf('%s.%s.%s', $parts[2], $parts[1], $parts[0]);
        }
        
        return $date->format('d.m.Y');
    }

    public static function getCurrentGermanDate(): string {
        return date('d.m.Y');
    }

    public static function validateDateInput($input): bool {
        if ($input->value && !self::isValidGermanDate($input->value)) {
            return false;
        }
        return true;
    }
}
