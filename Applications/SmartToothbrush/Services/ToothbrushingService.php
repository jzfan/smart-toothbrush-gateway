<?php

namespace Services;

class ToothbrushingService
{
    const SECONDS = 2;
    const POINTS_40 = [
        1 => 1.47,
        2 => 2.98,
        3 => 2.86,
        4 => 1.05,
        5 => 3.44,
        6 => 1.52,
        7 => 3.03,
        8 => 2.81,
        9 => 1.83,
        10 => 3.24,
        11 => 3.22,
        12 => 0.96,
        13 => 3.35,
        14 => 1.78,
        15 => 3.29,
        16 => 3.17,
    ];
    const TIME = 120;
    const POINTS_60 = [
        5 => 0.1,
        10 => 0.2,
        20 => 0.3,
        30 => 0.4,
        999 => 0.5
    ];
    protected static $totalTime = 0;

    // 000100000200000300040400080500000600000700000800000900000a00000b00000c00000d00000e00000f00001000
    public static function getPoints($result)
    {
        $p40 = self::byEachLocationTime($result);
        $p60 = self::byTotalTime(self::$totalTime);
        var_export('p40 : ' . $p40 . "\n");
        var_export('p60 : ' . $p60 . "\n");
        $total = $p40 + $p60;
        var_export('total points: ' . $total . "\n");
        return $total;
    }

    public static function byEachLocationTime($result)
    {
        $locations = \str_split($result, 6); // [000100, 000200]
        $points = $seconds = [];
        foreach ($locations as  $location) {
            $locationResult = \str_split($location, 2); // [00, 01, 00] [seconds, num, void]
            $seconds[] = hexdec($locationResult[0]);
            $points[] = hexdec($locationResult[0]) > self::SECONDS ? self::POINTS_40[hexdec($locationResult[1])] : 0;
        }
        self::$totalTime = array_sum($seconds);
        return array_sum($points);
    }

    public static function byTotalTime($time)
    {
        if ($time >= 120) {
            return 60;
        }
        $short = self::TIME - $time;
        $total = [];
        $prev_key = 0;
        foreach (self::POINTS_60 as $key => $val) {
            if ($short > $key) {
                $total[] = ($key - $prev_key) * $val;
                $prev_key = $key;
                continue;
            }
            $total[] = ($short - $prev_key) * $val;
            break;
        }
        return 60 - array_sum($total);
    }
}
