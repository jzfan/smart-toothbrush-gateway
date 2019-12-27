<?php

namespace Protocols;

use Protocols\Utils;

class PackageHandlerFactory
{
    const RECEIVERS = [
        Utils::RESULT => 'Result2',
        Utils::BOOT_DATA => 'Boot',
        Utils::QUERY_DATE => 'QueryDate',
        Utils::CLIENT_TYPE => 'ClientType',
        Utils::CLIENT_CONFIG => 'ClientConfig',
        Utils::CLIENT_STATUS => 'ClientStatus',
        Utils::CLIENT_OK => 'OK',
    ];

    const SENDERS = [
        Utils::CONTROL => 'Control',
        Utils::SERVER_OK => 'Ok',
        Utils::SET_DATE => 'SetDate'
    ];

    public static function getReceiver($index)
    {
        // if (!isset(self::RECEIVERS[$index])) {
        //     return new \Protocols\Receiver\Fallback;
        // }
        $class = self::RECEIVERS[$index];
        $class = '\\Protocols\\Receiver\\' . $class;
        return new $class;
    }

    public static function getSender($index)
    {
        $class = self::SENDERS[$index];
        $class = '\\Protocols\\Sender\\' . $class;
        return new $class;
    }

    public static function all($instance = false)
    {
        $arr = [];
        foreach (self::RECEIVERS as $class) {
            $class = '\\Protocols\\Receiver\\' . $class;
            $arr[] = new $class;
        }
        foreach (self::SENDERS as $class) {
            $class = '\\Protocols\\Sender\\' . $class;
            $arr[] = new $class;
        }
        if ($instance !== false) {
            $instance = '\\Protocols\\Contract\\' . $instance;
            return array_filter($arr, function ($class) use ($instance) {
                return $class instanceof $instance;
            });
        }
        return $arr;
    }
}
