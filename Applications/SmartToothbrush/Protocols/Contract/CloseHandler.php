<?php

namespace Protocols\Contract;

interface CloseHandler
{
    public function handleClose($client_id, $db);
}
