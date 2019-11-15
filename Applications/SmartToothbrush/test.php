<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Services\ToothbrushingService;

$result = '000100000200000300000400090500000600000700000800000900000a00000b00000c00000d00000e00000f00001000';

$p40 = ToothbrushingService::getPoints($result);

var_export($p40);
