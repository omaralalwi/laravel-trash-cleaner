<?php

namespace Omaralalwi\LaravelTrashCleaner\Support;

use InvalidArgumentException;

final class Bytes
{
    private const UNITS = ['B', 'KB', 'MB', 'GB', 'TB'];

    public static function format(int $bytes, int $precision = 2): string
    {
        if ($bytes <= 0) {
            return '0 B';
        }

        $power = min((int) floor(log($bytes, 1024)), count(self::UNITS) - 1);

        return round($bytes / (1024 ** $power), $precision).' '.self::UNITS[$power];
    }

    /**
     * Parse a size such as "1048576", "500K", "1M" or "2G" (binary units) into bytes.
     */
    public static function parse(string $size): int
    {
        if (! preg_match('/^\s*(\d+)\s*([KMG])?B?\s*$/i', $size, $matches)) {
            throw new InvalidArgumentException("Invalid size [{$size}]. Use bytes or a K, M or G suffix, e.g. 1M.");
        }

        $exponent = ['' => 0, 'K' => 1, 'M' => 2, 'G' => 3][strtoupper($matches[2] ?? '')];

        return (int) $matches[1] * (1024 ** $exponent);
    }
}
