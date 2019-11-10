<?php

namespace Protocols;

use Protocols\Utils;

class PackageHandler
{
    static function handleMessage($client_id, $data, $db)
    {
        $type = PackageTypeFactory::getType($data['code']);
        $type->handleData($data, $db);
    }

    static function handleClose($client_id, $db)
    {
        \LoggerServer::log(Utils::INFO, "client:{" . $client_id . "} closing...\n");
        if (self::checkSession(['mac', 'result'])) {
            self::saveResult($db);
        }
    }

    static function checkSession($arr)
    {
        foreach ($arr as $key) {
            if (!isset($_SESSION[$key]) || $_SESSION[$key] === null) {
                return false;
            }
        }
        return true;
    }

    static function saveResult($db)
    {
        $db->insert('hh_toothbrushing_result')->cols([
            'result' => $_SESSION['result'],
            'mac' => $_SESSION['mac'],
            'points' => 100,
            'add_time' => time()
        ])->query();
    }
}
