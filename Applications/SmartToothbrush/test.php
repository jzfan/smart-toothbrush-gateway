<?php

use Protocols\PackageHandlerFactory;
use Protocols\Utils;

<<<<<<< HEAD
require_once __DIR__ . '/../../vendor/autoload.php';

// $data = 'aa0109143938443836333739433845380619';
// echo crc16($data) . "\n";

function testSetDate()
{
    $sender = PackageHandlerFactory::getSender(Utils::SET_DATE);
    // $sender->send('12345', null);
}

testSetDate();
=======
use Protocols\Package;

function testCrc() {
    $data = 'aa0109143938443836333739433845380619';
    echo crc16($data) . "\n";
    echo PHP_EOL;
}

function testEncodeMac() {
    $mac = '98D86352E032';
    echo unpack('H*', $mac)[1];
    echo PHP_EOL;
}

testEncodeMac();

function testEncode() {
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

>>>>>>> f0dfc3fb023d3b0a54cf7983e1eafc3df1a83b48
