<?php

function hasSession($keys)
{
    foreach ($keys as $key) {
        if (!isset($_SESSION[$key]) || !$_SESSION[$key]) {
            return false;
        }
    }
    return true;
}

function dump($info, $arr = null)
{
    if ($arr === null) {
        var_export($info . ' : ' . "\n");
    }
    if (is_array($arr)) {
        $arr = json_encode($arr);
    }
    var_export($info . ' : ' . $arr . "\n");
}

function crc16($msg)
{
    $data = pack('H*', $msg);
    $crc = 0xFFFF;
    $leng = strlen($data);
    for ($i = 0; $i < $leng; $i++) {
        $crc ^= ord($data[$i]);

        for ($j = 8; $j != 0; $j--) {
            if (($crc & 0x0001) != 0) {
                $crc >>= 1;
                $crc ^= 0xA001;
            } else $crc >>= 1;
        }
    }
    return sprintf('%04x', $crc);
}
