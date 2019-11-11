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
