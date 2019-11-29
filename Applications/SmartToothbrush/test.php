<?php

use Protocols\PackageHandlerFactory;
use Protocols\Utils;
use Protocols\Package;
use Protocols\Receiver\Result2;

require_once __DIR__ . '/../../vendor/autoload.php';

function testCrc()
{
    $data = 'aa0109143938443836333739433845380619';
    echo crc16($data) . "\n";
    echo PHP_EOL;
}

function testEncodeMac()
{
    $mac = '98D86352E032';
    echo unpack('H*', $mac)[1];
    echo PHP_EOL;
}

// testEncodeMac();

function testEncode()
{
    $data = [
        'code' => 'cc',
        'length' => 99,
        'mac' => '98D86352E032'
    ];
    $str = Package::encode($data);
    echo $str;
    echo PHP_EOL;
}

// testEncode();

function testResult2()
{
    $r = new Result2();
    echo $r->getDecodeRule();
}

testResult2();
