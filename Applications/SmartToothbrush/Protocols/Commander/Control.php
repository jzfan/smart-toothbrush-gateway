<?php

namespace Protocols\Commander;

class Control
{
    public function cmd()
    {
        return '3a';
    }

    public function length()
    {
        return '16';
    }

    public function data()
    {
        return '050101';
    }

    public function onReplyOk($data, $db)
    { }

    public function onReplyNg($data, $db)
    { }
}
