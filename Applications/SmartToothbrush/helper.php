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
