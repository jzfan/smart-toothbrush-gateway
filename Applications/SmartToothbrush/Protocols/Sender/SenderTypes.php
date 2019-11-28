<?php

namespace Protocols\Sender;

abstract class SenderTypes
{
    abstract public function send($mac, $data);

    protected function getSeq($cmd)
    {
        if (in_array($cmd, ['09', '0a', '0b'])) {
            return $cmd;
        }
        $index = $cmd . 'ok';
        if (isset($_SESSION[$index])) {
            $_SESSION[$index] = $_SESSION[$index] + 1;
        } else {
            $_SESSION[$index] = 1;
        }
        return str_pad($_SESSION[$index], 2, '0', \STR_PAD_LEFT);
    }
}
