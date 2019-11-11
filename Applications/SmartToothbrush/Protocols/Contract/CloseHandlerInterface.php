<?php

namespace Protocols\Contract;

interface CloseHandler
{
    public function onClose($data, $db);
}
