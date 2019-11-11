<?php

namespace Protocols\Sender;

abstract class SenderTypes
{

    abstract public function cmd();
    abstract public function length();
    abstract public function data($message);

    abstract public function onReplyOk($data, $db);

    abstract public function onReplyNg($data, $db);
}
