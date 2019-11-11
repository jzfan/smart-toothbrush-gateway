<?php

namespace Protocols\Sender;

use Protocols\Utils;
use GatewayWorker\Lib\Gateway;

class Ok extends SenderTypes
{
    public function send($cmd, $mac)
    {
        return Gateway::sendToCurrentClient([
            'seq' => $cmd,
            'length' => 12,
            'mac' => $mac,
            'code' => Utils::SERVER_OK
        ]);
    }
}
