<?php

namespace Protocols\Sender;

class Control extends SenderTypes
{
    public function cmd()
    {
        return '3a';
    }

    public function length()
    {
        return '16';
    }

    public function data($message)
    {
        return '050101';
    }

    public function send($data, $mac)
    { }
}
