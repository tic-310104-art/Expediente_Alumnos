<?php

namespace App\Helpers;

class PeriodoHelper
{
    const PERIODOS = ['ENE-ABR', 'MAY-AGO', 'SEP-DIC'];

    public static function getCurrentPeriodName(): string
    {
        $month = (int) date('n');
        $year = (int) date('Y');

        if ($month <= 4) return "ENE-ABR {$year}";
        if ($month <= 8) return "MAY-AGO {$year}";
        return "SEP-DIC {$year}";
    }

    public static function getCurrentPeriodIndex(): int
    {
        $month = (int) date('n');
        if ($month <= 4) return 0;
        if ($month <= 8) return 1;
        return 2;
    }

    public static function getPeriodName(int $year, int $index): string
    {
        return self::PERIODOS[$index] . " {$year}";
    }

    public static function getPeriodOptions(int $yearsBack = 3): array
    {
        $currentYear = (int) date('Y');
        $currentPeriod = self::getCurrentPeriodIndex();
        $options = [];

        for ($y = $currentYear - $yearsBack; $y <= $currentYear; $y++) {
            $max = ($y === $currentYear) ? $currentPeriod : 2;
            for ($p = 0; $p <= $max; $p++) {
                $options[] = self::getPeriodName($y, $p);
            }
        }

        return array_reverse($options);
    }

    public static function extractCuatrimestreFromGroup(?string $grupo): ?int
    {
        if ($grupo === null || $grupo === '') return null;

        $len = strlen($grupo);
        if ($len === 0) return null;

        if ($len >= 3) {
            $prefix = substr($grupo, 0, 2);
            if ($prefix === '10') return 10;
            if ($prefix === '11') return 11;
        }

        $first = (int) $grupo[0];
        if ($first >= 1 && $first <= 9) return $first;

        return null;
    }

    public static function getPeriodOptionsForCuatrimestre(?int $cuatrimestre): array
    {
        if ($cuatrimestre === null || $cuatrimestre < 1) {
            return self::getPeriodOptions();
        }

        $maxPastPeriods = min($cuatrimestre + 1, 12);
        return self::getPeriodOptionsFromCount($maxPastPeriods);
    }

    private static function getPeriodOptionsFromCount(int $maxCount): array
    {
        $currentYear = (int) date('Y');
        $currentPeriod = self::getCurrentPeriodIndex();
        $options = [];
        $count = 0;

        for ($y = $currentYear; $y >= $currentYear - 3 && $count < $maxCount; $y--) {
            $max = ($y === $currentYear) ? $currentPeriod : 2;
            for ($p = $max; $p >= 0 && $count < $maxCount; $p--) {
                $options[] = self::getPeriodName($y, $p);
                $count++;
            }
        }

        return $options;
    }
}
