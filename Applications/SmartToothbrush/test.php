<?php

use Protocols\PackageHandlerFactory;
use Protocols\Utils;

require_once __DIR__ . '/../../vendor/autoload.php';

// $data = 'aa0109143938443836333739433845380619';
// echo crc16($data) . "\n";

function testSetDate()
{
    $sender = PackageHandlerFactory::getSender(Utils::SET_DATE);
    // $sender->send('12345', null);
}

testSetDate();
