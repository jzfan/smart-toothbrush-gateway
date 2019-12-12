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
    ];

    const SENDERS = [
        Utils::CONTROL => 'Control',
        Utils::SERVER_OK => 'Ok',
        Utils::SET_DATE => 'SetDate'
    ];

    public static function getReceiver($index)
    {
if (self::RECEIVERS[$index] === null) {
            return new \Protocols\Receiver\Fallback;
        }
        $class = self::RECEIVERS[$index];
        $class = '\\Protocols\\Receiver\\' . $class;
        return new $class;
    }

    public static function getSender($index)
    {
        if (self::SENDERS[$index] === null) {
            return new \Protocols\Sender\Fallback;
        }
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
