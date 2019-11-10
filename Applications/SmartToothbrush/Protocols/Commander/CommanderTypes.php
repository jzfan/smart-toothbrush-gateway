<?php

namespace Protocols\Commander;

abstract class CommanderTypes
{

    abstract public function cmd();

    abstract public function onReplyOk($data, $db);

    abstract public function onReplyNg($data, $db);
}
