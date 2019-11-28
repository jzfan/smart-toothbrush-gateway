<?php

namespace Protocols\Sender;

abstract class SenderTypes
{
    abstract public function send($mac, $data);
}
