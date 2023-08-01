<?php

namespace Ameax\Datev;

class DatevHelpers
{
    public static function cleanXmlNamespace(string $namespace): string
    {
        return str_replace(['{', '}'], ['', ''], $namespace);
    }

    public static function formatAmount(?float $amount): ?string
    {
        if (! isset($amount)) {
            return null;
        }

        return number_format($amount, 2, '.', '');
    }

    public static function clearNullValues(array $data): array
    {
        foreach ($data as $key => $value) {
            if (! isset($value)) {
                unset($data[$key]);
            }
        }

        return $data;
    }
}
