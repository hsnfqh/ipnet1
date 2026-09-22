<?php

namespace App\Helpers;

class CurrencyHelper
{
    /**
     * Format an amount into compact Indonesian Rupiah format.
     * Values in Billions (>= 1 Miliar / 1.000.000.000) format to "Rp X M" or "Rp X,XX M"
     * Values in Millions (>= 1 Juta / 1.000.000) format to "Rp X Jt" or "Rp X,XX Jt"
     * Values in Thousands (>= 1 Ribu / 1.000) format to "Rp X Rb"
     * Values below 1 Ribu or 0 format to "Rp 0" / "Rp X"
     */
    public static function formatCompact(float|int|null $value, string $prefix = 'Rp '): string
    {
        if ($value === null || (float)$value == 0.0) {
            return $prefix . '0';
        }

        $val = (float) $value;
        $abs = abs($val);
        $sign = $val < 0 ? '-' : '';

        if ($abs >= 1_000_000_000) {
            $num = $abs / 1_000_000_000;
            $formatted = number_format($num, 2, ',', '.');
            if (str_ends_with($formatted, ',00')) {
                $formatted = substr($formatted, 0, -3);
            } elseif (str_contains($formatted, ',') && str_ends_with($formatted, '0')) {
                $formatted = rtrim($formatted, '0');
            }
            return $sign . $prefix . $formatted . ' M';
        }

        if ($abs >= 1_000_000) {
            $num = $abs / 1_000_000;
            $formatted = number_format($num, 2, ',', '.');
            if (str_ends_with($formatted, ',00')) {
                $formatted = substr($formatted, 0, -3);
            } elseif (str_contains($formatted, ',') && str_ends_with($formatted, '0')) {
                $formatted = rtrim($formatted, '0');
            }
            return $sign . $prefix . $formatted . ' Jt';
        }

        if ($abs >= 1_000) {
            $num = $abs / 1_000;
            $formatted = number_format($num, 1, ',', '.');
            if (str_ends_with($formatted, ',0')) {
                $formatted = substr($formatted, 0, -2);
            }
            return $sign . $prefix . $formatted . ' Rb';
        }

        return $sign . $prefix . number_format($abs, 0, ',', '.');
    }

    /**
     * Standard full Rupiah format with thousand separators (e.g. Rp 1.500.000.000).
     */
    public static function formatRupiah(float|int|null $value, string $prefix = 'Rp '): string
    {
        if ($value === null) {
            return $prefix . '0';
        }
        $val = (float) $value;
        $sign = $val < 0 ? '-' : '';
        return $sign . $prefix . number_format(abs($val), 0, ',', '.');
    }
}
