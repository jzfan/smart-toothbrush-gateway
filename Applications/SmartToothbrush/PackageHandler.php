<?php

use Protocols\Utils;

class PackageHandler
{
    static function handleMessage($client_id, $data)
    {
        if ($data['code'] === Utils::BOOT_DATA) {
            $_SESSION['mac'] = $data['mac'];
            $_SESSION['position'] = $data['position'];
            $_SESSION['press'] = $data['press'];
        }
    }

    static function handleClose($client_id, $db)
    {
        LoggerServer::log(Utils::INFO, "client:{" . $client_id . "} closing...\n");
        if (self::checkSession(['mac', 'press', 'position'])) {
            self::saveBootDate($db);
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

    static function saveBootDate($db)
    {
        $db->insert('hh_user_toothbrushing_boot')->cols([
            'user_id' => 1,
            'press' => intval($_SESSION['press']),
            'position' => intval($_SESSION['position']),
            'mac' => $_SESSION['mac'],
            'add_time' => time()
        ])->query();
    }
}
