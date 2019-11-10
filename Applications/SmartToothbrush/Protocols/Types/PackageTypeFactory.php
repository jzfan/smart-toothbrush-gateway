<?php

namespace Protocols\Types;

use Protocols\Utils;

class PackageTypeFactory
{
    const TYPES = [
        Utils::RESULT => 'Result',
        Utils::BOOT_DATA => 'Boot',
        Utils::QUERY_DATE => 'QueryDate',
        Utils::CLIENT_TYPE => 'ClientType',
        Utils::CLIENT_CONFIG => 'ClientConfig',
        Utils::CLIENT_STATUS => 'ClientStatus',
    ];

    public static function getType($index)
    {
        if (!isset(self::TYPES[$index])) {
            return new Fallback;
        }
        $class = self::TYPES[$index];
        $class = '\\Protocols\\Types\\' . $class;
        return new $class;
    }
}
