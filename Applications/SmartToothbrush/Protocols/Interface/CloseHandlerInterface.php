<?php

namespace Protocols\Interfaces;

interface CloseHandler
{
    public function onClose($data, $db);
}
